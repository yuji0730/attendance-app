{{-- @extends('layouts.app')

@section('title','勤怠一覧ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="attendance-container">
    <h2 class="heading">勤怠一覧</h2>

    <div class="month-controls">
        <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="nav-button">← 前月</a>
        <div class="current-month">
            <i class="fa-regular fa-calendar"></i>
            {{ $currentMonth->format('Y/m') }}
        </div>
        <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="nav-button">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daysInMonth as $date)
                @php
                    $attendance = $attendances->firstWhere('date', $date->format('Y-m-d'));
                    $restTotal = $attendance && $attendance->rests
                        ? $attendance->rests->sum(fn($r) => $r->end_time && $r->start_time
                            ? \Carbon\Carbon::parse($r->end_time)->diffInSeconds($r->start_time)
                            : 0)
                        : 0;
                    $workTotal = ($attendance && $attendance->clock_in && $attendance->clock_out)
                        ? \Carbon\Carbon::parse($attendance->clock_out)->diffInSeconds($attendance->clock_in) - $restTotal
                        : null;
                @endphp
                <tr>
                    <td>{{ $date->format('m/d(D)') }}</td>
                    <td>{{ $attendance && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                    <td>{{ $restTotal ? gmdate('H:i', $restTotal) : '-' }}</td>
                    <td>{{ $workTotal ? gmdate('H:i', $workTotal) : '-' }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', $attendance->id ?? -1) }}?date={{ $date->format('Y-m-d') }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection --}}


@extends('layouts.app')

@section('title','勤怠一覧ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="attendance-container">
    <h2 class="heading">勤怠一覧</h2>

    <div class="month-controls">
        <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="nav-button">← 前月</a>
        <div class="current-month">
            <i class="fa-regular fa-calendar"></i>
            {{ $currentMonth->format('Y/m') }}
        </div>
        <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="nav-button">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daysInMonth as $date)
                @php
                    $attendance = $attendances->get($date->format('Y-m-d'));

                    // 修正申請の有無と承認済みか確認
                    $modReq = $attendance->modificationRequest ?? null;
                    $isApprovedMod = $modReq && $modReq->status === 'approved';

                    // 休憩時間計算用の休憩コレクションを用意
                    if ($isApprovedMod) {
                        $rests = collect();
                        if ($modReq->rests) {
                            $restsArray = is_string($modReq->rests) ? json_decode($modReq->rests, true) : $modReq->rests;
                            foreach ($restsArray as $r) {
                                $rests->push((object)[
                                    'start_time' => $r['start_time'] ?? null,
                                    'end_time' => $r['end_time'] ?? null,
                                ]);
                            }
                        }
                        $clockIn = $modReq->clock_in;
                        $clockOut = $modReq->clock_out;
                        $remarks = $modReq->remarks;
                    } else {
                        $rests = $attendance ? $attendance->rests : collect();
                        $clockIn = $attendance->clock_in ?? null;
                        $clockOut = $attendance->clock_out ?? null;
                        $remarks = $attendance->remarks ?? '';
                    }

                    $restTotal = $rests->sum(fn($r) =>
                        $r->end_time && $r->start_time
                            ? \Carbon\Carbon::parse($r->end_time)->diffInSeconds($r->start_time)
                            : 0
                    );

                    $workTotal = ($clockIn && $clockOut)
                        ? \Carbon\Carbon::parse($clockOut)->diffInSeconds($clockIn) - $restTotal
                        : null;
                @endphp
                <tr>
                    <td>{{ $date->format('m/d(D)') }}</td>
                    <td>{{ $clockIn ? \Carbon\Carbon::parse($clockIn)->format('H:i') : '-' }}</td>
                    <td>{{ $clockOut ? \Carbon\Carbon::parse($clockOut)->format('H:i') : '-' }}</td>
                    <td>{{ $restTotal ? gmdate('H:i', $restTotal) : '-' }}</td>
                    <td>{{ $workTotal ? gmdate('H:i', $workTotal) : '-' }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', $attendance->id ?? -1) }}?date={{ $date->format('Y-m-d') }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
