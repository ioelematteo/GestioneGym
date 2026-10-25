<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** I due utenti iniziali. Cambia le password dopo il primo accesso (Profilo). */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->user('Matteo', 'ioelematteo@gmail.com', 'Matteo2026!', 'admin');
        $this->user('Utente', 'user@test.it', 'User2026!', 'user');
    }

    private function user(string $name, string $email, string $password, string $role): void
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'password' => $password, // il cast 'hashed' del model la cripta da solo
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);
    }
}
