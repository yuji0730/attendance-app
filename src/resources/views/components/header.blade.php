<header class="header">
    <div class="logo">

        <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH">

    </div>

    @auth
        @php
            $user = Auth::user();
            $hideNavRoutes = ['login', 'register'];
            $currentRoute = Route::currentRouteName() ?? '';
        @endphp

        @if (!in_array($currentRoute, $hideNavRoutes))
            <nav class="nav">
                @if ($user->is_admin)
                    <a href="{{ route('admin.attendance.index') }}">勤怠一覧</a>
                    <a href="{{ route('admin.staff.index') }}">スタッフ一覧</a>
                    <a href="{{ route('request.index') }}">申請一覧</a>
                @else
                    <a href="{{ route('attendance.show') }}">勤怠</a>
                    <a href="{{ route('attendance.index') }}">勤怠一覧</a>
                    <a href="{{ route('request.index') }}">申請</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            </nav>
        @endif
    @endauth
</header>
