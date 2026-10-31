@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="hero">
        <div class="container hero-inner">
            <div class="hero-text">
                <h1>Welkom bij Gouw&nbsp;Waas!</h1>
                <p class="lead">
                    Vier districten, {{ collect($districts)->sum(fn ($district) => count($district['groups'])) }} scoutsgroepen
                    en één thuisbasis: gouwhuis Karree in Sint-Niklaas.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('districten') }}" class="button button-accent">Onze districten</a>
                    <a href="{{ route('wie-is-wie') }}" class="button button-outline-light">Wie is wie</a>
                </div>
            </div>
            <img src="{{ asset('images/logo-600.webp') }}"
                 srcset="{{ asset('images/logo-600.webp') }} 600w, {{ asset('images/logo-1200.webp') }} 1200w"
                 sizes="(max-width: 860px) 70vw, 480px"
                 alt="" class="hero-logo" width="600" height="433">
        </div>
    </section>

    <section class="container section">
        <div class="intro-grid">
            <article class="prose">
                <h2>Gouw watte?</h2>
                <p>
                    Gouw Waas is de verzameling van de districten Klaas, Lelie, Durme en Land van Aymon, die samen
                    35 scoutsgroepen vertegenwoordigen verspreid over het ruime Waasland. Onze uitvalsbasis is in
                    gouwhuis Karree in Sint-Niklaas.
                </p>
                <p><a href="{{ route('districten') }}" class="arrow-link">Bekijk alle groepen per district</a></p>
            </article>

            <article class="prose">
                <h2>En wat doen jullie dan?</h2>
                <p>
                    De meeste leiding kent ons ongetwijfeld van <strong>Herfstontmoeting</strong>, waar onze ploeg voor
                    jullie klaarstaat. Als gouw ondersteunen wij de districten (en daarmee ook indirect de groepen) in
                    verschillende pedagogische en beleidsmatige thema's. Dit doen we door het aanbieden van vormingen
                    en het vertegenwoordigen op enkele nationale vergaderingen.
                </p>
                <p>
                    Daarnaast organiseren we ook <strong>open kampwerkingen</strong>, zodat elk kind of jongere uit de
                    regio mee op kamp kan, ook als dat via een eigen groep wat moeilijker lukt.
                </p>
            </article>
        </div>
    </section>

    <section class="container section section-flush-top">
        <div class="cta">
            <div class="cta-text">
                <h2>Interessant, kan ik ook bij de gouw komen?</h2>
                <p>
                    Zit jouw leidingscarrière erop, maar wil je nog geen afscheid nemen van scouting? Of heb je nood
                    aan een extra uitdaging buiten het engagement bij jouw groep? Perfect, wij kunnen altijd
                    enthousiaste nieuwe mensen gebruiken!
                </p>
                <p>
                    Neem zeker contact op met iemand van de gouw (via de knoppen hieronder of rechtstreeks via een
                    lid) en dan kijken we samen met jou wat je in onze gouw kan betekenen.
                </p>
            </div>
            <div class="cta-actions">
                <a href="mailto:gouwbureau@gouwwaas.be" class="button button-accent">Mail het gouwbureau</a>
                <a href="https://www.instagram.com/gouw.waas/" class="button button-outline-light" rel="noopener">Stuur een bericht op Instagram</a>
            </div>
        </div>
    </section>
@endsection
