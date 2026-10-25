<?php

namespace Database\Factories;

use App\Models\BodyEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BodyEntry> */
class BodyEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'measured_on' => fake()->unique()->dateTimeBetween('-12 weeks', 'now')->format('Y-m-d'),
            'weight_kg' => fake()->randomFloat(1, 54, 60),
            'body_fat' => fake()->optional()->randomFloat(1, 10, 18),
            'waist_cm' => fake()->optional()->randomFloat(1, 68, 76),
        ];
    }
}
