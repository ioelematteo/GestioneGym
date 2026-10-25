<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Una foto dei progressi. Il file vero sta sul disco (vedi App\Services\Body\PhotoStorage). */
#[Fillable(['taken_on', 'pose', 'path', 'notes'])]
class ProgressPhoto extends Model
{
    use HasFactory;

    public const POSES = ['fronte', 'lato', 'retro'];

    protected function casts(): array
    {
        return ['taken_on' => 'date'];
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
