<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** La scheda: "Allenamento A", "Allenamento B"... */
#[Fillable(['name', 'color', 'notes'])]
class Workout extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Gli esercizi della scheda, già in ordine. */
    public function items(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class)->orderBy('position');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    /** La lettera nel badge: "Allenamento A" -> "A", "Legs" -> "L". */
    public function initial(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        $last = end($words);

        return mb_strtoupper(count($words) > 1 && mb_strlen($last) <= 2 ? $last : mb_substr($this->name, 0, 1));
    }
}
