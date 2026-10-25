<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Exercise> */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->randomElement([
            'Panca piana', 'Squat', 'Stacco', 'Lat machine', 'Pulley', 'Curl', 'Leg press', 'Military press',
        ]).' '.fake()->unique()->numberBetween(1, 9999)];
    }
}
