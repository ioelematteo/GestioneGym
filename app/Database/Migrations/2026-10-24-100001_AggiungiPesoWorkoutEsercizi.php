<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Aggiunge la colonna "peso" (peso di lavoro previsto) agli esercizi di un workout.
 * Es: Push A -> Panca piana, 4 serie, 6-8 rep, 80 kg
 */
class AggiungiPesoWorkoutEsercizi extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('workout_esercizi', [
            'peso' => ['type' => 'DECIMAL', 'constraint' => '6,2', 'null' => true, 'after' => 'rep_target'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('workout_esercizi', 'peso');
    }
}
