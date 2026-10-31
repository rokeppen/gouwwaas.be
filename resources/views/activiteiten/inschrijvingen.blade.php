@extends('layouts.app')

@section('title', 'Inschrijvingen · '.$activity->title)

@section('content')
    <section class="page-header">
        <div class="container">
            <p><a href="{{ route('activiteiten.show', $activity) }}" class="back-link">{{ $activity->title }}</a></p>
            <div class="page-header-row">
                <h1>Inschrijvingen</h1>
                @if ($activity->registrations->isNotEmpty())
                    <a href="{{ route('activiteiten.inschrijvingen.export', $activity) }}" class="button">Download (Excel/CSV)</a>
                @endif
            </div>
            <p class="lead">
                {{ $activity->registrations->count() }} {{ $activity->registrations->count() === 1 ? 'inschrijving' : 'inschrijvingen' }}
                · {{ $activity->dateLabel() }}
            </p>
        </div>
    </section>

    <section class="container section">
        @if ($activity->registrations->isEmpty())
            <div class="empty-state">
                <p>Nog niemand ingeschreven.</p>
            </div>
        @else
            <div class="table-scroll" tabindex="0" role="region" aria-label="Inschrijvingen">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Ingeschreven op</th>
                            <th scope="col">Naam</th>
                            <th scope="col">E-mailadres</th>
                            @foreach ($columns as $column)
                                <th scope="col">{{ $column['label'] }}</th>
                            @endforeach
                            <th scope="col"><span class="visually-hidden">Acties</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activity->registrations as $registration)
                            <tr>
                                <td class="nowrap">{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                <td class="nowrap">{{ $registration->name }}</td>
                                <td class="nowrap"><a href="mailto:{{ $registration->email }}">{{ $registration->email }}</a></td>
                                @foreach ($columns as $column)
                                    <td>{{ $registration->answerFor($column['id']) }}</td>
                                @endforeach
                                <td class="nowrap">
                                    <form method="POST" action="{{ route('activiteiten.inschrijvingen.destroy', [$activity, $registration]) }}"
                                          onsubmit="return confirm({{ Js::from("De inschrijving van {$registration->name} verwijderen?") }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-danger button-small">Verwijderen</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
