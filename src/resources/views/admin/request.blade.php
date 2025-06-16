@extends('layouts.app')

@section('title', '申請一覧ページ（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="application-container">
    <h2 class="page-title">｜申請一覧</h2>

    <div class="tab-menu">
        <a href="#" class="tab active">承認待ち</a>
        <a href="#" class="tab">承認済み</a>
    </div>

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
            <tr>
                <td>承認待ち</td>
                <td>西玲奈</td>
                <td>2023/06/01</td>
                <td>遅延のため</td>
                <td>2023/06/02</td>
                <td><a href="#" class="detail-link">詳細</a></td>
            </tr>
            <tr>
                <td>承認待ち</td>
                <td>山田太郎</td>
                <td>2023/06/01</td>
                <td>遅延のため</td>
                <td>2023/08/02</td>
                <td><a href="#" class="detail-link">詳細</a></td>
            </tr>
            <tr>
                <td>承認待ち</td>
                <td>山田花子</td>
                <td>2023/06/02</td>
                <td>遅延のため</td>
                <td>2023/07/02</td>
                <td><a href="#" class="detail-link">詳細</a></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
