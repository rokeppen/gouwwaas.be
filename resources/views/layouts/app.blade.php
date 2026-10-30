<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#c7d121">
    <title>@yield('title', 'Home') · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="preload" href="{{ asset('fonts/nunito-sans-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a href="#content" class="skip-link">Skip to content</a>

    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('home') }}" class="brand" aria-label="Gouw Waas – home">
                @if (file_exists(public_path('images/logo.svg')))
                    <img src="{{ asset('images/logo.svg') }}" alt="" class="brand-logo">
                @else
                    <span class="brand-mark" aria-hidden="true">GW</span>
                @endif
                <span class="brand-text">
                    <span class="brand-name">Gouw Waas</span>
                    <span class="brand-tagline">Scouts en Gidsen Vlaanderen</span>
                </span>
            </a>

            <div class="account">
                @auth
                    <span class="welcome">Welcome <strong>{{ auth()->user()->name }}</strong>!</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="button button-ghost button-small">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="button button-ghost button-small">Log in</a>
                @endauth
            </div>
        </div>

        <nav class="container tabs" aria-label="Main">
            <a href="{{ route('home') }}" @class(['tab', 'active' => request()->routeIs('home')]) @if (request()->routeIs('home')) aria-current="page" @endif>Home</a>
            <a href="{{ route('who-is-who') }}" @class(['tab', 'active' => request()->routeIs('who-is-who')]) @if (request()->routeIs('who-is-who')) aria-current="page" @endif>Who is who</a>
            <a href="{{ route('activities') }}" @class(['tab', 'active' => request()->routeIs('activities')]) @if (request()->routeIs('activities')) aria-current="page" @endif>Activities</a>
        </nav>
    </header>

    <main id="content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div>
                <p class="footer-name">Gouw Waas</p>
                <p>Part of Scouts en Gidsen Vlaanderen</p>
            </div>
            <ul class="footer-links">
                <li><a href="mailto:gouwbureau@gouwwaas.be">gouwbureau@gouwwaas.be</a></li>
                <li><a href="https://www.facebook.com/gouwwaas" rel="noopener">Facebook</a></li>
                <li><a href="https://www.scoutsengidsenvlaanderen.be" rel="noopener">scoutsengidsenvlaanderen.be</a></li>
            </ul>
        </div>
        <div class="container footer-bottom">&copy; {{ date('Y') }} Gouw Waas</div>
    </footer>
</body>
</html>
