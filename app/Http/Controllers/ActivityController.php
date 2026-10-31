<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        return view('activiteiten.index', [
            'upcoming' => Activity::upcoming()->orderBy('starts_at')->get(),
            'past' => Activity::past()->orderByDesc('starts_at')->limit(6)->get(),
            'mine' => $request->user()
                ? $request->user()->activities()->withCount('registrations')->orderByDesc('starts_at')->get()
                : collect(),
        ]);
    }

    public function show(Activity $activity): View
    {
        return view('activiteiten.show', ['activity' => $activity->load('fields')]);
    }

    public function create(): View
    {
        Gate::authorize('create', Activity::class);

        return view('activiteiten.form', ['activity' => new Activity]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Activity::class);

        [$attributes, $fields] = $this->validated($request);

        $activity = DB::transaction(function () use ($request, $attributes, $fields) {
            $activity = $request->user()->activities()->create($attributes);
            $activity->syncFields($fields);

            return $activity;
        });

        return redirect()->route('activiteiten.show', $activity)->with('status', 'De activiteit is aangemaakt.');
    }

    public function edit(Activity $activity): View
    {
        Gate::authorize('update', $activity);

        return view('activiteiten.form', ['activity' => $activity->load('fields')->loadCount('registrations')]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        Gate::authorize('update', $activity);

        [$attributes, $fields] = $this->validated($request);

        DB::transaction(function () use ($activity, $attributes, $fields) {
            $activity->update($attributes);
            $activity->syncFields($fields);
        });

        return redirect()->route('activiteiten.show', $activity)->with('status', 'De activiteit is bijgewerkt.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        Gate::authorize('delete', $activity);

        $activity->delete();

        return redirect()->route('activiteiten.index')->with('status', "\"{$activity->title}\" is verwijderd.");
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, array<string, mixed>>}
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'location' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'registration_deadline' => ['required', 'date', 'before_or_equal:starts_at'],
            'price' => ['nullable', 'regex:/^\d{1,5}([.,]\d{1,2})?$/'],
            'fields' => ['nullable', 'array', 'max:30'],
            'fields.*.id' => ['nullable', 'integer'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', Rule::in(array_keys(ActivityField::TYPES))],
            'fields.*.required' => ['nullable', 'boolean'],
            'fields.*.options' => ['nullable', 'string', 'max:5000'],
        ], [
            'ends_at.after_or_equal' => 'Het einde moet na het begin liggen.',
            'registration_deadline.before_or_equal' => 'De inschrijvingsdeadline moet voor het begin van de activiteit liggen.',
            'price.regex' => 'Vul een bedrag in, bv. 12 of 12,50.',
            'fields.*.label.required' => 'Geef elk veld een naam.',
        ], [
            'title' => 'titel',
            'description' => 'beschrijving',
            'location' => 'locatie',
            'starts_at' => 'begin',
            'ends_at' => 'einde',
            'registration_deadline' => 'inschrijvingsdeadline',
            'price' => 'prijs',
        ]);

        // A dropdown needs at least one option to choose from.
        foreach ($validated['fields'] ?? [] as $index => $field) {
            if ($field['type'] === 'select' && Activity::parseOptions($field['options'] ?? '') === []) {
                throw ValidationException::withMessages([
                    "fields.{$index}.options" => 'Geef een keuzelijst minstens één keuze (één per regel).',
                ]);
            }
        }

        $attributes = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'registration_deadline' => $validated['registration_deadline'],
            'price_cents' => (int) round(((float) str_replace(',', '.', $validated['price'] ?? '0')) * 100),
        ];

        return [$attributes, $validated['fields'] ?? []];
    }
}
