<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('home') }}" class="brand">Gouw Waas</a>

            <div class="account">
                @auth
                    <span class="welcome">Welcome {{ auth()->user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-button">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                @endauth
            </div>
        </div>

        <nav class="container tabs">
            <a href="{{ route('home') }}" @class(['tab', 'active' => request()->routeIs('home')])>Home</a>
            <a href="{{ route('who-is-who') }}" @class(['tab', 'active' => request()->routeIs('who-is-who')])>Who is who</a>
            <a href="{{ route('activities') }}" @class(['tab', 'active' => request()->routeIs('activities')])>Activities</a>
        </nav>
    </header>

    <main class="container content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">&copy; {{ date('Y') }} Gouw Waas</div>
    </footer>
</body>
</html>
