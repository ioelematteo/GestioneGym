<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "peso_corporeo"
 * Una pesata al giorno per utente (se ripesi lo stesso giorno, si aggiorna).
 */
class CreaPesoCorporeo extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'peso'       => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'data'       => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['user_id', 'data']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('peso_corporeo');
    }

    public function down(): void
    {
        $this->forge->dropTable('peso_corporeo');
    }
}
