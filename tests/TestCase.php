<?php

namespace Tests;

use App\Models\User;
use App\Models\Workout;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SetStateSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // i test non hanno bisogno di CSS/JS compilati
    }

    /** Ruoli e stati servono quasi sempre: li creo prima di ogni test che usa il database. */
    protected function seedBasics(): void
    {
        $this->seed([RoleSeeder::class, SetStateSeeder::class]);
    }

    protected function athlete(string $role = 'user'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    /** Una scheda con due esercizi: Squat 4×8-10 10kg, Curl 3×10-12 7.5kg */
    protected function workoutFor(User $user): Workout
    {
        return Workout::factory()->for($user)
            ->withExercises([['Squat', 4, '8-10', 10.0], ['Curl', 3, '10-12', 7.5]])
            ->create(['name' => 'Allenamento B', 'color' => 'blu']);
    }
}
