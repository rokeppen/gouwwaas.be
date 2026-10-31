<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityField;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationController extends Controller
{
    public function store(Request $request, Activity $activity): RedirectResponse
    {
        if (! $activity->isRegistrationOpen()) {
            return back()->withErrors(['registration' => 'De inschrijvingen voor deze activiteit zijn afgesloten.']);
        }

        $fields = $activity->fields;

        $rules = ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255']];
        $attributes = ['name' => 'naam', 'email' => 'e-mailadres'];

        foreach ($fields as $field) {
            $rules["answers.{$field->id}"] = $field->rules();
            $attributes["answers.{$field->id}"] = '"'.$field->label.'"';
        }

        $validated = $request->validate($rules, [
            'answers.*.accepted' => 'Vink :attribute aan.',
            'answers.*.in' => 'Kies een geldige waarde voor :attribute.',
        ], $attributes);

        $activity->registrations()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'answers' => $fields->map(fn (ActivityField $field) => [
                'field_id' => $field->id,
                'label' => $field->label,
                'value' => $field->present($validated['answers'][$field->id] ?? null),
            ])->values()->all(),
        ]);

        return redirect()->route('activiteiten.show', $activity)
            ->with('status', "Bedankt {$validated['name']}, je bent ingeschreven!");
    }

    public function index(Activity $activity): View
    {
        Gate::authorize('viewRegistrations', $activity);

        $activity->load('fields', 'registrations');

        return view('activiteiten.inschrijvingen', [
            'activity' => $activity,
            'columns' => $activity->answerColumns(),
        ]);
    }

    public function export(Activity $activity): StreamedResponse
    {
        Gate::authorize('viewRegistrations', $activity);

        $activity->load('fields', 'registrations');
        $columns = $activity->answerColumns();
        $filename = "inschrijvingen-{$activity->slug}.csv";

        return response()->streamDownload(function () use ($activity, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly

            // Semicolons: what Excel expects with Belgian/Dutch regional settings.
            fputcsv($out, ['Ingeschreven op', 'Naam', 'E-mailadres', ...$columns->pluck('label')], ';');

            foreach ($activity->registrations->sortBy('created_at') as $registration) {
                fputcsv($out, array_map($this->safeCell(...), [
                    $registration->created_at->format('d/m/Y H:i'),
                    $registration->name,
                    $registration->email,
                    ...$columns->map(fn (array $column) => $registration->answerFor($column['id']) ?? ''),
                ]), ';');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Answers come from the public: stop Excel from treating them as formulas.
     */
    private function safeCell(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    public function destroy(Activity $activity, Registration $registration): RedirectResponse
    {
        Gate::authorize('viewRegistrations', $activity);

        $registration->delete();

        return back()->with('status', "De inschrijving van {$registration->name} is verwijderd.");
    }
}
