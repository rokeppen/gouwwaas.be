@extends('layouts.app')

@section('title', 'Nieuw wachtwoord kiezen')

@section('content')
    <section class="container section">
        <div class="card form-card">
            <h1>Nieuw wachtwoord kiezen</h1>

            <form method="POST" action="{{ route('password.update') }}" class="form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="field">
                    <label for="email">E-mailadres</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                        <p class="error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Nieuw wachtwoord</label>
                    <input id="password" type="password" name="password" required autofocus autocomplete="new-password" minlength="8" aria-describedby="password-hint @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror>
                    <p class="hint" id="password-hint">Minstens 8 tekens.</p>
                    @error('password')
                        <p class="error" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Herhaal nieuw wachtwoord</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>

                <button type="submit" class="button button-block">Wachtwoord opslaan</button>
            </form>
        </div>
    </section>
@endsection
