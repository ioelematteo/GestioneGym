<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Un allenamento fatto (il "singolo workout"). Le serie sono in session_sets. */
#[Fillable(['workout_id', 'performed_at', 'duration_minutes', 'notes'])]
class TrainingSession extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['performed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** withTrashed: anche se la scheda è stata eliminata, lo storico ne conosce il nome. */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class)->withTrashed();
    }

    public function sets(): HasMany
    {
        return $this->hasMany(SessionSet::class)->orderBy('id');
    }

    /** Volume = somma di kg × rep di tutte le serie. */
    public function volume(): float
    {
        return (float) $this->sets->sum(fn (SessionSet $set) => $set->weight * $set->reps);
    }
}
