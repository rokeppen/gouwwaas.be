@extends('layouts.app')

@section('title', 'Wie is wie')

@section('content')
    <section class="page-header">
        <div class="container">
            <h1>Wie is wie</h1>
            <p class="lead">En hoe zit dat dan juist in elkaar?</p>
        </div>
    </section>

    <section class="container section">
        <div class="section-heading">
            <h2>Het gouwbureau</h2>
            <p>
                De dagelijkse leiding van de gouw ligt bij het gouwbureau. Dit bureau bestaat uit drie functies.
            </p>
        </div>

        <div class="roles">
            <article class="card role">
                <h3>Gouwvoorzitter</h3>
                <p>Verantwoordelijk voor de coördinatie binnen de gouw.</p>
            </article>
            <article class="card role">
                <h3>Gouwcommissaris</h3>
                <p>De pedagogisch verantwoordelijke.</p>
            </article>
            <article class="card role">
                <h3>Gouwbeheerder</h3>
                <p>Verantwoordelijk voor de financiën en andere beheersmatige thema's.</p>
            </article>
        </div>
    </section>

    <section class="section section-tinted">
        <div class="container">
            <div class="section-heading">
                <h2>Adjuncten</h2>
                <p>
                    Uiteraard kan onze gouw niet werken zonder nog een hele hoop andere medewerkers, elk met hun eigen
                    specialiteit. Momenteel hebben we voor de volgende thema's adjuncten:
                </p>
            </div>

            <div class="themes">
                @foreach ([
                    'Technieken' => "Interesse in een vorming? Of advies nodig over een sjorconstructie? Je kan altijd bij onze AGC's technieken terecht!",
                    'Integriteit' => null,
                    'Diversiteit' => null,
                    'Social media' => null,
                    'Evenementen' => null,
                    'Karree' => 'Omdat we als een van de weinige gouwen een gouwhuis hebben met een vaste evenementenwerking, is hier ook een apart team voor opgericht.',
                    'Materiaal' => null,
                    'Beheerszaken' => null,
                    'Catering' => null,
                ] as $theme => $description)
                    <article class="theme">
                        <h3>{{ $theme }}</h3>
                        @if ($description)
                            <p>{{ $description }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container section">
        <div class="section-heading">
            <h2>Districtscommissarissen</h2>
            <p>
                Tot slot maken ook de districtscommissarissen deel uit van de gouw. Zij komen op onze samenkomsten hun
                districten vertegenwoordigen en zorgen dat alle belangrijke info tot bij de groepen geraakt.
            </p>
        </div>

        <ul class="district-links">
            @foreach ($districts as $slug => $district)
                <li>
                    <a href="{{ route('districten') }}#district-{{ $slug }}">
                        <span>District {{ $district['name'] }}</span>
                        <span class="muted">{{ count($district['groups']) }} groepen</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endsection
