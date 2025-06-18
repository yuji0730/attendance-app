@extends('layouts.app')

@section('title', 'スタッフ別勤怠一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_attendance.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="monthly-container">
    <h2 class="page-title">｜西玲奈さんの勤怠</h2>

    <div class="month-selector">
        <a href="#" class="arrow">&larr; 前月</a>
        <div class="current-month">
            <i class="fa-regular fa-calendar"></i>
            <span>2023/06</span>
        </div>
        <a href="#" class="arrow">翌月 &rarr;</a>
    </div>

    <table class="monthly-table">
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
            @php
                $dates = [
                    '06/01(木)', '06/02(金)', '06/03(土)', '06/04(日)', '06/05(月)',
                    '06/06(火)', '06/07(水)', '06/08(木)', '06/09(金)', '06/10(土)',
                    '06/11(日)', '06/12(月)', '06/13(火)', '06/14(水)', '06/15(木)',
                    '06/16(金)', '06/17(土)', '06/18(日)', '06/19(月)', '06/20(火)',
                    '06/21(水)', '06/22(木)', '06/23(金)', '06/23(金)', '06/24(土)',
                    '06/25(日)', '06/26(月)', '06/27(火)', '06/28(水)', '06/29(木)', '06/30(金)'
                ];
            @endphp
            @foreach ($dates as $date)
            <tr>
                <td>{{ $date }}</td>
                @if (str_contains($date, '(土)') || str_contains($date, '(日)'))
                    <td colspan="4"></td>
                @else
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                @endif
                <td><a href="#" class="detail-link">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-button-container">
        <button class="csv-button">CSV出力</button>
    </div>
</div>
@endsection
