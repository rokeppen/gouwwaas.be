<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#23290a">
    <meta name="description" content="@yield('description', 'Gouw Waas verenigt de scoutsgroepen van de districten Klaas, Lelie, Durme en Land van Aymon in het Waasland.')">
    <title>@yield('title', 'Home') · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/icon-192.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <link rel="preload" href="{{ asset('fonts/nunito-sans-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a href="#content" class="skip-link">Naar de inhoud</a>

    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ asset('images/logo-200.webp') }}" alt="Gouw Waas – Scouts en Gidsen Vlaanderen" class="brand-logo" width="200" height="144">
            </a>

            <nav class="tabs" aria-label="Hoofdmenu">
                <a href="{{ route('home') }}" @class(['tab', 'active' => request()->routeIs('home')]) @if (request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a href="{{ route('districten') }}" @class(['tab', 'active' => request()->routeIs('districten')]) @if (request()->routeIs('districten')) aria-current="page" @endif>Districten</a>
                <a href="{{ route('wie-is-wie') }}" @class(['tab', 'active' => request()->routeIs('wie-is-wie')]) @if (request()->routeIs('wie-is-wie')) aria-current="page" @endif>Wie is wie</a>
                <a href="{{ route('activiteiten') }}" @class(['tab', 'active' => request()->routeIs('activiteiten')]) @if (request()->routeIs('activiteiten')) aria-current="page" @endif>Activiteiten</a>
            </nav>

            <div class="account">
                @auth
                    <span class="welcome">Welkom <strong>{{ auth()->user()->name }}</strong>!</span>
                    <div class="account-actions">
                        <a href="{{ route('profiel') }}" @class(['button', 'button-ghost', 'button-small', 'is-active' => request()->routeIs('profiel')])>Profiel</a>
                        @can('admin')
                            <a href="{{ route('gebruikers.index') }}" @class(['button', 'button-ghost', 'button-small', 'is-active' => request()->routeIs('gebruikers.*')])>Gebruikers</a>
                        @endcan
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="button button-ghost button-small">Uitloggen</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="button button-ghost button-small">Inloggen</a>
                @endauth
            </div>
        </div>
    </header>

    <main id="content">
        @if (session('status'))
            <div class="container">
                <p class="flash" role="status">{{ session('status') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div class="footer-about">
                <img src="{{ asset('images/logo-200.webp') }}" alt="Gouw Waas" class="footer-logo" width="200" height="144" loading="lazy">
                <div>
                    <address class="footer-address">
                        <strong>Gouwhuis Karree</strong><br>
                        Apostelstraat 5-7<br>
                        9100 Sint-Niklaas
                    </address>
                    <ul class="footer-links">
                        <li>
                            <a href="mailto:gouwbureau@gouwwaas.be">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
                                gouwbureau@gouwwaas.be
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/gouw.waas/" rel="noopener">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5" fill="currentColor"/></svg>
                                @gouw.waas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-federation">
                <a href="https://www.scoutsengidsenvlaanderen.be/" rel="noopener" class="federation-link">
                    <img src="{{ asset('images/scouts-en-gidsen-vlaanderen-wit.svg') }}" alt="Scouts en Gidsen Vlaanderen" width="92" height="140" loading="lazy">
                </a>
            </div>
        </div>
    </footer>
</body>
</html>
