<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabella "singolo_workout" (ogni allenamento che hai fatto)
 */
class SingoloWorkoutModel extends Model
{
    protected $table         = 'singolo_workout';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'workout_id', 'data_esecuzione', 'durata_minuti', 'note'];
    protected $useTimestamps = true;

    /**
     * L'ULTIMO allenamento registrato per quel workout (il più recente per data).
     * Da qui prendiamo le rep e i pesi da mostrare come "placeholder".
     */
    public function ultimoPerWorkout(int $userId, int $workoutId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('workout_id', $workoutId)
            ->orderBy('data_esecuzione', 'DESC')
            ->orderBy('id', 'DESC') // stessa data? vince l'ultimo salvato
            ->first();
    }

    /**
     * Un allenamento con nome e colore del workout, solo se è dell'utente.
     */
    public function trovaDelUtente(int $id, int $userId): ?array
    {
        return $this->conWorkout()
            ->where('singolo_workout.user_id', $userId)
            ->where('singolo_workout.id', $id)
            ->first();
    }

    /**
     * Aggiunge alla query il nome e il colore del workout.
     * Ritorna $this, così puoi continuare la catena: ->conWorkout()->where(...)->findAll()
     * (join "a mano" = vede anche i workout eliminati, così lo storico ha sempre il nome)
     */
    public function conWorkout(): self
    {
        return $this->select('singolo_workout.*, workouts.nome AS workout_nome, workouts.colore AS workout_colore')
            ->join('workouts', 'workouts.id = singolo_workout.workout_id');
    }

    /**
     * Gli ultimi N allenamenti, già con il volume totale (kg sollevati).
     */
    public function ultimi(int $userId, int $limite = 5): array
    {
        $allenamenti = $this->conWorkout()
            ->where('singolo_workout.user_id', $userId)
            ->orderBy('data_esecuzione', 'DESC')
            ->findAll($limite);

        return $this->aggiungiVolume($allenamenti);
    }

    /**
     * Aggiunge 'volume' e 'n_serie' a ogni allenamento della lista.
     */
    public function aggiungiVolume(array $allenamenti): array
    {
        if ($allenamenti === []) {
            return [];
        }

        $totali = model(SerieEseguitaModel::class)->totaliPerAllenamento(array_column($allenamenti, 'id'));

        foreach ($allenamenti as &$allenamento) {
            $allenamento['volume']  = $totali[$allenamento['id']]['volume'] ?? 0;
            $allenamento['n_serie'] = $totali[$allenamento['id']]['n_serie'] ?? 0;
        }

        return $allenamenti;
    }

    /**
     * Le date (Y-m-d) di tutti gli allenamenti in un periodo.
     * Servono per contare la frequenza per settimana / mese.
     */
    public function dateTra(int $userId, string $da, string $a): array
    {
        $righe = $this->select('data_esecuzione')
            ->where('user_id', $userId)
            ->where('data_esecuzione >=', $da)
            ->where('data_esecuzione <', $a)
            ->orderBy('data_esecuzione')
            ->findAll();

        return array_map(static fn ($riga) => substr($riga['data_esecuzione'], 0, 10), $righe);
    }

    /**
     * Per ogni workout la data dell'ultima volta: [workout_id => '2026-10-20 18:30:00']
     */
    public function ultimaDataPerWorkout(array $workoutIds): array
    {
        $righe = $this->select('workout_id')
            ->selectMax('data_esecuzione', 'ultima')
            ->whereIn('workout_id', $workoutIds)
            ->groupBy('workout_id')
            ->findAll();

        return array_column($righe, 'ultima', 'workout_id');
    }

    /**
     * Per ogni workout la durata media in minuti: [workout_id => 64]
     */
    public function durataMediaPerWorkout(array $workoutIds): array
    {
        $righe = $this->select('workout_id')
            ->selectAvg('durata_minuti', 'media')
            ->whereIn('workout_id', $workoutIds)
            ->where('durata_minuti IS NOT NULL')
            ->groupBy('workout_id')
            ->findAll();

        $durate = [];
        foreach ($righe as $riga) {
            $durate[$riga['workout_id']] = (int) round((float) $riga['media']);
        }

        return $durate;
    }

    /**
     * Quanti allenamenti ha fatto ogni utente: [user_id => 36] (pagina admin)
     */
    public function contaPerUtente(): array
    {
        $righe = $this->select('user_id')
            ->selectCount('id', 'totale')
            ->groupBy('user_id')
            ->findAll();

        return array_column($righe, 'totale', 'user_id');
    }

    /**
     * Salva l'allenamento E tutte le sue serie in un colpo solo (transazione).
     * Ritorna l'id del nuovo allenamento.
     */
    public function salvaConSerie(array $allenamento, array $serie): int
    {
        $this->db->transStart();

        $id = (int) $this->insert($allenamento);

        foreach ($serie as $riga) {
            model(SerieEseguitaModel::class)->insert($riga + ['singolo_workout_id' => $id]);
        }

        $this->db->transComplete();

        return $id;
    }
}
