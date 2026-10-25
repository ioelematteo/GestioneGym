<?php

namespace Database\Factories;

use App\Models\TrainingSession;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TrainingSession> */
class TrainingSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'user_id' => fn (array $attributes) => Workout::find($attributes['workout_id'])->user_id,
            'performed_at' => fake()->dateTimeBetween('-8 weeks', 'now'),
            'duration_minutes' => fake()->numberBetween(50, 85),
        ];
    }
}
