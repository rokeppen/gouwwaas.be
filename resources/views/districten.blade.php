@extends('layouts.app')

@section('title', 'Districten')

@section('content')
    <section class="page-header">
        <div class="container">
            <h1>Districten</h1>
            <p class="lead">
                Gouw Waas bestaat uit vier districten, die samen
                {{ collect($districts)->sum(fn ($district) => count($district['groups'])) }} scoutsgroepen
                vertegenwoordigen verspreid over het ruime Waasland.
            </p>
        </div>
    </section>

    <section class="container section">
        <div class="districts">
            @foreach ($districts as $slug => $district)
                <article class="district" id="district-{{ $slug }}">
                    <header class="district-header">
                        <h2>District {{ $district['name'] }}</h2>
                        <span class="badge">{{ count($district['groups']) }} groepen</span>
                    </header>
                    <ul class="group-list">
                        @foreach ($district['groups'] as [$name, $town, $number, $website])
                            <li>
                                @if ($website)
                                    <a href="{{ $website }}" class="group-row" target="_blank" rel="noopener">
                                        <span class="group-name">
                                            {{ $name }}
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                                            <span class="visually-hidden">(website, opent in een nieuw tabblad)</span>
                                        </span>
                                        <span class="group-town">{{ $town }}</span>
                                    </a>
                                @else
                                    <span class="group-row">
                                        <span class="group-name">{{ $name }}</span>
                                        <span class="group-town">{{ $town }}</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>
@endsection
