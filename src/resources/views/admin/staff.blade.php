@extends('layouts.app')

@section('title', 'スタッフ一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="staff-container">
    <h2 class="page-title">スタッフ一覧</h2>

    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @php
                $staffs = [
                    ['name' => '西 伶奈', 'email' => 'reina.n@coachtech.com'],
                    ['name' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
                    ['name' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
                    ['name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com'],
                    ['name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com'],
                    ['name' => '中西 教夫', 'email' => 'norio.n@coachtech.com'],
                ];
            @endphp
            @foreach ($staffs as $staff)
            <tr>
                <td>{{ $staff['name'] }}</td>
                <td>{{ $staff['email'] }}</td>
                <td><a href="#" class="detail-link">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
