@extends('layouts.app')

@section('title', 'Wachtwoord vergeten')

@section('content')
    <section class="container section">
        <div class="card form-card">
            <h1>Wachtwoord vergeten</h1>
            <p class="muted">Vul je e-mailadres in. Je krijgt een e-mail met een link om een nieuw wachtwoord te kiezen.</p>

            <form method="POST" action="{{ route('password.email') }}" class="form">
                @csrf

                <div class="field">
                    <label for="email">E-mailadres</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                        <p class="error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="button button-block">Link versturen</button>
            </form>

            <p class="form-note"><a href="{{ route('login') }}" class="back-link">Terug naar inloggen</a></p>
        </div>
    </section>
@endsection
