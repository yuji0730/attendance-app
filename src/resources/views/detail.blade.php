{{-- @extends('layouts.app')

@section('title', '勤怠詳細ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="detail-container">
    <h2 class="title">勤怠詳細</h2>
    <form method="POST" action="{{ route('attendance-request.store', ['date' => $date]) }}">
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
                        <input
                            type="text"
                            name="clock_in"
                            value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error('clock_in')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="separator">～</span>
                    <div class="input-group">
                        <input
                            type="text"
                            name="clock_out"
                            value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
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
                        @php
                            $startRaw = $rest->start_time ?? '';
                            $endRaw = $rest->end_time ?? '';
                            try {
                                $start = $startRaw ? \Carbon\Carbon::parse($startRaw)->format('H:i') : '';
                            } catch (\Exception $e) {
                                $start = $startRaw;
                            }
                            try {
                                $end = $endRaw ? \Carbon\Carbon::parse($endRaw)->format('H:i') : '';
                            } catch (\Exception $e) {
                                $end = $endRaw;
                            }
                        @endphp

                        <input
                            type="text"
                            name="rests[{{ $index }}][start_time]"
                            value="{{ old("rests.$index.start_time", $start) }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error("rests.$index.start_time")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <span class="separator">～</span>
                    <div class="input-group">
                        <input
                            type="text"
                            name="rests[{{ $index }}][end_time]"
                            value="{{ old("rests.$index.end_time", $end) }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error("rests.$index.end_time")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endforeach

            {{-- 新しい休憩入力欄 --}}
            {{-- @if(!in_array($status, ['pending', 'approved']))
            <div class="row">
                <div class="label">休憩{{ count($attendance->rests) + 1 }}</div>
                <div class="value time-input">
                    <div class="input-group">
                        <input
                            type="text"
                            name="rests[new][start_time]"
                            value="{{ old('rests.new.start_time') }}"
                        >
                        @error('rests.new.start_time')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="separator">～</span>
                    <div class="input-group">
                        <input
                            type="text"
                            name="rests[new][end_time]"
                            value="{{ old('rests.new.end_time') }}"
                        >
                        @error('rests.new.end_time')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <div class="input-group">
                        <textarea name="remarks" placeholder="修正理由など" @if(in_array($status, ['pending', 'approved'])) readonly @endif>{{ old('remarks', $attendance->remarks) }}</textarea>
                        @error('remarks')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            @php
                $attendanceDate = $attendance->date ? \Carbon\Carbon::parse($attendance->date)->startOfDay() : null;
                $today = now()->startOfDay();
                $isPast = $attendanceDate ? $attendanceDate->lt($today) : false;
                $isToday = $attendanceDate ? $attendanceDate->eq($today) : false;
                $hasClockOut = !is_null($attendance->clock_out);
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
@endsection --}}



@extends('layouts.app')

{{-- @section('title', '勤怠詳細ページ（一般ユーザー）') --}}
@section('title', Auth::user()->is_admin ? '勤怠詳細ページ（管理者）' : '勤怠詳細ページ（一般ユーザー）')


@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="detail-container">
    <h2 class="title">勤怠詳細</h2>
    <form method="POST"
    action="@if(Auth::user()->is_admin)
        {{ isset($attendance) && $attendance->id ? route('admin.attendance.update', ['id' => $attendance->id]) : route('admin.attendance.store') }}
    @else
        {{ route('attendance-request.store', ['date' => $date]) }}
    @endif">



        @csrf
        @if(Auth::user()->is_admin && !$attendance)
            <input type="hidden" name="user_id" value="{{ request('user_id') }}">
            <input type="hidden" name="date" value="{{ $date }}">
        @endif



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
                        <input
                            type="text"
                            name="clock_in"
                            value="{{ old('clock_in', isset($attendance) && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error('clock_in')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="separator">～</span>
                    <div class="input-group">
                        <input
                            type="text"
                            name="clock_out"
                            value="{{ old('clock_out', isset($attendance) && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error('clock_out')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @foreach ($attendance->rests ?? [] as $index => $rest)
            <div class="row">
                <div class="label">休憩{{ $index + 1 }}</div>
                <div class="value time-input">
                    <div class="input-group">
                        @php
                            $startRaw = $rest->start_time ?? '';
                            $endRaw = $rest->end_time ?? '';
                            try {
                                $start = $startRaw ? \Carbon\Carbon::parse($startRaw)->format('H:i') : '';
                            } catch (\Exception $e) {
                                $start = $startRaw;
                            }
                            try {
                                $end = $endRaw ? \Carbon\Carbon::parse($endRaw)->format('H:i') : '';
                            } catch (\Exception $e) {
                                $end = $endRaw;
                            }
                        @endphp

                        <input
                            type="text"
                            name="rests[{{ $index }}][start_time]"
                            value="{{ old("rests.$index.start_time", $start) }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error("rests.$index.start_time")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <span class="separator">～</span>
                    <div class="input-group">
                        <input
                            type="text"
                            name="rests[{{ $index }}][end_time]"
                            value="{{ old("rests.$index.end_time", $end) }}"
                            @if(in_array($status, ['pending', 'approved'])) readonly @endif
                        >
                        @error("rests.$index.end_time")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endforeach

            {{-- 新しい休憩入力欄 --}}
            @if(!in_array($status, ['pending', 'approved']))
            <div class="row">
                <div class="label">休憩{{ count($attendance->rests) + 1 }}</div>
                <div class="value time-input">
                    <div class="input-group">
                        <input
                            type="text"
                            name="rests[new][start_time]"
                            value="{{ old('rests.new.start_time') }}"
                        >
                        @error('rests.new.start_time')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="separator">～</span>
                    <div class="input-group">
                        <input
                            type="text"
                            name="rests[new][end_time]"
                            value="{{ old('rests.new.end_time') }}"
                        >
                        @error('rests.new.end_time')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <div class="input-group">
                        <textarea name="remarks" placeholder="修正理由など" @if(in_array($status, ['pending', 'approved'])) readonly @endif>{{ old('remarks', $attendance->remarks) }}</textarea>
                        @error('remarks')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            @php
                $attendanceDate = $attendance->date ? \Carbon\Carbon::parse($attendance->date)->startOfDay() : null;
                $today = now()->startOfDay();
                $isPast = $attendanceDate ? $attendanceDate->lt($today) : false;
                $isToday = $attendanceDate ? $attendanceDate->eq($today) : false;
                $hasClockOut = !is_null($attendance->clock_out);
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

