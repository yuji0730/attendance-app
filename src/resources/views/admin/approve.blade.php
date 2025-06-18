@extends('layouts.app')

@section('title', '修正申請承認ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approve.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="detail-container">
    <h2 class="title">勤怠詳細</h2>
    <form action="{{ route('admin.request.approve', $modificationRequest->id) }}" method="POST">
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
                            value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}" readonly
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
                            value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}" readonly
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
                            value="{{ old("rests.$index.start_time", $start) }}" readonly
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
                            value="{{ old("rests.$index.end_time", $end) }}" readonly
                        >
                        @error("rests.$index.end_time")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endforeach

            {{-- 新しい休憩入力欄
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
            @endif --}}

            <div class="row">
                <div class="label">備考</div>
                <div class="value">
                    <div class="input-group">
                        <textarea name="remarks" placeholder="修正理由など" readonly>{{ $modificationRequest->remarks }}</textarea>
                        @error('remarks')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            @if ($status === 'pending')
                <button type="submit" class="submit-button">承認</button>
            @elseif ($status === 'approved')
                <button type="button" class="submit-button submit-button--disabled" disabled>承認済み</button>
            @endif
        </div>
    </form>
</div>
@endsection

