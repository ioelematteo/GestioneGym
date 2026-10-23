<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabella "peso_corporeo" (le tue pesate)
 */
class PesoCorporeoModel extends Model
{
    protected $table         = 'peso_corporeo';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'peso', 'data'];
    protected $useTimestamps = true;

    /**
     * Salva la pesata del giorno. Se quel giorno c'è già, la aggiorna.
     */
    public function registra(int $userId, float $peso, string $data): void
    {
        $esistente = $this->where('user_id', $userId)->where('data', $data)->first();

        if ($esistente !== null) {
            $this->update($esistente['id'], ['peso' => $peso]);

            return;
        }

        $this->insert(['user_id' => $userId, 'peso' => $peso, 'data' => $data]);
    }

    /**
     * Le pesate da una data in poi, dalla più vecchia alla più nuova.
     */
    public function andamento(int $userId, string $da): array
    {
        return $this->where('user_id', $userId)
            ->where('data >=', $da)
            ->orderBy('data')
            ->findAll();
    }

    /**
     * Le ultime N pesate (dalla più vecchia alla più nuova), a prescindere dalla data.
     */
    public function ultime(int $userId, int $quante): array
    {
        $righe = $this->where('user_id', $userId)
            ->orderBy('data', 'DESC')
            ->findAll($quante);

        return array_reverse($righe);
    }
}
