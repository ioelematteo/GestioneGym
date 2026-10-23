<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabella "workouts" (le schede: Push A, Pull B, Legs...)
 */
class WorkoutModel extends Model
{
    protected $table          = 'workouts';
    protected $returnType     = 'array';
    protected $allowedFields  = ['user_id', 'nome', 'descrizione', 'colore'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true; // delete() riempie deleted_at invece di cancellare

    /**
     * Un workout, ma SOLO se appartiene a quell'utente.
     * Così nessuno può aprire i workout degli altri cambiando l'id nell'URL.
     */
    public function trovaDelUtente(int $id, int $userId): ?array
    {
        return $this->where('user_id', $userId)->find($id);
    }

    /**
     * I workout dell'utente con un riepilogo pronto per le card:
     * ultima volta che l'hai fatto, n° esercizi, gruppi muscolari, durata stimata.
     */
    public function conRiepilogo(int $userId): array
    {
        $workouts = $this->where('user_id', $userId)->orderBy('nome')->findAll();

        if ($workouts === []) {
            return [];
        }

        $ids      = array_column($workouts, 'id');
        $ultime   = model(SingoloWorkoutModel::class)->ultimaDataPerWorkout($ids);
        $durate   = model(SingoloWorkoutModel::class)->durataMediaPerWorkout($ids);
        $esercizi = model(WorkoutEsercizioModel::class)->riepilogoPerWorkout($ids);

        foreach ($workouts as &$workout) {
            $id        = $workout['id'];
            $riepilogo = $esercizi[$id] ?? ['quanti' => 0, 'serie' => 0, 'gruppi' => []];

            $workout['ultima_volta'] = $ultime[$id] ?? null;
            $workout['n_esercizi']   = $riepilogo['quanti'];
            $workout['gruppi']       = $riepilogo['gruppi'];
            // se l'hai già fatto uso la tua durata media, altrimenti stimo ~2.5 min a serie
            $workout['durata_stimata'] = $durate[$id] ?? (int) round($riepilogo['serie'] * 2.5);
        }

        return $workouts;
    }

    /**
     * Come conRiepilogo(), ma in ordine di "quale tocca adesso":
     * prima quelli mai fatti, poi quelli che non fai da più tempo.
     */
    public function prossimi(int $userId): array
    {
        $workouts = $this->conRiepilogo($userId);

        usort($workouts, static fn ($a, $b) => strcmp($a['ultima_volta'] ?? '', $b['ultima_volta'] ?? ''));

        return $workouts;
    }

    /**
     * Crea o aggiorna un workout insieme ai suoi esercizi, tutto insieme.
     * La "transazione" fa sì che se qualcosa va storto non si salva niente a metà.
     *
     * @param array $dati     ['user_id', 'nome', 'descrizione', 'colore']
     * @param array $esercizi righe già pulite dal controller
     */
    public function salvaConEsercizi(array $dati, array $esercizi, ?int $workoutId = null): int
    {
        $this->db->transStart();

        if ($workoutId === null) {
            $workoutId = (int) $this->insert($dati);
        } else {
            $this->update($workoutId, $dati);
        }

        model(WorkoutEsercizioModel::class)->sostituisci($workoutId, $esercizi);

        $this->db->transComplete();

        return $workoutId;
    }
}
