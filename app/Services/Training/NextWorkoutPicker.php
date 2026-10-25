<?php

namespace App\Services\Training;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Collection;

/**
 * QUALE SCHEDA TOCCA ADESSO: prima quelle mai fatte, poi quella che non fai da più tempo.
 * Aggiunge a ogni scheda: last_done_at, avg_minutes (durata media o stima).
 */
class NextWorkoutPicker
{
    /** @return Collection<int, Workout> */
    public function ordered(User $user): Collection
    {
        return $user->workouts()
            ->withCount('items')
            ->withSum('items', 'sets')
            ->withMax('sessions as last_done_at', 'performed_at')
            ->withAvg('sessions as avg_minutes', 'duration_minutes')
            ->get()
            ->each(function (Workout $workout) {
                // mai fatta? stimo 2.5 minuti a serie
                $workout->avg_minutes = (int) round($workout->avg_minutes ?? ($workout->items_sum_sets ?? 0) * 2.5);
            })
            ->sortBy(fn (Workout $workout) => $workout->last_done_at ?? '')
            ->values();
    }
}
