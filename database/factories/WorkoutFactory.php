<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Workout> */
class WorkoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Allenamento '.fake()->randomElement(['A', 'B', 'C']),
            'color' => fake()->randomElement(array_keys(config('gymlog.colors'))),
        ];
    }

    /**
     * Aggiunge esercizi alla scheda.
     * Uso: Workout::factory()->withExercises([['Squat', 4, '8-10', 10.0]])->create()
     *
     * @param  list<array{0: string, 1: int, 2: string, 3: float|null}>  $rows  [nome, serie, rep, peso]
     */
    public function withExercises(array $rows): static
    {
        return $this->afterCreating(function (Workout $workout) use ($rows) {
            foreach ($rows as $i => [$name, $sets, $reps, $weight]) {
                $workout->items()->create([
                    'exercise_id' => \App\Models\Exercise::findOrCreateByName($name)->id,
                    'position' => $i + 1,
                    'sets' => $sets,
                    'reps_target' => $reps,
                    'weight_target' => $weight,
                    'rest_seconds' => 90,
                ]);
            }
        });
    }
}
