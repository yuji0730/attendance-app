@extends('layouts.app')

@section('title','ログインページ（管理者ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/auth/login.css') }}">
@endsection

@section('content')
<div class="login-wrapper">
    <h1 class="login-title">管理者ログイン</h1>

    <form method="POST" action="/login" class="login-form">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group extra-margin">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="login-button">管理者ログインする</button>
        </div>
    </form>
</div>
@endsection
