<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Un catalogo base di esercizi, così quando crei un workout
 * hai già i suggerimenti. Quelli nuovi li crei direttamente dal form.
 */
class EserciziSeeder extends Seeder
{
    public function run(): void
    {
        $esercizi = [
            'Petto'        => ['Panca piana', 'Panca inclinata manubri', 'Croci ai cavi', 'Dip alle parallele'],
            'Schiena'      => ['Trazioni', 'Rematore bilanciere', 'Lat machine', 'Pulley basso', 'Stacco da terra'],
            'Spalle'       => ['Military press', 'Alzate laterali', 'Face pull'],
            'Bicipiti'     => ['Curl bilanciere', 'Curl manubri a martello'],
            'Tricipiti'    => ['French press', 'Pushdown ai cavi'],
            'Quadricipiti' => ['Squat', 'Leg press', 'Affondi bulgari', 'Leg extension'],
            'Femorali'     => ['Stacco rumeno', 'Leg curl'],
            'Glutei'       => ['Hip thrust'],
            'Polpacci'     => ['Calf raise in piedi'],
            'Addome'       => ['Crunch ai cavi', 'Plank'],
        ];

        $adesso = date('Y-m-d H:i:s');

        foreach ($esercizi as $gruppo => $nomi) {
            foreach ($nomi as $nome) {
                $esiste = $this->db->table('esercizi')->where('nome', $nome)->countAllResults();

                if ($esiste === 0) {
                    $this->db->table('esercizi')->insert([
                        'nome'             => $nome,
                        'gruppo_muscolare' => $gruppo,
                        'created_at'       => $adesso,
                        'updated_at'       => $adesso,
                    ]);
                }
            }
        }
    }
}
