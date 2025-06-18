<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function showClock()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            $status = '勤務外';
        } elseif (!$attendance->clock_out) {
            // 出勤していてまだ退勤していない
            $latestRest = $attendance->rests()->latest()->first();

            if ($latestRest && !$latestRest->end_time) {
                $status = '休憩中';
            } else {
                $status = '出勤中';
            }
        } else {
            $status = '退勤済';
        }

        return view('attendance', compact('status'));
    }

    public function clockIn(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        // すでに出勤済みなら処理しない（2重登録防止）
        $alreadyClockedIn = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->exists();

        if (!$alreadyClockedIn) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'clock_in' => now()->format('H:i:s'), // 出勤時刻を現在時刻で記録
            ]);
        }

        return redirect()->route('attendance.show');
    }

    public function clockOut()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance && !$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now()->format('H:i:s'),
            ]);

            // return redirect()->route('attendance.show')->with('message', 'お疲れ様でした。');
        }

        return redirect()->route('attendance.show');
    }



    public function breakStart()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            // まだ休憩中でないときのみ、新しい休憩レコードを作成
            $latestRest = $attendance->rests()->latest()->first();
            if (!$latestRest || $latestRest->end_time) {
                $attendance->rests()->create([
                    'start_time' => now()->format('H:i:s'),
                ]);
            }
        }

        return redirect()->route('attendance.show');
    }

    public function breakEnd()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            $latestRest = $attendance->rests()->latest()->first();

            if ($latestRest && !$latestRest->end_time) {
                $latestRest->update([
                    'end_time' => now()->format('H:i:s'),
                ]);
            }
        }

        return redirect()->route('attendance.show');
    }




    // public function index(Request $request)
    // {
    //     $user = auth()->user();
    //     $currentMonth = $request->input('month')
    //         ? Carbon::parse($request->input('month'))
    //         : now()->startOfMonth();

    //     $attendances = Attendance::with('rests')
    //         ->where('user_id', $user->id)
    //         ->whereBetween('date', [
    //             $currentMonth->copy()->startOfMonth(),
    //             $currentMonth->copy()->endOfMonth()
    //         ])
    //         ->get();

    //     $daysInMonth = collect();
    //     $start = $currentMonth->copy()->startOfMonth();
    //     $end = $currentMonth->copy()->endOfMonth();
    //     for ($date = $start; $date <= $end; $date->addDay()) {
    //         $daysInMonth->push($date->copy());
    //     }

    //     return view('index', compact('attendances', 'daysInMonth', 'currentMonth'));
    // }

    public function index(Request $request)
    {
        $user = auth()->user();
        $currentMonth = $request->input('month')
            ? Carbon::parse($request->input('month'))
            : now()->startOfMonth();

        // 勤怠レコードを取得
        $attendances = Attendance::with(['rests', 'modificationRequest'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ])
            ->get();

        // 承認済み修正申請（attendanceが存在しない日分のみ）を取得
        $approvedModsWithoutAttendance = \App\Models\ModificationRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereBetween('date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ])
            ->whereDoesntHave('attendance') // これが存在しない勤怠
            ->get();

        // 仮の Attendance インスタンスを作成
        foreach ($approvedModsWithoutAttendance as $mod) {
            $virtualAttendance = new \App\Models\Attendance([
                'id' => null,
                'user_id' => $user->id,
                'date' => $mod->date,
                'clock_in' => $mod->clock_in,
                'clock_out' => $mod->clock_out,
                'remarks' => $mod->remarks,
            ]);
            // 関係を設定
            $virtualAttendance->setRelation('modificationRequest', $mod);
            $virtualAttendance->setRelation('rests', collect()); // 空のコレクションで初期化
            $attendances->push($virtualAttendance);
        }

        // 表示のため日付でグループ化
        $attendances = $attendances->keyBy('date');

        // 対象月の日付リスト
        $daysInMonth = collect();
        $start = $currentMonth->copy()->startOfMonth();
        $end = $currentMonth->copy()->endOfMonth();
        for ($date = $start; $date <= $end; $date->addDay()) {
            $daysInMonth->push($date->copy());
        }

        return view('index', compact('attendances', 'daysInMonth', 'currentMonth'));
    }



    // public function detail($id)
    // {
    //     if ($id == -1) {
    //         $date = request()->query('date');

    //         $attendance = new Attendance([
    //             'id' => null,
    //             'date' => $date,
    //             'clock_in' => null,
    //             'clock_out' => null,
    //             'rests' => collect(),
    //             'remarks' => '',
    //         ]);

    //         // 修正申請を取得
    //         $modificationRequest = \App\Models\ModificationRequest::with('user')
    //             ->where('user_id', auth()->id())
    //             ->where('date', $date)
    //             ->latest()
    //             ->first();

    //         if ($modificationRequest && in_array($modificationRequest->status, ['pending', 'approved'])) {
    //             $attendance->clock_in = $modificationRequest->clock_in;
    //             $attendance->clock_out = $modificationRequest->clock_out;
    //             $attendance->remarks = $modificationRequest->remarks;

    //             // restsの形式が文字列(JSON)か配列かを判別して処理
    //             $restsArray = [];

    //             if (is_string($modificationRequest->rests)) {
    //                 $decoded = json_decode($modificationRequest->rests, true);
    //                 $restsArray = is_array($decoded) ? $decoded : [];
    //             } elseif (is_array($modificationRequest->rests)) {
    //                 $restsArray = $modificationRequest->rests;
    //             }

    //             $restCollection = collect();
    //             foreach ($restsArray as $restData) {
    //                 $restCollection->push(new \App\Models\Rest([
    //                     'start_time' => $restData['start_time'] ?? null,
    //                     'end_time' => $restData['end_time'] ?? null,
    //                 ]));
    //             }

    //             $attendance->setRelation('rests', $restCollection);
    //         }
    //     } else {
    //         $attendance = Attendance::with('rests', 'modificationRequest.user')->findOrFail($id);

    //         $modificationRequest = $attendance->modificationRequest;

    //         if ($modificationRequest && in_array($modificationRequest->status, ['pending', 'approved'])) {
    //             $attendance->clock_in = $modificationRequest->clock_in ?? $attendance->clock_in;
    //             $attendance->clock_out = $modificationRequest->clock_out ?? $attendance->clock_out;
    //             $attendance->remarks = $modificationRequest->remarks ?? $attendance->remarks;

    //             if ($modificationRequest->rests) {
    //                 $restsArray = is_string($modificationRequest->rests)
    //                     ? json_decode($modificationRequest->rests, true)
    //                     : $modificationRequest->rests;

    //                 $newRests = collect();

    //                 foreach ($restsArray as $restData) {
    //                     $newRests->push((object)[
    //                         'start_time' => $restData['start_time'] ?? null,
    //                         'end_time' => $restData['end_time'] ?? null,
    //                     ]);
    //                 }

    //                 $attendance->rests = $newRests;
    //             }
    //         }
    //     }

    //     $userName = optional(optional($modificationRequest)->user)->name ?? auth()->user()->name;
    //     $date = $attendance->date;
    //     $status = optional($modificationRequest)->status;

    //     return view('detail', compact('attendance', 'userName', 'date', 'status'));
    // }


    public function detail($id)
    {
        $date = request()->query('date');
        $targetUserId = null;

        $isManager = auth()->user()->is_admin == 1;

        if ($isManager) {
            $targetUserId = request()->query('user_id');

            if (!$targetUserId || !is_numeric($targetUserId)) {
                abort(400, '管理者は正しいユーザーIDを指定してください。');
            }

            $targetUserId = (int) $targetUserId;
        } else {
            $targetUserId = auth()->id();
        }

        if ($id == -1) {
            $attendance = new Attendance([
                'id' => null,
                'date' => $date,
                'clock_in' => null,
                'clock_out' => null,
                'rests' => collect(),
                'remarks' => '',
            ]);

            $modificationRequest = \App\Models\ModificationRequest::with('user')
                ->where('user_id', $targetUserId)
                ->where('date', $date)
                ->latest()
                ->first();

            if ($modificationRequest && in_array($modificationRequest->status, ['pending', 'approved'])) {
                $attendance->clock_in = $modificationRequest->clock_in;
                $attendance->clock_out = $modificationRequest->clock_out;
                $attendance->remarks = $modificationRequest->remarks;

                $restsArray = [];

                if (is_string($modificationRequest->rests)) {
                    $decoded = json_decode($modificationRequest->rests, true);
                    $restsArray = is_array($decoded) ? $decoded : [];
                } elseif (is_array($modificationRequest->rests)) {
                    $restsArray = $modificationRequest->rests;
                }

                $restCollection = collect();
                foreach ($restsArray as $restData) {
                    $restCollection->push(new \App\Models\Rest([
                        'start_time' => $restData['start_time'] ?? null,
                        'end_time' => $restData['end_time'] ?? null,
                    ]));
                }

                $attendance->setRelation('rests', $restCollection);
            }

        } else {
            $attendance = Attendance::with('rests', 'modificationRequest.user', 'user')
                ->where('id', $id)
                ->where('user_id', $targetUserId)
                ->firstOrFail();

            $modificationRequest = $attendance->modificationRequest;

            if ($modificationRequest && in_array($modificationRequest->status, ['pending', 'approved'])) {
                $attendance->clock_in = $modificationRequest->clock_in ?? $attendance->clock_in;
                $attendance->clock_out = $modificationRequest->clock_out ?? $attendance->clock_out;
                $attendance->remarks = $modificationRequest->remarks ?? $attendance->remarks;

                if ($modificationRequest->rests) {
                    $restsArray = is_string($modificationRequest->rests)
                        ? json_decode($modificationRequest->rests, true)
                        : $modificationRequest->rests;

                    $newRests = collect();
                    foreach ($restsArray as $restData) {
                        $newRests->push((object)[
                            'start_time' => $restData['start_time'] ?? null,
                            'end_time' => $restData['end_time'] ?? null,
                        ]);
                    }

                    $attendance->rests = $newRests;
                }
            }
        }

        $user = \App\Models\User::find($targetUserId);
        $userName = $user ? $user->name : '不明なユーザー';
        $status = optional($modificationRequest)->status;

        return view('detail', compact('attendance', 'userName', 'date', 'status'));
    }





}
