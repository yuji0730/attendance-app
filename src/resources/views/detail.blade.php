@extends('layouts.app')

@section('title', '勤怠詳細ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="detail-container">
    <h2 class="title">勤怠詳細</h2>
    <form method="POST" action="{{ route('attendance-request.store', ['date' => $date]) }}">
    {{-- <form method="POST" action="#"> --}}
        @csrf

        <div class="attendance-card">
            <div class="row">
                <div class="label">名前</div>
                <div class="value">{{ $userName }}</div>
            </div>

            <div class="row">
                <div class="label">日付</div>
                <div class="value">
                    {{ \Carbon\Carbon::parse($date)->format('Y年') }}
                    <span class="space"></span>
                    {{ \Carbon\Carbon::parse($date)->format('n月j日') }}
                </div>
            </div>

            <div class="row">
                <div class="label">出勤・退勤</div>
                <div class="value time-input">
                    <div class="input-group">
                        <input type="text" name="clock_in" value="{{ old('clock_in', \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')) }}">
                        @error('clock_in')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="separator">～</span>
                    <div class="input-group">
                        <input type="text" name="clock_out" value="{{ old('clock_out', \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')) }}">
                        @error('clock_out')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @foreach ($attendance->rests as $index => $rest)
                <div class="row">
                    <div class="label">休憩{{ $index + 1 }}</div>
                    <div class="value time-input">
                        <div class="input-group">
                            <input type="text" name="rests[{{ $index }}][start_time]" value="{{ old("rests.$index.start_time", \Carbon\Carbon::parse($rest->start_time)->format('H:i')) }}">
                            @error("rests.$index.start_time")
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <span class="separator">～</span>
                        <div class="input-group">
                            <input type="text" name="rests[{{ $index }}][end_time]" value="{{ old("rests.$index.end_time", \Carbon\Carbon::parse($rest->end_time)->format('H:i')) }}">
                            @error("rests.$index.end_time")
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="row">
                <div class="label">休憩{{ count($attendance->rests) + 1 }}</div>
                <div class="value time-input">
                    <div class="input-group">
                        <input type="text" name="rests[new][start_time]" value="{{ old('rests.new.start_time') }}">
                        @error('rests.new.start_time')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="separator">～</span>
                    <div class="input-group">
                        <input type="text" name="rests[new][end_time]" value="{{ old('rests.new.end_time') }}">
                        @error('rests.new.end_time')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <div class="input-group">
                        <textarea name="remarks" placeholder="修正理由など">{{ old('remarks', $attendance->remarks) }}</textarea>
                        @error('remarks')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            @php
                $attendanceDate = $attendance->date ? \Carbon\Carbon::parse($attendance->date)->startOfDay(): null;
                $today = now()->startOfDay();
                $isPast = $attendanceDate->lt($today); // 勤怠日が今日より前
                $isToday = $attendanceDate->eq($today); // 勤怠日が今日
                $hasClockOut = !is_null($attendance->clock_out); // 退勤済み
            @endphp

            @if (($status ?? '') === 'pending')
                <p class="pending-message">*承認待ちのため修正はできません。</p>
            @elseif (($status ?? '') === 'approved')
                <p class="pending-message">*この勤怠はすでに承認済みのため修正できません。</p>
            @elseif ($isPast || ($isToday && $hasClockOut))
                <button type="submit" class="submit-button">修正</button>
            @endif
        </div>
    </form>
</div>
@endsection





