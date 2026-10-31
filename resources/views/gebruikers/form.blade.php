@extends('layouts.app')

@section('title', $user->exists ? 'Gebruiker bewerken' : 'Gebruiker toevoegen')

@section('content')
    <section class="page-header">
        <div class="container">
            <p><a href="{{ route('gebruikers.index') }}" class="back-link">Alle gebruikers</a></p>
            <h1>{{ $user->exists ? 'Gebruiker bewerken' : 'Gebruiker toevoegen' }}</h1>
        </div>
    </section>

    <section class="container section">
        <div class="card form-card form-card-left">
            <form method="POST" action="{{ $user->exists ? route('gebruikers.update', $user) : route('gebruikers.store') }}" class="form">
                @csrf
                @if ($user->exists)
                    @method('PUT')
                @endif

                <div class="field">
                    <label for="name">Naam</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="off" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name')
                        <p class="error" id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="email">E-mailadres</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="off" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                        <p class="error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                @unless ($user->exists)
                    <p class="muted small">
                        De nieuwe gebruiker krijgt een e-mail met een link om zelf een wachtwoord te kiezen.
                        De link is 24 uur geldig.
                    </p>
                @endunless

                <button type="submit" class="button">{{ $user->exists ? 'Opslaan' : 'Gebruiker aanmaken' }}</button>
            </form>
        </div>
    </section>
@endsection
