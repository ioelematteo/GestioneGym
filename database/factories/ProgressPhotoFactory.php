<?php

namespace Database\Factories;

use App\Models\ProgressPhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProgressPhoto> */
class ProgressPhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'taken_on' => fake()->dateTimeBetween('-12 weeks', 'now')->format('Y-m-d'),
            'pose' => fake()->randomElement(ProgressPhoto::POSES),
            'path' => 'progress-photos/finta.jpg',
        ];
    }
}
