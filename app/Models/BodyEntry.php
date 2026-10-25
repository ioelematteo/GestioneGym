<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Una misurazione del corpo (una al giorno). */
#[Fillable(['measured_on', 'weight_kg', 'body_fat', 'waist_cm', 'notes'])]
class BodyEntry extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'measured_on' => 'date',
            'weight_kg' => 'float',
            'body_fat' => 'float',
            'waist_cm' => 'float',
        ];
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
