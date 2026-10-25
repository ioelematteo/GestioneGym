<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Training\WorkoutPlanner;
use Illuminate\Database\Seeder;

/**
 * LE SCHEDE DI MATTEO
 * Per ogni esercizio: [nome, serie, rep, peso in kg (0 = corpo libero)]
 */
class WorkoutPlanSeeder extends Seeder
{
    public const PLANS = [
        'Allenamento A' => ['color' => 'rosso', 'exercises' => [
            ['Dip', 3, '8-10', 0],
            ['Panca inclinata', 4, '8-10', 16],
            ['Chest press', 3, '10-12', 30],
            ['Alzate laterali', 3, '12-15', 10],
            ['Shoulder press', 3, '8-10', 25],
            ['French press', 3, '10-12', 20],
        ]],
        'Allenamento B' => ['color' => 'blu', 'exercises' => [
            ['Squat', 4, '8-10', 10],
            ['Schiena', 3, '10-12', 15],
            ['Lat machine', 3, '10-12', 40],
            ['Pulley', 3, '10-12', 35],
            ['Leg extension', 3, '12-15', 20],
            ['Leg curl', 3, '12-15', 20],
            ['Curl', 3, '10-12', 7.5],
            ['Curl hammer', 3, '10-12', 10],
        ]],
    ];

    /** Uso lo stesso servizio del form "Nuova scheda" (niente factory: in produzione Faker non c'è). */
    public function run(WorkoutPlanner $planner): void
    {
        $matteo = User::where('email', 'ioelematteo@gmail.com')->firstOrFail();

        foreach (self::PLANS as $name => $plan) {
            if ($matteo->workouts()->where('name', $name)->exists()) {
                continue; // già creata: non la duplico
            }

            $rows = array_map(fn ($e) => [
                'name' => $e[0], 'sets' => $e[1], 'reps_target' => $e[2],
                'weight_target' => (float) $e[3], 'rest_seconds' => 90, 'notes' => null,
            ], $plan['exercises']);

            $planner->save($matteo, ['name' => $name, 'color' => $plan['color'], 'notes' => null], $rows);
        }
    }
}
