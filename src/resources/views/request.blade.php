@extends('layouts.app')

@php
    $user = Auth::user();
@endphp

@section('title', $title)

@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="application-container">
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-menu">
        <a href="#" class="tab active" data-tab="pending">承認待ち</a>
        <a href="#" class="tab" data-tab="approved">承認済み</a>
    </div>

    <div class="tab-content" id="pending">
        <table class="application-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingRequests as $request)
                    <tr>
                        <td>承認待ち</td>
                        <td>{{ $request->user->name }}</td>
                        <td>
                            {{ $request->attendance && $request->attendance->date
                                ? \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d')
                                : ($request->date ? \Carbon\Carbon::parse($request->date)->format('Y/m/d') : '-') }}
                        </td>
                        <td>{{ $request->remarks }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.approval.show', ['attendance_correct_request' => $request->id]) }}" class="detail-link">詳細</a>
                            @else
                                <a href="{{ route('attendance.detail', ['id' => $request->attendance_id ?? -1]) }}?date={{ $request->date }}" class="detail-link">詳細</a>
                            @endif
                            {{-- <a href="{{ route('attendance.detail', ['id' => $request->attendance_id ?? -1]) }}?date={{ $request->date }}" class="detail-link">詳細</a> --}}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">承認待ちの申請はありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="tab-content" id="approved" style="display:none;">
        <table class="application-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($approvedRequests as $request)
                    <tr>
                        <td>承認済み</td>
                        <td>{{ $request->user->name }}</td>
                        <td>
                            {{ $request->attendance && $request->attendance->date
                                ? \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d')
                                : ($request->date ? \Carbon\Carbon::parse($request->date)->format('Y/m/d') : '-') }}
                        </td>
                        <td>{{ $request->remarks }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.approval.show', ['attendance_correct_request' => $request->id]) }}" class="detail-link">詳細</a>
                            @else
                                <a href="{{ route('attendance.detail', ['id' => $request->attendance_id ?? -1]) }}?date={{ $request->date }}" class="detail-link">詳細</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">承認済みの申請はありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const target = this.getAttribute('data-tab');

            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = content.id === target ? 'block' : 'none';
            });
        });
    });
</script>
@endsection

