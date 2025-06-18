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




    public function index(Request $request)
    {
        $user = auth()->user();
        $currentMonth = $request->input('month')
            ? Carbon::parse($request->input('month'))
            : now()->startOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ])
            ->get();
            // ->keyBy(function ($item) {
            //     return Carbon::parse($item->date)->format('Y-m-d'); // ここを追加
            // });

        $daysInMonth = collect();
        $start = $currentMonth->copy()->startOfMonth();
        $end = $currentMonth->copy()->endOfMonth();
        for ($date = $start; $date <= $end; $date->addDay()) {
            $daysInMonth->push($date->copy());
        }

        return view('index', compact('attendances', 'daysInMonth', 'currentMonth'));
    }

    public function detail($id)
    {
        // 1. IDが-1などの空データ用の場合は、リクエストから日付を取得
        if ($id == -1) {
            $date = request()->query('date');

            $attendance = new Attendance([
                'id' => null,
                'date' => $date,
                'clock_in' => null,
                'clock_out' => null,
                'rests' => collect(),
                'remarks' => '',
            ]);
            $modificationRequest = \App\Models\ModificationRequest::where('user_id', auth()->id())
                ->where('date', $date) // ここはmodification_requestsテーブルに日付カラムがある想定。もしなければ勤怠IDで紐付けしているので工夫が必要。
                ->latest()
                ->first();
            $status = $modificationRequest ? $modificationRequest->status : null;

        } else {
            $attendance = Attendance::with('rests', 'modificationRequest')->findOrFail($id);
            $date = $attendance->date;
            $status = optional($attendance->modificationRequest)->status;

        }

        // 2. ログインユーザーの名前
        $userName = auth()->user()->name;

        return view('detail', compact('attendance', 'userName', 'date','status'));
    }



}
