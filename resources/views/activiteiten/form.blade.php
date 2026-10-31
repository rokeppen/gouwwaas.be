@extends('layouts.app')

@section('title', $activity->exists ? 'Activiteit bewerken' : 'Nieuwe activiteit')

@php
    $inputDate = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';

    // After a validation error, show what was submitted; otherwise the stored fields.
    $fields = old('fields', $activity->fields->map(fn ($field) => [
        'id' => $field->id,
        'label' => $field->label,
        'type' => $field->type,
        'required' => $field->required,
        'options' => implode("\n", $field->options ?? []),
    ])->all());
@endphp

@section('content')
    <section class="page-header">
        <div class="container">
            <p>
                <a href="{{ $activity->exists ? route('activiteiten.show', $activity) : route('activiteiten.index') }}" class="back-link">
                    {{ $activity->exists ? 'Terug naar de activiteit' : 'Alle activiteiten' }}
                </a>
            </p>
            <h1>{{ $activity->exists ? 'Activiteit bewerken' : 'Nieuwe activiteit' }}</h1>
        </div>
    </section>

    <section class="container section">
        <form method="POST" action="{{ $activity->exists ? route('activiteiten.update', $activity) : route('activiteiten.store') }}" class="form activity-form">
            @csrf
            @if ($activity->exists)
                @method('PUT')
            @endif

            @if ($errors->any())
                <p class="flash flash-error" role="alert">Niet alles is correct ingevuld. Bekijk de velden hieronder.</p>
            @endif

            <div class="card">
                <h2>Over de activiteit</h2>

                <div class="field">
                    <label for="title">Titel</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $activity->title) }}" required maxlength="255" @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
                    @error('title')
                        <p class="error" id="title-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="description">Beschrijving</label>
                    <textarea id="description" name="description" rows="8" required maxlength="10000" aria-describedby="description-hint @error('description') description-error @enderror" @error('description') aria-invalid="true" @enderror>{{ old('description', $activity->description) }}</textarea>
                    <p class="hint" id="description-hint">Laat een lege regel tussen alinea's.</p>
                    @error('description')
                        <p class="error" id="description-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="location">Locatie</label>
                    <input id="location" type="text" name="location" value="{{ old('location', $activity->location) }}" required maxlength="255" placeholder="bv. Gouwhuis Karree, Apostelstraat 5-7, Sint-Niklaas" @error('location') aria-invalid="true" aria-describedby="location-error" @enderror>
                    @error('location')
                        <p class="error" id="location-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="starts_at">Begin</label>
                        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $inputDate($activity->starts_at)) }}" required @error('starts_at') aria-invalid="true" aria-describedby="starts-at-error" @enderror>
                        @error('starts_at')
                            <p class="error" id="starts-at-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="ends_at">Einde <span class="muted">(optioneel)</span></label>
                        <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $inputDate($activity->ends_at)) }}" @error('ends_at') aria-invalid="true" aria-describedby="ends-at-error" @enderror>
                        @error('ends_at')
                            <p class="error" id="ends-at-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="registration_deadline">Inschrijven kan tot</label>
                        <input id="registration_deadline" type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline', $inputDate($activity->registration_deadline)) }}" required @error('registration_deadline') aria-invalid="true" aria-describedby="deadline-error" @enderror>
                        @error('registration_deadline')
                            <p class="error" id="deadline-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="price">Prijs</label>
                        <div class="input-prefix">
                            <span aria-hidden="true">€</span>
                            <input id="price" type="text" name="price" inputmode="decimal" value="{{ old('price', $activity->exists && ! $activity->isFree() ? number_format($activity->price_cents / 100, 2, ',', '') : '') }}" placeholder="0,00" aria-describedby="price-hint @error('price') price-error @enderror" @error('price') aria-invalid="true" @enderror>
                        </div>
                        <p class="hint" id="price-hint">Leeg laten of 0 = gratis.</p>
                        @error('price')
                            <p class="error" id="price-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>Inschrijvingsformulier</h2>
                <p class="muted">
                    Iedereen vult altijd zijn <strong>naam</strong> en <strong>e-mailadres</strong> in.
                    Voeg hier de extra vragen toe die deelnemers moeten beantwoorden.
                </p>

                @if (($activity->registrations_count ?? 0) > 0)
                    <p class="flash flash-warning">
                        Er zijn al {{ $activity->registrations_count }} inschrijvingen. Bestaande antwoorden blijven bewaard,
                        ook als je een vraag aanpast of verwijdert.
                    </p>
                @endif

                @error('fields')
                    <p class="error">{{ $message }}</p>
                @enderror

                <ol class="field-builder" data-field-list>
                    @foreach ($fields as $key => $field)
                        @include('activiteiten._field-row', ['key' => $key, 'field' => $field])
                    @endforeach
                </ol>

                <p class="muted small" data-field-empty @if (count($fields) > 0) hidden @endif>Nog geen extra vragen.</p>

                <button type="button" class="button button-ghost" data-add-field>+ Vraag toevoegen</button>

                <template data-field-template>
                    @include('activiteiten._field-row', ['key' => '__KEY__', 'field' => []])
                </template>
            </div>

            <div class="form-actions">
                <button type="submit" class="button">{{ $activity->exists ? 'Opslaan' : 'Activiteit aanmaken' }}</button>
                <a href="{{ $activity->exists ? route('activiteiten.show', $activity) : route('activiteiten.index') }}" class="button button-ghost">Annuleren</a>
            </div>
        </form>

        @if ($activity->exists)
            <form method="POST" action="{{ route('activiteiten.destroy', $activity) }}" class="danger-zone"
                  onsubmit="return confirm({{ Js::from("\"{$activity->title}\" en alle inschrijvingen definitief verwijderen?") }})">
                @csrf
                @method('DELETE')
                <button type="submit" class="button button-danger">Activiteit verwijderen</button>
            </form>
        @endif
    </section>

    <script>
        (() => {
            const list = document.querySelector('[data-field-list]');
            const template = document.querySelector('[data-field-template]');
            const empty = document.querySelector('[data-field-empty]');
            let counter = Date.now();

            const refresh = () => { empty.hidden = list.children.length > 0; };

            const toggleOptions = (row) => {
                const isSelect = row.querySelector('[data-field-type]').value === 'select';
                row.querySelector('[data-field-options]').hidden = ! isSelect;
            };

            document.querySelector('[data-add-field]').addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__KEY__', 'n' + (counter++));
                list.insertAdjacentHTML('beforeend', html);
                list.lastElementChild.querySelector('input[type=text]').focus();
                refresh();
            });

            list.addEventListener('change', (event) => {
                if (event.target.matches('[data-field-type]')) {
                    toggleOptions(event.target.closest('[data-field-row]'));
                }
            });

            list.addEventListener('click', (event) => {
                const row = event.target.closest('[data-field-row]');
                if (! row) return;

                if (event.target.matches('[data-remove]')) {
                    row.remove();
                    refresh();
                } else if (event.target.matches('[data-move="up"]') && row.previousElementSibling) {
                    row.previousElementSibling.before(row);
                    event.target.focus();
                } else if (event.target.matches('[data-move="down"]') && row.nextElementSibling) {
                    row.nextElementSibling.after(row);
                    event.target.focus();
                }
            });
        })();
    </script>
@endsection
