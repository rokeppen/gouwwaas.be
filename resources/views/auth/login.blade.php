@extends('layouts.app')

@section('title', 'Inloggen')

@section('content')
    <section class="container section">
        <div class="card form-card">
            <h1>Inloggen</h1>

            <form method="POST" action="{{ route('login') }}" class="form">
                @csrf

                <div class="field">
                    <label for="email">E-mailadres</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                        <p class="error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Wachtwoord</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    @error('password')
                        <p class="error" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="checkbox">
                    <input type="checkbox" name="remember"> Ingelogd blijven
                </label>

                <button type="submit" class="button button-block">Inloggen</button>
            </form>
        </div>
    </section>
@endsection
