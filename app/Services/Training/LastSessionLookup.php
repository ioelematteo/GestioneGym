<?php

namespace App\Services\Training;

use App\Models\SessionSet;
use App\Models\TrainingSession;
use App\Models\User;
use App\Models\Workout;

/**
 * L'ULTIMO ALLENAMENTO di una scheda: da qui vengono i "numeri fantasma"
 * (peso e rep dell'ultima volta) mostrati sotto ogni serie.
 */
class LastSessionLookup
{
    /** Il più recente per data (a parità, l'ultimo salvato). */
    public function forWorkout(User $user, Workout $workout): ?TrainingSession
    {
        return $user->trainingSessions()
            ->where('workout_id', $workout->id)
            ->latest('performed_at')
            ->latest('id')
            ->first();
    }

    /**
     * Le serie organizzate per la vista: [exercise_id][set_number] => SessionSet
     *
     * @return array<int, array<int, SessionSet>>
     */
    public function setsByExercise(?TrainingSession $session): array
    {
        if ($session === null) {
            return [];
        }

        $grid = [];
        foreach ($session->sets()->with('state')->get() as $set) {
            $grid[$set->exercise_id][$set->set_number] = $set;
        }

        return $grid;
    }
}
