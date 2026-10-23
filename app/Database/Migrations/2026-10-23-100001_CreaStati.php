<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TABELLA "stati"
 * Le etichette che puoi mettere a una serie: "Ultime kippate", "Spottato", ...
 */
class CreaStati extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'descrizione' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'colore'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'grigio'],
            'ordine'      => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('stati');
    }

    public function down(): void
    {
        $this->forge->dropTable('stati');
    }
}
