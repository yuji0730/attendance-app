@extends('layouts.app')

@section('title', '勤怠一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="attendance-container">
    <h2 class="page-title">{{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}の勤怠</h2>

    <div class="navigation-bar">
        <a href="{{ route('admin.attendance.index', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}" class="nav-button">← 前日</a>
        <div class="date-display">
            <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
        </div>
        <a href="{{ route('admin.attendance.index', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}" class="nav-button">翌日 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                @php
                    $attendance = $attendances[$user->id] ?? null;
                    $clockIn = optional($attendance)->clock_in;
                    $clockOut = optional($attendance)->clock_out;

                    // 休憩時間をリレーション経由で合計
                    $restMinutes = 0;
                    if ($attendance && $attendance->rests) {
                        $restMinutes = $attendance->rests->sum(function ($rest) {
                            return \Carbon\Carbon::parse($rest->end_time)
                                ->diffInMinutes(\Carbon\Carbon::parse($rest->start_time));
                        });
                    }

                    // 合計勤務時間（出勤と退勤がある場合のみ）
                    $workMinutes = ($clockIn && $clockOut)
                        ? \Carbon\Carbon::parse($clockOut)->diffInMinutes(\Carbon\Carbon::parse($clockIn)) - $restMinutes
                        : null;
                @endphp

                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $clockIn ? \Carbon\Carbon::parse($clockIn)->format('H:i') : '' }}</td>
                    <td>{{ $clockOut ? \Carbon\Carbon::parse($clockOut)->format('H:i') : '' }}</td>
                    <td>{{ $restMinutes ? floor($restMinutes / 60) . ':' . str_pad($restMinutes % 60, 2, '0', STR_PAD_LEFT) : '' }}</td>
                    <td>{{ $workMinutes ? floor($workMinutes / 60) . ':' . str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT) : '' }}</td>
                    <td><a href="#" class="detail-link">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

