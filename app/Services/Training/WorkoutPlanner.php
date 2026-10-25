<?php

namespace App\Services\Training;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Facades\DB;

/**
 * CREA / MODIFICA UNA SCHEDA con i suoi esercizi.
 * In modifica le righe vecchie vengono sostituite: lo storico non si rompe
 * perché le serie fatte puntano all'esercizio, non alla riga della scheda.
 */
class WorkoutPlanner
{
    /**
     * @param  array{name: string, color: string, notes: ?string}  $data
     * @param  list<array{name: string, sets: int, reps_target: ?string, weight_target: ?float, rest_seconds: int, notes: ?string}>  $rows
     */
    public function save(User $user, array $data, array $rows, ?Workout $workout = null): Workout
    {
        return DB::transaction(function () use ($user, $data, $rows, $workout) {
            $workout ??= $user->workouts()->make();
            $workout->fill($data)->save();

            $workout->items()->delete();

            foreach (array_values($rows) as $i => $row) {
                $workout->items()->create([
                    'exercise_id' => Exercise::findOrCreateByName($row['name'])->id,
                    'position' => $i + 1,
                    'sets' => $row['sets'],
                    'reps_target' => $row['reps_target'],
                    'weight_target' => $row['weight_target'],
                    'rest_seconds' => $row['rest_seconds'],
                    'notes' => $row['notes'],
                ]);
            }

            return $workout;
        });
    }
}
