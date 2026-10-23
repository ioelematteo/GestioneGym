<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Gli stati che puoi mettere a una serie.
 * Vuoi aggiungerne uno? Aggiungi una riga qui e rilancia il seeder.
 */
class StatiSeeder extends Seeder
{
    public function run(): void
    {
        $stati = [
            ['nome' => 'Ultime kippate',   'descrizione' => 'Le ultime rep con slancio', 'colore' => 'ambra'],
            ['nome' => 'Ultima mezza rep', 'descrizione' => 'L\'ultima rep non è completa', 'colore' => 'blu'],
            ['nome' => 'Spottato',         'descrizione' => 'Aiutato da qualcuno nelle ultime rep', 'colore' => 'viola'],
            ['nome' => 'Cedimento',        'descrizione' => 'Arrivato a cedimento muscolare', 'colore' => 'rosso'],
            ['nome' => 'Rest-pause',       'descrizione' => 'Piccole pause dentro la serie', 'colore' => 'ciano'],
            ['nome' => 'Fastidio',         'descrizione' => 'Dolore o fastidio durante la serie', 'colore' => 'rosa'],
        ];

        foreach ($stati as $ordine => $stato) {
            // se esiste già uno stato con quel nome, non lo duplico
            $esiste = $this->db->table('stati')->where('nome', $stato['nome'])->countAllResults();

            if ($esiste === 0) {
                $this->db->table('stati')->insert($stato + ['ordine' => $ordine + 1]);
            }
        }
    }
}
