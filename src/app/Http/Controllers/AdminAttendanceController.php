<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\ModificationRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\ModificationRequestStoreRequest;

class AdminAttendanceController extends Controller
{
    // public function index(Request $request)
    // {
    //     $date = $request->input('date', Carbon::today()->toDateString()); // 指定がなければ今日

    //     $users = User::where('is_admin', false)->get();

    //     $attendances = Attendance::with('rests')
    //     ->where('date', $date)
    //     ->get()
    //     ->keyBy('user_id');


    //     return view('admin.index', compact('users', 'attendances', 'date'));
    // }
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $users = User::where('is_admin', false)->get();

        $attendances = Attendance::with('rests')
            ->where('date', $date)
            ->get()
            ->keyBy('user_id');

        // 修正申請も取得（承認済みのみ）
        $modRequests = \App\Models\ModificationRequest::where('date', $date)
            ->where('status', 'approved')
            ->get()
            ->keyBy('user_id'); // user_idをキーにしておく

        return view('admin.index', compact('users', 'attendances', 'modRequests', 'date'));
    }

    public function store(ModificationRequestStoreRequest $request)
    {
        $validated = $request->validated();

        $date = $validated['date'];
        $clockIn = $validated['clock_in'] ? Carbon::parse($date . ' ' . $validated['clock_in']) : null;
        $clockOut = $validated['clock_out'] ? Carbon::parse($date . ' ' . $validated['clock_out']) : null;

        $attendance = Attendance::create([
            'user_id' => $validated['user_id'],
            'date' => $date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // 既存の休憩時間
        if (!empty($validated['rests']) && is_array($validated['rests'])) {
            foreach ($validated['rests'] as $rest) {
                if (isset($rest['start_time'], $rest['end_time'])) {
                    $attendance->rests()->create([
                        'start_time' => Carbon::parse($date . ' ' . $rest['start_time']),
                        'end_time' => Carbon::parse($date . ' ' . $rest['end_time']),
                    ]);
                }
            }
        }

        // 新しい休憩時間（new）
        if (isset($validated['rests']['new']['start_time'], $validated['rests']['new']['end_time'])) {
            $attendance->rests()->create([
                'start_time' => Carbon::parse($date . ' ' . $validated['rests']['new']['start_time']),
                'end_time' => Carbon::parse($date . ' ' . $validated['rests']['new']['end_time']),
            ]);
        }

        return redirect()->back()->with('success', '勤怠を新規登録しました。');
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'date' => 'required|date',
    //         'clock_in' => 'nullable|date_format:H:i',
    //         'clock_out' => 'nullable|date_format:H:i|after:clock_in',
    //         'rests' => 'nullable|array',
    //         'rests.*.start_time' => 'nullable|date_format:H:i',
    //         'rests.*.end_time' => 'nullable|date_format:H:i',
    //         'rests.new.start_time' => 'nullable|date_format:H:i',
    //         'rests.new.end_time' => 'nullable|date_format:H:i|after:rests.new.start_time',
    //         'remarks' => 'nullable|string|max:255',
    //     ]);

    //     $clockIn = $validated['clock_in'] ? Carbon::parse($validated['date'] . ' ' . $validated['clock_in']) : null;
    //     $clockOut = $validated['clock_out'] ? Carbon::parse($validated['date'] . ' ' . $validated['clock_out']) : null;

    //     $attendance = Attendance::create([
    //         'user_id' => $validated['user_id'],
    //         'date' => $validated['date'],
    //         'clock_in' => $clockIn,
    //         'clock_out' => $clockOut,
    //         'remarks' => $validated['remarks'] ?? null,
    //     ]);

    //     // 既存の休憩時間
    //     if (!empty($validated['rests']) && is_array($validated['rests'])) {
    //         foreach ($validated['rests'] as $rest) {
    //             if (isset($rest['start_time'], $rest['end_time'])) {
    //                 $attendance->rests()->create([
    //                     'start_time' => Carbon::parse($validated['date'] . ' ' . $rest['start_time']),
    //                     'end_time' => Carbon::parse($validated['date'] . ' ' . $rest['end_time']),
    //                 ]);
    //             }
    //         }
    //     }

    //     // 新しい休憩時間（new）
    //     if (isset($validated['rests']['new']['start_time'], $validated['rests']['new']['end_time'])) {
    //         $attendance->rests()->create([
    //             'start_time' => Carbon::parse($validated['date'] . ' ' . $validated['rests']['new']['start_time']),
    //             'end_time' => Carbon::parse($validated['date'] . ' ' . $validated['rests']['new']['end_time']),
    //         ]);
    //     }

    //     return redirect()->back()->with('success', '勤怠を新規登録しました。');
    // }

    public function update(ModificationRequestStoreRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $validated = $request->validated();

        $date = $attendance->date;

        $clockIn = $validated['clock_in'] ? Carbon::parse($date . ' ' . $validated['clock_in']) : null;
        $clockOut = $validated['clock_out'] ? Carbon::parse($date . ' ' . $validated['clock_out']) : null;

        $attendance->update([
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'remarks' => $validated['remarks'],
        ]);

        // 既存の休憩を全て削除して再登録
        $attendance->rests()->delete();

        // 既存の休憩時間
        if (!empty($validated['rests']) && is_array($validated['rests'])) {
            foreach ($validated['rests'] as $rest) {
                if (isset($rest['start_time'], $rest['end_time'])) {
                    $attendance->rests()->create([
                        'start_time' => Carbon::parse($date . ' ' . $rest['start_time']),
                        'end_time' => Carbon::parse($date . ' ' . $rest['end_time']),
                    ]);
                }
            }
        }

        // 新しい休憩時間（new）
        if (isset($validated['rests']['new']['start_time'], $validated['rests']['new']['end_time'])) {
            $attendance->rests()->create([
                'start_time' => Carbon::parse($date . ' ' . $validated['rests']['new']['start_time']),
                'end_time' => Carbon::parse($date . ' ' . $validated['rests']['new']['end_time']),
            ]);
        }

        return redirect()->back()->with('success', '勤怠を修正しました。');
    }

    // public function update(Request $request, $id)
    // {
    //     $attendance = Attendance::findOrFail($id);

    //     $validated = $request->validate([
    //         'clock_in' => 'nullable|date_format:H:i',
    //         'clock_out' => 'nullable|date_format:H:i|after:clock_in',
    //         'rests' => 'nullable|array',
    //         'rests.*.start_time' => 'nullable|date_format:H:i',
    //         'rests.*.end_time' => 'nullable|date_format:H:i',
    //         'rests.new.start_time' => 'nullable|date_format:H:i',
    //         'rests.new.end_time' => 'nullable|date_format:H:i|after:rests.new.start_time',
    //         'remarks' => 'nullable|string|max:255',
    //     ]);

    //     $date = $attendance->date;

    //     $clockIn = $validated['clock_in'] ? Carbon::parse($date . ' ' . $validated['clock_in']) : null;
    //     $clockOut = $validated['clock_out'] ? Carbon::parse($date . ' ' . $validated['clock_out']) : null;

    //     $attendance->update([
    //         'clock_in' => $clockIn,
    //         'clock_out' => $clockOut,
    //         'remarks' => $validated['remarks'] ?? null,
    //     ]);

    //     $attendance->rests()->delete();

    //     if (!empty($validated['rests']) && is_array($validated['rests'])) {
    //         foreach ($validated['rests'] as $rest) {
    //             if (isset($rest['start_time'], $rest['end_time'])) {
    //                 $attendance->rests()->create([
    //                     'start_time' => Carbon::parse($date . ' ' . $rest['start_time']),
    //                     'end_time' => Carbon::parse($date . ' ' . $rest['end_time']),
    //                 ]);
    //             }
    //         }
    //     }

    //     // 新しい休憩
    //     if (isset($validated['rests']['new']['start_time'], $validated['rests']['new']['end_time'])) {
    //         $attendance->rests()->create([
    //             'start_time' => Carbon::parse($date . ' ' . $validated['rests']['new']['start_time']),
    //             'end_time' => Carbon::parse($date . ' ' . $validated['rests']['new']['end_time']),
    //         ]);
    //     }

    //     return redirect()->back()->with('success', '勤怠を修正しました。');
    // }




    public function staff()
    {
        $staffs = User::where('is_admin', false)->get();

        return view('admin.staff', compact('staffs'));
    }

    public function attendance($id, Request $request)
    {
        $user = User::findOrFail($id);

        $currentMonth = $request->input('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))
            : Carbon::now()->startOfMonth();

        // 対象月の日付一覧
        $daysInMonth = collect();
        $start = $currentMonth->copy()->startOfMonth();
        $end = $currentMonth->copy()->endOfMonth();
        while ($start->lte($end)) {
            $daysInMonth->push($start->copy());
            $start->addDay();
        }

        // 勤怠・修正申請データ取得
        $rawAttendances = Attendance::with(['rests', 'modificationRequest' => function ($query) {
            $query->where('status', 'approved');
        }])
        ->where('user_id', $user->id)
        ->whereBetween('date', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
        ->get()
        ->keyBy('date');

        $modRequests = ModificationRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereBetween('date', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
            ->get()
            ->keyBy('date');

        // 日付ごとの勤怠＋修正申請をマージ
        $attendances = collect();
        foreach ($daysInMonth as $date) {
            $dateKey = $date->format('Y-m-d');
            $attendance = $rawAttendances->get($dateKey);
            $modRequest = $modRequests->get($dateKey);

            // 修正申請がある場合はその内容を優先して反映
            $clockIn = optional($modRequest)->clock_in ?? optional($attendance)->clock_in;
            $clockOut = optional($modRequest)->clock_out ?? optional($attendance)->clock_out;

            // 休憩情報（修正申請優先）
            $modRests = is_string(optional($modRequest)->rests) ? json_decode(optional($modRequest)->rests, true) : optional($modRequest)->rests;
            $rests = collect();
            if ($modRests) {
                $rests = collect($modRests);
            } elseif ($attendance && $attendance->rests) {
                $rests = $attendance->rests->map(function ($r) {
                    return [
                        'start_time' => $r->start_time,
                        'end_time' => $r->end_time,
                    ];
                });
            }

            $attendances->put($dateKey, (object)[
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'rests' => $rests,
            ]);
        }

        return view('admin.staff_attendance', compact('user', 'currentMonth', 'daysInMonth', 'attendances'));
    }


    // public function attendance($id, Request $request)
    // {
    //     $user = User::findOrFail($id);

    //     $currentMonth = $request->input('month')
    //         ? Carbon::createFromFormat('Y-m', $request->input('month'))
    //         : Carbon::now()->startOfMonth();

    //     $daysInMonth = collect();
    //     $start = $currentMonth->copy()->startOfMonth();
    //     $end = $currentMonth->copy()->endOfMonth();
    //     while ($start->lte($end)) {
    //         $daysInMonth->push($start->copy());
    //         $start->addDay();
    //     }

    //     $attendances = Attendance::with('rests')
    //         ->where('user_id', $user->id)
    //         ->whereBetween('date', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
    //         ->get();

    //     return view('admin.staff_attendance', compact('user', 'currentMonth', 'daysInMonth', 'attendances'));
    // }

    public function exportCsv($id, Request $request): StreamedResponse
    {
        $user = User::findOrFail($id);
        $month = $request->input('month') ?? Carbon::now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // 対象ユーザーの勤怠・修正申請を取得
        $attendances = Attendance::with('rests')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('date');

        $modRequests = ModificationRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('date');

        // 指定月の日付一覧（毎日1行表示したいため）
        $daysInMonth = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $daysInMonth[] = $day->format('Y-m-d');
        }

        // ファイル名
        $filename = $user->name . '_勤怠_' . $month . '.csv';

        // CSVヘッダー
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['日付', '出勤', '退勤', '休憩', '合計'];

        $callback = function () use ($daysInMonth, $attendances, $modRequests, $columns) {
            if (ob_get_level()) ob_end_clean();
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($daysInMonth as $date) {
                $attendance = $attendances->get($date);
                $mod = $modRequests->get($date);

                // 修正申請があればそちらを優先
                $clockIn = $mod && $mod->clock_in ? $mod->clock_in : optional($attendance)->clock_in;
                $clockOut = $mod && $mod->clock_out ? $mod->clock_out : optional($attendance)->clock_out;

                // 休憩情報も修正申請があればそちらを使用
                $rests = [];
                if ($mod && $mod->rests) {
                    $rests = is_string($mod->rests) ? json_decode($mod->rests, true) : $mod->rests;
                } elseif ($attendance && $attendance->rests) {
                    $rests = $attendance->rests->map(fn($r) => [
                        'start_time' => $r->start_time,
                        'end_time' => $r->end_time,
                    ])->toArray();
                }

                // 休憩時間合計（秒）
                $restTotalSeconds = 0;
                foreach ($rests as $rest) {
                    if (!empty($rest['start_time']) && !empty($rest['end_time'])) {
                        $start = Carbon::parse($rest['start_time']);
                        $end = Carbon::parse($rest['end_time']);
                        $diff = $end->diffInSeconds($start);
                        if ($diff > 0) $restTotalSeconds += $diff;
                    }
                }

                // 合計勤務時間（出勤〜退勤 - 休憩）
                $workTotalSeconds = null;
                if ($clockIn && $clockOut) {
                    $inTime = Carbon::parse($clockIn);
                    $outTime = Carbon::parse($clockOut);
                    $workTotalSeconds = $outTime->diffInSeconds($inTime) - $restTotalSeconds;
                    if ($workTotalSeconds < 0) $workTotalSeconds = 0;
                }

                // 書き込み
                fputcsv($handle, [
                    $date,
                    $clockIn ? Carbon::parse($clockIn)->format('H:i') : '-',
                    $clockOut ? Carbon::parse($clockOut)->format('H:i') : '-',
                    $restTotalSeconds ? gmdate('H:i', $restTotalSeconds) : '-',
                    $workTotalSeconds !== null ? gmdate('H:i', $workTotalSeconds) : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // public function exportCsv(User $user, Request $request): StreamedResponse
    // {
    //     $month = $request->input('month') ?? Carbon::now()->format('Y-m');
    //     $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //     $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    //     $attendances = Attendance::with('rests')
    //         ->where('user_id', $user->id)
    //         ->whereBetween('date', [$start, $end])
    //         ->get();

    //     $filename = $user->name . '_勤怠_' . $month . '.csv';

    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => "attachment; filename=\"$filename\"",
    //     ];

    //     $columns = ['日付', '出勤', '退勤', '休憩', '合計'];

    //     $callback = function () use ($attendances, $columns) {
    //         $handle = fopen('php://output', 'w');
    //         fputcsv($handle, $columns);

    //         foreach ($attendances as $attendance) {
    //             $restTotal = $attendance->rests->sum(function ($r) {
    //                 return $r->end_time && $r->start_time
    //                     ? Carbon::parse($r->end_time)->diffInSeconds($r->start_time)
    //                     : 0;
    //             });

    //             $workTotal = $attendance->clock_in && $attendance->clock_out
    //                 ? Carbon::parse($attendance->clock_out)->diffInSeconds($attendance->clock_in) - $restTotal
    //                 : null;

    //             fputcsv($handle, [
    //                 $attendance->date,
    //                 $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '-',
    //                 $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '-',
    //                 $restTotal ? gmdate('H:i', $restTotal) : '-',
    //                 $workTotal ? gmdate('H:i', $workTotal) : '-',
    //             ]);
    //         }

    //         fclose($handle);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

}
