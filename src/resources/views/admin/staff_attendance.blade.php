{{-- @extends('layouts.app')

@section('title', 'スタッフ別勤怠一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_attendance.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="attendance-container">
    <h2 class="heading">{{ $user->name }}さんの勤怠</h2>

    <div class="month-controls">
        <a href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="nav-button">← 前月</a>
        <div class="current-month">
            <i class="fa-regular fa-calendar"></i>
            {{ $currentMonth->format('Y/m') }}
        </div>
        <a href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="nav-button">翌月 →</a>
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
                        <a href="{{ route('attendance.detail', ['id' => -1]) }}?date={{ $date->format('Y-m-d') }}&user_id={{ $user->id }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-button-container">
        <a href="{{ route('admin.attendance.staff.export', ['id' => $user->id, 'month' => $currentMonth->format('Y-m')]) }}" class="csv-button">CSV出力</a>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('title', 'スタッフ別勤怠一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_attendance.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="attendance-container">
    <h2 class="heading">{{ $user->name }}さんの勤怠</h2>

    <div class="month-controls">
        <a href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="nav-button">← 前月</a>
        <div class="current-month">
            <i class="fa-regular fa-calendar"></i>
            {{ $currentMonth->format('Y/m') }}
        </div>
        <a href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="nav-button">翌月 →</a>
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
                    $dateKey = $date->format('Y-m-d');
                    $attendance = $attendances->get($dateKey);

                    $clockIn = optional($attendance)->clock_in;
                    $clockOut = optional($attendance)->clock_out;

                    $rests = collect(optional($attendance)->rests ?? []);
                    $restTotal = $rests->sum(function ($rest) {
                        return isset($rest['start_time'], $rest['end_time'])
                            ? \Carbon\Carbon::parse($rest['end_time'])->diffInSeconds($rest['start_time'])
                            : 0;
                    });

                    $workTotal = ($clockIn && $clockOut)
                        ? \Carbon\Carbon::parse($clockOut)->diffInSeconds(\Carbon\Carbon::parse($clockIn)) - $restTotal
                        : null;
                @endphp

                <tr>
                    <td>{{ $date->format('m/d(D)') }}</td>
                    <td>{{ $clockIn ? \Carbon\Carbon::parse($clockIn)->format('H:i') : '-' }}</td>
                    <td>{{ $clockOut ? \Carbon\Carbon::parse($clockOut)->format('H:i') : '-' }}</td>
                    <td>{{ $restTotal ? gmdate('H:i', $restTotal) : '-' }}</td>
                    <td>{{ $workTotal !== null ? gmdate('H:i', $workTotal) : '-' }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', ['id' => -1]) }}?date={{ $date->format('Y-m-d') }}&user_id={{ $user->id }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-button-container">
        <a href="{{ route('admin.attendance.staff.export', ['id' => $user->id, 'month' => $currentMonth->format('Y-m')]) }}" class="csv-button">CSV出力</a>
    </div>
</div>
@endsection
