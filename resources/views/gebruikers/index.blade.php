@extends('layouts.app')

@section('title', 'Gebruikers')

@section('content')
    <section class="page-header">
        <div class="container page-header-row">
            <h1>Gebruikers</h1>
            <a href="{{ route('gebruikers.create') }}" class="button">Gebruiker toevoegen</a>
        </div>
    </section>

    <section class="container section">
        @error('user')
            <p class="flash flash-error" role="alert">{{ $message }}</p>
        @enderror

        <ul class="user-list">
            @foreach ($users as $user)
                <li class="card user-row">
                    <div class="user-info">
                        <p class="user-name">
                            {{ $user->name }}
                            @if ($user->is_admin)
                                <span class="badge">Beheerder</span>
                            @endif
                            @if ($user->is(auth()->user()))
                                <span class="badge badge-muted">Jij</span>
                            @endif
                        </p>
                        <p class="muted">{{ $user->email }}</p>
                    </div>

                    <div class="user-actions">
                        <a href="{{ route('gebruikers.edit', $user) }}" class="button button-ghost button-small">Bewerken</a>

                        <form method="POST" action="{{ route('gebruikers.wachtwoord-reset', $user) }}"
                              onsubmit="return confirm({{ Js::from("Het huidige wachtwoord van {$user->name} wordt meteen ongeldig en er wordt een e-mail gestuurd naar {$user->email} om een nieuw te kiezen. Doorgaan?") }})">
                            @csrf
                            <button type="submit" class="button button-ghost button-small">Wachtwoord resetten</button>
                        </form>

                        @unless ($user->is(auth()->user()))
                            <form method="POST" action="{{ route('gebruikers.destroy', $user) }}"
                                  onsubmit="return confirm({{ Js::from("{$user->name} definitief verwijderen?") }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger button-small">Verwijderen</button>
                            </form>
                        @endunless
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
@endsection
