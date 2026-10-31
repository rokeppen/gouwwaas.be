@extends('layouts.app')

@section('title', 'Profiel')

@section('content')
    <section class="page-header">
        <div class="container">
            <h1>Profiel</h1>
        </div>
    </section>

    <section class="container section">
        <div class="stack">
            <div class="card">
                <h2>Je gegevens</h2>
                <dl class="details">
                    <div>
                        <dt>Naam</dt>
                        <dd>{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt>E-mailadres</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt>Rol</dt>
                        <dd>{{ $user->is_admin ? 'Beheerder' : 'Gebruiker' }}</dd>
                    </div>
                </dl>
                <p class="muted small">Klopt je naam of e-mailadres niet? Vraag een beheerder om het aan te passen.</p>
            </div>

            <div class="card">
                <h2>Wachtwoord wijzigen</h2>

                <form method="POST" action="{{ route('profiel.wachtwoord') }}" class="form">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="current_password">Huidig wachtwoord</label>
                        <input id="current_password" type="password" name="current_password" required autocomplete="current-password" @error('current_password', 'password') aria-invalid="true" aria-describedby="current-password-error" @enderror>
                        @error('current_password', 'password')
                            <p class="error" id="current-password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Nieuw wachtwoord</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" minlength="8" aria-describedby="password-hint @error('password', 'password') password-error @enderror" @error('password', 'password') aria-invalid="true" @enderror>
                        <p class="hint" id="password-hint">Minstens 8 tekens.</p>
                        @error('password', 'password')
                            <p class="error" id="password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Herhaal nieuw wachtwoord</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="button">Wachtwoord wijzigen</button>
                    <p class="muted small form-note">Je blijft hier ingelogd; op andere toestellen word je uitgelogd.</p>
                </form>
            </div>
        </div>
    </section>
@endsection
