<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * IL SEEDER PRINCIPALE
 * Lancialo con: php spark db:seed DatabaseSeeder
 * Riempie le tabelle "fisse" e crea i due utenti.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('StatiSeeder');
        $this->call('EserciziSeeder');
        $this->call('UtentiSeeder');
    }
}
