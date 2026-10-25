<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/** I due ruoli (Spatie): admin gestisce gli utenti, user si allena. */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('user');
    }
}
