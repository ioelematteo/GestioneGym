<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Una domanda fatta al coach IA e la sua risposta. */
#[Fillable(['question', 'answer', 'model'])]
class CoachReport extends Model
{

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
