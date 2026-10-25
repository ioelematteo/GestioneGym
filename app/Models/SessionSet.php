<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Una serie fatta: esercizio, numero, peso di QUESTA serie, rep, stato. */
#[Fillable(['exercise_id', 'set_number', 'weight', 'reps', 'set_state_id'])]
class SessionSet extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return ['weight' => 'float', 'reps' => 'integer'];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(SetState::class, 'set_state_id');
    }
}
