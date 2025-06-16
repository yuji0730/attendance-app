@extends('layouts.app')

@section('title','会員登録ページ（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection


@section('content')
@include('components.header')


<div class="register-wrapper">
    <h1 class="register-title">会員登録</h1>

    <form method="POST" action="/register" class="register-form">
    @csrf

        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                @if ($message !== 'パスワードと一致しません')
                    <p class="error-message">{{ $message }}</p>
                @endif
            @enderror
        </div>

        <div class="form-group extra-margin">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            @php
                $passwordErrors = $errors->get('password') ?? [];
                $confirmedError = null;
                foreach ($passwordErrors as $error) {
                    if ($error === 'パスワードと一致しません') {
                        $confirmedError = $error;
                        break;
                    }
                }
            @endphp

            @if ($confirmedError)
                <p class="error-message">{{ $confirmedError }}</p>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="register-button">登録する</button>
        </div>
    </form>

    <div class="login-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection


