@extends('layouts.app')

@section('title', $activity->title)
@section('description', \Illuminate\Support\Str::limit($activity->description, 155))

@section('content')
    <section class="page-header">
        <div class="container">
            <p><a href="{{ route('activiteiten.index') }}" class="back-link">Alle activiteiten</a></p>
            <h1>{{ $activity->title }}</h1>

            <ul class="facts">
                <li>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span><span class="visually-hidden">Wanneer: </span>{{ $activity->dateLabel() }}</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    <span><span class="visually-hidden">Waar: </span>{{ $activity->location }}</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
                    <span><span class="visually-hidden">Prijs: </span>{{ $activity->priceLabel() }}</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <span>Inschrijven tot {{ $activity->registration_deadline->translatedFormat('l j F Y, H:i') }}</span>
                </li>
            </ul>

            @can('update', $activity)
                <div class="owner-actions">
                    <a href="{{ route('activiteiten.inschrijvingen', $activity) }}" class="button button-small">Inschrijvingen ({{ $activity->registrations()->count() }})</a>
                    <a href="{{ route('activiteiten.edit', $activity) }}" class="button button-ghost button-small">Bewerken</a>
                </div>
            @endcan
        </div>
    </section>

    <section class="container section">
        <div class="activity-layout">
            <article class="prose activity-description">
                {!! $activity->descriptionHtml() !!}
            </article>

            <aside class="card registration-card" id="inschrijven">
                <h2>Inschrijven</h2>

                @error('registration')
                    <p class="flash flash-error" role="alert">{{ $message }}</p>
                @enderror

                @if (! $activity->isRegistrationOpen())
                    <p class="muted">De inschrijvingen voor deze activiteit zijn afgesloten.</p>
                @else
                    <form method="POST" action="{{ route('activiteiten.inschrijven', $activity) }}" class="form">
                        @csrf

                        <div class="field">
                            <label for="reg-name">Naam <span class="required" aria-hidden="true">*</span></label>
                            <input id="reg-name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" @error('name') aria-invalid="true" aria-describedby="reg-name-error" @enderror>
                            @error('name')
                                <p class="error" id="reg-name-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="reg-email">E-mailadres <span class="required" aria-hidden="true">*</span></label>
                            <input id="reg-email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="reg-email-error" @enderror>
                            @error('email')
                                <p class="error" id="reg-email-error">{{ $message }}</p>
                            @enderror
                        </div>

                        @foreach ($activity->fields as $field)
                            @php($name = "answers[{$field->id}]")
                            @php($key = "answers.{$field->id}")
                            @php($id = "field-{$field->id}")

                            <div class="field">
                                @if ($field->type === 'checkbox')
                                    <label class="checkbox">
                                        <input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="1" @checked(old($key)) @required($field->required) @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>
                                        <span>{{ $field->label }} @if ($field->required)<span class="required" aria-hidden="true">*</span>@endif</span>
                                    </label>
                                @else
                                    <label for="{{ $id }}">{{ $field->label }} @if ($field->required)<span class="required" aria-hidden="true">*</span>@endif</label>

                                    @if ($field->type === 'textarea')
                                        <textarea id="{{ $id }}" name="{{ $name }}" rows="4" maxlength="5000" @required($field->required) @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>{{ old($key) }}</textarea>
                                    @elseif ($field->type === 'select')
                                        <select id="{{ $id }}" name="{{ $name }}" @required($field->required) @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>
                                            <option value="">Maak een keuze</option>
                                            @foreach ($field->options ?? [] as $option)
                                                <option value="{{ $option }}" @selected(old($key) === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input id="{{ $id }}" type="{{ $field->type }}" name="{{ $name }}" value="{{ old($key) }}"
                                               @if ($field->type === 'number') step="any" inputmode="decimal" @endif
                                               @required($field->required) @error($key) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror>
                                    @endif
                                @endif

                                @error($key)
                                    <p class="error" id="{{ $id }}-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <p class="muted small"><span class="required" aria-hidden="true">*</span> verplicht</p>

                        <button type="submit" class="button button-accent button-block">
                            Inschrijven{{ $activity->isFree() ? '' : ' · '.$activity->priceLabel() }}
                        </button>
                    </form>
                @endif
            </aside>
        </div>
    </section>
@endsection
