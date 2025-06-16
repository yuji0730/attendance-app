@extends('layouts.app')

@section('title','ログインページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="login-wrapper">
    <h1 class="login-title">ログイン</h1>

    <form method="POST" action="/login" class="login-form">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            {{-- @if ($errors->has('email'))
                <p class="error-message">{{ $errors->first('email') }}</p>
            @endif --}}
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group extra-margin">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            {{-- @if ($errors->has('password'))
                <p class="error-message">{{ $errors->first('password') }}</p>
            @endif --}}
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="login-button">ログインする</button>
        </div>
    </form>

    <div class="register-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>
@endsection
