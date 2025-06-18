{{-- @extends('layouts.app')

@section('title', '勤怠一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
@include('components.header')

@php
    use Carbon\Carbon;
    $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
@endphp

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
                $modRequest = $modRequests[$user->id] ?? null;

                // 修正申請があればそれを優先
                $clockIn = $modRequest->clock_in ?? optional($attendance)->clock_in;
                $clockOut = $modRequest->clock_out ?? optional($attendance)->clock_out;

                // 休憩時間
                $restMinutes = 0;
                $rests = collect();

                if ($modRequest && $modRequest->rests) {
                    $rests = collect(is_string($modRequest->rests) ? json_decode($modRequest->rests, true) : $modRequest->rests);
                } elseif ($attendance && $attendance->rests) {
                    $rests = $attendance->rests->map(fn($r) => ['start_time' => $r->start_time, 'end_time' => $r->end_time]);
                }

                foreach ($rests as $rest) {
                    if (!empty($rest['start_time']) && !empty($rest['end_time'])) {
                        $restMinutes += \Carbon\Carbon::parse($rest['end_time'])->diffInMinutes(\Carbon\Carbon::parse($rest['start_time']));
                    }
                }

                // 勤務合計時間（休憩を引く）
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
                <td><a href="{{ route('attendance.detail', ['id' => -1]) }}?date={{ $date->format('Y-m-d') }}&user_id={{ $user->id }}" class="detail-link">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection --}}

@extends('layouts.app')

@section('title', '勤怠一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
@include('components.header')

@php
    use Carbon\Carbon;
    $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
@endphp

<div class="attendance-container">
    <h2 class="page-title">{{ $carbonDate->format('Y年n月j日') }}の勤怠</h2>

    <div class="navigation-bar">
        <a href="{{ route('admin.attendance.index', ['date' => $carbonDate->copy()->subDay()->toDateString()]) }}" class="nav-button">← 前日</a>
        <div class="date-display">
            <i class="fas fa-calendar-alt"></i> {{ $carbonDate->format('Y/m/d') }}
        </div>
        <a href="{{ route('admin.attendance.index', ['date' => $carbonDate->copy()->addDay()->toDateString()]) }}" class="nav-button">翌日 →</a>
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
                    $modRequest = $modRequests[$user->id] ?? null;

                    $clockIn = $modRequest->clock_in ?? optional($attendance)->clock_in;
                    $clockOut = $modRequest->clock_out ?? optional($attendance)->clock_out;

                    $restMinutes = 0;
                    $rests = collect();

                    if ($modRequest && $modRequest->rests) {
                        $rests = collect(is_string($modRequest->rests) ? json_decode($modRequest->rests, true) : $modRequest->rests);
                    } elseif ($attendance && $attendance->rests) {
                        $rests = $attendance->rests->map(fn($r) => ['start_time' => $r->start_time, 'end_time' => $r->end_time]);
                    }

                    foreach ($rests as $rest) {
                        if (!empty($rest['start_time']) && !empty($rest['end_time'])) {
                            $restMinutes += Carbon::parse($rest['end_time'])->diffInMinutes(Carbon::parse($rest['start_time']));
                        }
                    }

                    $workMinutes = ($clockIn && $clockOut)
                        ? Carbon::parse($clockOut)->diffInMinutes(Carbon::parse($clockIn)) - $restMinutes
                        : null;
                @endphp

                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $clockIn ? Carbon::parse($clockIn)->format('H:i') : '' }}</td>
                    <td>{{ $clockOut ? Carbon::parse($clockOut)->format('H:i') : '' }}</td>
                    <td>{{ $restMinutes ? floor($restMinutes / 60) . ':' . str_pad($restMinutes % 60, 2, '0', STR_PAD_LEFT) : '' }}</td>
                    <td>{{ $workMinutes ? floor($workMinutes / 60) . ':' . str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT) : '' }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', ['id' => -1]) }}?date={{ $carbonDate->format('Y-m-d') }}&user_id={{ $user->id }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
