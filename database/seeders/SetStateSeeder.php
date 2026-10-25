<?php

namespace Database\Seeders;

use App\Models\SetState;
use Illuminate\Database\Seeder;

/** Gli stati che puoi mettere a una serie. Per aggiungerne uno: una riga qui + db:seed. */
class SetStateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['Ultime kippate', 'Le ultime rep con slancio', 'giallo'],
            ['Ultima mezza rep', "L'ultima rep non è completa", 'blu'],
            ['Spottato', 'Aiutato da qualcuno nelle ultime rep', 'verde'],
            ['Cedimento', 'Arrivato a cedimento', 'rosso'],
            ['Rest-pause', 'Piccole pause dentro la serie', 'grigio'],
            ['Fastidio', 'Dolore o fastidio durante la serie', 'rosso'],
        ];

        foreach ($states as $position => [$name, $description, $color]) {
            SetState::updateOrCreate(['name' => $name], [
                'description' => $description,
                'color' => $color,
                'position' => $position + 1,
            ]);
        }
    }
}
