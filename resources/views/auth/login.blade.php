@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="card">
        <h1>Log in</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror

            <label class="checkbox">
                <input type="checkbox" name="remember"> Remember me
            </label>

            <button type="submit" class="button">Log in</button>
        </form>
    </div>
@endsection
