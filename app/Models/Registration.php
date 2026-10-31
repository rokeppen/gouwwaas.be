<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'answers'])]
class Registration extends Model
{
    protected function casts(): array
    {
        return [
            'answers' => 'array',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function answerFor(int $fieldId): ?string
    {
        foreach ($this->answers as $answer) {
            if ($answer['field_id'] === $fieldId) {
                return $answer['value'];
            }
        }

        return null;
    }
}
