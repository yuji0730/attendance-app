@extends('layouts.app')

@section('title','勤怠登録ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection


@section('content')
@include('components.header')

<div class="attendance-container">
    <!-- ステータス表示 -->
    <div class="status">
        <span class="status-label">{{ $status }}</span>
    </div>

    <!-- 日付 -->
    <div class="date">
        @php
            $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        @endphp
        <p class="date-text">{{ now()->format('Y年n月j日') }}<span class="no-space">（{{ $weekDays[now()->dayOfWeek] }}）</span></p>
    </div>

    <!-- 現在時刻 -->
    <div class="time">
        <p id=clock class="time-text">{{ now()->format('H:i') }}</p>
    </div>

    <!-- アクションボタン -->
    <div class="buttons">
        @if ($status === '勤務外')
            <form method="POST" action="{{ route('attendance.clock_in') }}">
                @csrf
                <button class="btn black">出勤</button>
            </form>
        @elseif ($status === '出勤中')
            {{-- <form method="POST" action="{{ route('attendance.break_start') }}">
                @csrf
                <button class="btn orange">休憩入</button>
            </form> --}}
            <form method="POST" action="{{ route('attendance.clock_out') }}">
                @csrf
                <button class="btn black">退勤</button>
            </form>
            <form method="POST" action="{{ route('attendance.break_start') }}">
                @csrf
                <button class="btn white">休憩入</button>
            </form>
        @elseif ($status === '休憩中')
            <form method="POST" action="{{ route('attendance.break_end') }}">
                @csrf
                <button class="btn white">休憩戻</button>
            </form>
        @elseif ($status === '退勤済')
            <p class="message">お疲れ様でした。</p>
        @endif
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('clock').textContent = `${hours}:${minutes}`;
    }

    updateClock(); // 初回表示
    setInterval(updateClock, 10000); // 10秒ごとに更新（分単位ならこのくらいで十分）
</script>
@endsection
