<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "singolo_workout"
 * Ogni volta che fai un allenamento nasce UNA riga qui:
 * chi, quale workout, quando (data_esecuzione), quanto è durato, note.
 * Le serie fatte stanno nella tabella figlia "serie_eseguite".
 */
class CreaSingoloWorkout extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'workout_id'      => ['type' => 'INT', 'unsigned' => true],
            'data_esecuzione' => ['type' => 'DATETIME'],
            'durata_minuti'   => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'note'            => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        // indici = ricerche veloci per "ultimo allenamento dell'utente X sul workout Y"
        $this->forge->addKey(['user_id', 'data_esecuzione']);
        $this->forge->addKey(['workout_id', 'data_esecuzione']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('workout_id', 'workouts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('singolo_workout');
    }

    public function down(): void
    {
        $this->forge->dropTable('singolo_workout');
    }
}
