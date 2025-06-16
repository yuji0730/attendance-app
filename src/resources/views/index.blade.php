{{-- @extends('layouts.app')

@section('title','勤怠一覧ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection


@section('content')
@include('components.header')

<div class="attendance-container">
    <h2 class="month-heading">{{ $currentMonth->format('Y年m月') }}</h2>

    <div class="month-navigation">
        <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="btn-nav">前月</a>
        <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="btn-nav">翌月</a>
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
                    $restTotal = 0;
                        if ($attendance && $attendance->rests) {
                            $restTotal = $attendance->rests->sum(function ($rest) {
                                return $rest->end_time && $rest->start_time
                                    ? \Carbon\Carbon::parse($rest->end_time)->diffInSeconds($rest->start_time)
                                    : 0;
                            });
                        }
                    $workTotal = null;
                    if ($attendance && $attendance->clock_in && $attendance->clock_out) {
                        $workTotal = \Carbon\Carbon::parse($attendance->clock_out)->diffInSeconds($attendance->clock_in) - $restTotal;
                    }
                @endphp
                <tr>
                    <td>{{ $date->format('m/d(D)') }}</td>
                    <td>{{ $attendance && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                    <td>{{ $attendance && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                    <td>{{ $restTotal ? gmdate('H:i', $restTotal) : '' }}</td>
                    <td>{{ $workTotal ? gmdate('H:i', $workTotal) : '' }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', $attendance->id ?? -1) }}?date={{ $date->format('Y-m-d') }}">詳細</a>
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
@endsection
