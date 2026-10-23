<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "workout_esercizi"
 * Quali esercizi ci sono dentro un workout, in che ordine e con che obiettivo.
 * Es: Push A -> 1) Panca piana, 4 serie, 6-8 rep, 120s di recupero
 */
class CreaWorkoutEsercizi extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'workout_id'       => ['type' => 'INT', 'unsigned' => true],
            'esercizio_id'     => ['type' => 'INT', 'unsigned' => true],
            'ordine'           => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'serie'            => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 3],
            'rep_target'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'recupero_secondi' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 90],
            'note'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('workout_id');
        $this->forge->addForeignKey('workout_id', 'workouts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('esercizio_id', 'esercizi', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('workout_esercizi');
    }

    public function down(): void
    {
        $this->forge->dropTable('workout_esercizi');
    }
}
