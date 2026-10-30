@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="hero">
        <div class="container hero-inner">
            <p class="eyebrow">Scouting in the Waasland</p>
            <h1>Welcome to Gouw Waas</h1>

            {{-- Introduction text goes here --}}
            <p class="lead placeholder">Introduction text coming soon.</p>

            <div class="hero-actions">
                <a href="{{ route('activities') }}" class="button">Activities</a>
                <a href="{{ route('who-is-who') }}" class="button button-outline">Who is who</a>
            </div>
        </div>
    </section>

    <section class="container section">
        <div class="cards">
            <a href="{{ route('who-is-who') }}" class="card card-link">
                <span class="card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"/><path d="M1 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/><path d="M17 4a4 4 0 0 1 0 8"/><path d="M23 21v-1a6 6 0 0 0-4-5.6"/></svg>
                </span>
                <h2>Who is who</h2>
                <p>Meet the people behind the gouw.</p>
            </a>
            <a href="{{ route('activities') }}" class="card card-link">
                <span class="card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </span>
                <h2>Activities</h2>
                <p>See what's coming up in the gouw.</p>
            </a>
        </div>
    </section>
@endsection
