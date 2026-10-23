<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabella "stati" (Ultime kippate, Spottato, ...)
 */
class StatoModel extends Model
{
    protected $table         = 'stati';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'descrizione', 'colore', 'ordine'];

    /**
     * Tutti gli stati, nell'ordine giusto per la tendina.
     */
    public function elenco(): array
    {
        return $this->orderBy('ordine')->findAll();
    }

    /**
     * Gli stati indicizzati per id: [3 => ['nome' => 'Spottato', ...]]
     * Comodo nelle viste per fare $stati[$serie['stato_id']].
     */
    public function perId(): array
    {
        return array_column($this->elenco(), null, 'id');
    }
}
