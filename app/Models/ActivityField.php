<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

#[Fillable(['label', 'type', 'required', 'options', 'position'])]
class ActivityField extends Model
{
    /** Field types an organiser can choose, with their Dutch labels. */
    public const TYPES = [
        'text' => 'Korte tekst',
        'textarea' => 'Lange tekst',
        'number' => 'Getal',
        'email' => 'E-mailadres',
        'tel' => 'Telefoonnummer',
        'date' => 'Datum',
        'select' => 'Keuzelijst',
        'checkbox' => 'Aanvinkvakje',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
            'options' => 'array',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Validation rules for an answer to this field.
     *
     * @return array<int, mixed>
     */
    public function rules(): array
    {
        if ($this->type === 'checkbox') {
            return $this->required ? ['accepted'] : ['nullable', 'boolean'];
        }

        $rules = [$this->required ? 'required' : 'nullable'];

        return [...$rules, ...match ($this->type) {
            'textarea' => ['string', 'max:5000'],
            'number' => ['numeric'],
            'email' => ['email', 'max:255'],
            'tel' => ['string', 'max:50'],
            'date' => ['date'],
            'select' => [Rule::in($this->options ?? [])],
            default => ['string', 'max:255'],
        }];
    }

    /**
     * Turn a submitted value into the text that is stored and shown.
     */
    public function present(mixed $value): string
    {
        if ($this->type === 'checkbox') {
            return $value ? 'Ja' : 'Nee';
        }

        return trim((string) $value);
    }
}
