<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * php artisan db:seed            -> ruoli, stati, i 2 utenti, le schede A e B di Matteo
 * php artisan db:seed --class=DemoSeeder   -> IN PIÙ: 10 settimane di allenamenti e pesate finte
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SetStateSeeder::class,
            UserSeeder::class,
            WorkoutPlanSeeder::class,
        ]);
    }
}
