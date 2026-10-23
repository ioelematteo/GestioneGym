<?php

namespace App\Models;

use App\Libraries\Statistiche;
use CodeIgniter\Model;

/**
 * Tabella "serie_eseguite" (ogni serie fatta: peso, rep, stato)
 */
class SerieEseguitaModel extends Model
{
    protected $table         = 'serie_eseguite';
    protected $returnType    = 'array';
    protected $allowedFields = ['singolo_workout_id', 'esercizio_id', 'numero_serie', 'peso', 'rep', 'stato_id'];

    /**
     * Le serie di un allenamento, con il nome dell'esercizio.
     */
    public function perAllenamento(int $singoloWorkoutId): array
    {
        return $this->select('serie_eseguite.*, esercizi.nome AS esercizio_nome, esercizi.gruppo_muscolare')
            ->join('esercizi', 'esercizi.id = serie_eseguite.esercizio_id')
            ->where('singolo_workout_id', $singoloWorkoutId)
            ->orderBy('serie_eseguite.id')
            ->findAll();
    }

    /**
     * Le serie di un allenamento organizzate così:
     * [esercizio_id => [numero_serie => ['peso' => 80, 'rep' => 6, 'stato_id' => null]]]
     * È la forma comoda per mostrare i placeholder nella pagina Workout.
     */
    public function perEsercizioESerie(int $singoloWorkoutId): array
    {
        $risultato = [];

        foreach ($this->perAllenamento($singoloWorkoutId) as $serie) {
            $risultato[$serie['esercizio_id']][$serie['numero_serie']] = $serie;
        }

        return $risultato;
    }

    /**
     * Volume (somma di peso x rep) e numero di serie per ogni allenamento.
     * Ritorna [singolo_workout_id => ['volume' => 8400, 'n_serie' => 20]]
     */
    public function totaliPerAllenamento(array $singoloWorkoutIds): array
    {
        $righe = $this->select('singolo_workout_id, SUM(peso * rep) AS volume, COUNT(id) AS n_serie')
            ->whereIn('singolo_workout_id', $singoloWorkoutIds)
            ->groupBy('singolo_workout_id')
            ->findAll();

        $totali = [];
        foreach ($righe as $riga) {
            $totali[$riga['singolo_workout_id']] = [
                'volume'  => (float) $riga['volume'],
                'n_serie' => (int) $riga['n_serie'],
            ];
        }

        return $totali;
    }

    /**
     * Volume di ogni allenamento in un periodo, in ordine di data.
     * Ritorna [['data' => '2026-10-20', 'volume' => 8400], ...]
     */
    public function volumiTra(int $userId, string $da, string $a): array
    {
        $righe = $this->select('singolo_workout.data_esecuzione, SUM(serie_eseguite.peso * serie_eseguite.rep) AS volume')
            ->join('singolo_workout', 'singolo_workout.id = serie_eseguite.singolo_workout_id')
            ->where('singolo_workout.user_id', $userId)
            ->where('singolo_workout.data_esecuzione >=', $da)
            ->where('singolo_workout.data_esecuzione <', $a)
            ->groupBy('singolo_workout.id, singolo_workout.data_esecuzione')
            ->orderBy('singolo_workout.data_esecuzione')
            ->findAll();

        return array_map(static fn ($riga) => [
            'data'   => substr($riga['data_esecuzione'], 0, 10),
            'volume' => (float) $riga['volume'],
        ], $righe);
    }

    /**
     * Tutte le serie dell'utente da una data in poi, dalla più recente.
     * Servono per calcolare i "Progressi principali".
     */
    public function recentiPerUtente(int $userId, string $da): array
    {
        return $this->select('serie_eseguite.*, esercizi.nome AS esercizio_nome, singolo_workout.data_esecuzione')
            ->join('singolo_workout', 'singolo_workout.id = serie_eseguite.singolo_workout_id')
            ->join('esercizi', 'esercizi.id = serie_eseguite.esercizio_id')
            ->where('singolo_workout.user_id', $userId)
            ->where('singolo_workout.data_esecuzione >=', $da)
            ->orderBy('singolo_workout.data_esecuzione', 'DESC')
            ->findAll();
    }

    /**
     * Per ogni esercizio, il miglior "punteggio" fatto PRIMA di una certa data.
     * Ritorna [esercizio_id => 106.7]  (serve per capire se oggi hai fatto un record)
     */
    public function miglioriPunteggiPrima(int $userId, array $esercizioIds, string $primaDi): array
    {
        if ($esercizioIds === []) {
            return [];
        }

        $righe = $this->select('serie_eseguite.esercizio_id, serie_eseguite.peso, serie_eseguite.rep')
            ->join('singolo_workout', 'singolo_workout.id = serie_eseguite.singolo_workout_id')
            ->where('singolo_workout.user_id', $userId)
            ->where('singolo_workout.data_esecuzione <', $primaDi)
            ->whereIn('serie_eseguite.esercizio_id', $esercizioIds)
            ->findAll();

        $migliori = [];

        foreach ($righe as $riga) {
            $punteggio = Statistiche::punteggioSerie((float) $riga['peso'], (int) $riga['rep']);
            $id        = $riga['esercizio_id'];

            $migliori[$id] = max($migliori[$id] ?? 0, $punteggio);
        }

        return $migliori;
    }
}
