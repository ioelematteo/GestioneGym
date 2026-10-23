<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabella "esercizi" (il catalogo condiviso)
 */
class EsercizioModel extends Model
{
    protected $table         = 'esercizi';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'gruppo_muscolare'];
    protected $useTimestamps = true; // riempie da solo created_at e updated_at

    /**
     * Tutti gli esercizi in ordine alfabetico (per i suggerimenti nel form).
     */
    public function elenco(): array
    {
        return $this->orderBy('nome')->findAll();
    }

    /**
     * Cerca l'esercizio per nome. Se non c'è, lo crea.
     * Ritorna sempre l'id.
     */
    public function trovaOCrea(string $nome, ?string $gruppo): int
    {
        $nome      = trim($nome);
        $esistente = $this->where('nome', $nome)->first();

        if ($esistente !== null) {
            return (int) $esistente['id'];
        }

        return (int) $this->insert([
            'nome'             => $nome,
            'gruppo_muscolare' => $gruppo ?: null,
        ]);
    }
}
