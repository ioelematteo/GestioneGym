<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "workouts"
 * La "scheda": Push A, Pull B, Legs... Ogni workout appartiene a un utente.
 * deleted_at = soft delete: se elimini un workout non sparisce davvero,
 * così lo storico continua a sapere come si chiamava.
 */
class CreaWorkouts extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'descrizione' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'colore'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'verde'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id');
        // se cancello l'utente, cancello anche i suoi workout
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('workouts');
    }

    public function down(): void
    {
        $this->forge->dropTable('workouts');
    }
}
