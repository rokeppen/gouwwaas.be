@extends('layouts.app')

@section('title', 'Activiteiten')

@section('content')
    <section class="page-header">
        <div class="container page-header-row">
            <h1>Activiteiten</h1>
            @auth
                <a href="{{ route('activiteiten.create') }}" class="button">Nieuwe activiteit</a>
            @endauth
        </div>
    </section>

    <section class="container section">
        @if ($upcoming->isEmpty())
            <div class="empty-state">
                <p>Er staan momenteel geen activiteiten gepland. Kom binnenkort nog eens kijken!</p>
            </div>
        @else
            <div class="activity-list">
                @foreach ($upcoming as $activity)
                    @include('activiteiten._card', ['activity' => $activity])
                @endforeach
            </div>
        @endif
    </section>

    @if ($mine->isNotEmpty())
        <section class="section section-tinted">
            <div class="container">
                <div class="section-heading">
                    <h2>Mijn activiteiten</h2>
                    <p>Activiteiten die jij hebt aangemaakt.</p>
                </div>

                <ul class="user-list">
                    @foreach ($mine as $activity)
                        <li class="card user-row">
                            <div class="user-info">
                                <p class="user-name"><a href="{{ route('activiteiten.show', $activity) }}">{{ $activity->title }}</a></p>
                                <p class="muted">{{ $activity->starts_at->translatedFormat('j F Y') }} · {{ $activity->registrations_count }} {{ $activity->registrations_count === 1 ? 'inschrijving' : 'inschrijvingen' }}</p>
                            </div>
                            <div class="user-actions">
                                <a href="{{ route('activiteiten.inschrijvingen', $activity) }}" class="button button-ghost button-small">Inschrijvingen</a>
                                <a href="{{ route('activiteiten.edit', $activity) }}" class="button button-ghost button-small">Bewerken</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($past->isNotEmpty())
        <section class="container section">
            <div class="section-heading">
                <h2>Voorbije activiteiten</h2>
            </div>
            <div class="activity-list activity-list-past">
                @foreach ($past as $activity)
                    @include('activiteiten._card', ['activity' => $activity])
                @endforeach
            </div>
        </section>
    @endif
@endsection
