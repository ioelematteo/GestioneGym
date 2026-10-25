<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Una riga della scheda: esercizio + serie + rep + peso previsto + recupero. */
#[Fillable(['exercise_id', 'position', 'sets', 'reps_target', 'weight_target', 'rest_seconds', 'notes'])]
class WorkoutExercise extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return ['weight_target' => 'float'];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
