<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "esercizi"
 * Il catalogo di tutti gli esercizi (Panca piana, Squat, ...).
 * È condiviso tra gli utenti: così "Panca piana" è sempre lo stesso esercizio
 * e possiamo confrontare i progressi anche se lo fai in workout diversi.
 */
class CreaEsercizi extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'gruppo_muscolare' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nome');
        $this->forge->createTable('esercizi');
    }

    public function down(): void
    {
        $this->forge->dropTable('esercizi');
    }
}
