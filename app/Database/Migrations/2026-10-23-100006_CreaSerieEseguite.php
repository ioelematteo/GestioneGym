<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "serie_eseguite"
 * Ogni serie che hai fatto: peso, ripetizioni ed eventuale stato.
 * Es: allenamento #12 -> Panca piana, serie 2, 80 kg x 6, stato "Spottato"
 */
class CreaSerieEseguite extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'singolo_workout_id' => ['type' => 'INT', 'unsigned' => true],
            'esercizio_id'       => ['type' => 'INT', 'unsigned' => true],
            'numero_serie'       => ['type' => 'TINYINT', 'unsigned' => true],
            'peso'               => ['type' => 'DECIMAL', 'constraint' => '6,2', 'default' => 0],
            'rep'                => ['type' => 'TINYINT', 'unsigned' => true],
            'stato_id'           => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('singolo_workout_id');
        $this->forge->addKey('esercizio_id');
        $this->forge->addForeignKey('singolo_workout_id', 'singolo_workout', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('esercizio_id', 'esercizi', 'id', 'CASCADE', 'RESTRICT');
        // se un giorno cancelli uno stato, le serie restano ma senza stato
        $this->forge->addForeignKey('stato_id', 'stati', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('serie_eseguite');
    }

    public function down(): void
    {
        $this->forge->dropTable('serie_eseguite');
    }
}
