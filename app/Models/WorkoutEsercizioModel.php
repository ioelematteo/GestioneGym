<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabella "workout_esercizi" (gli esercizi dentro un workout)
 */
class WorkoutEsercizioModel extends Model
{
    protected $table         = 'workout_esercizi';
    protected $returnType    = 'array';
    protected $allowedFields = ['workout_id', 'esercizio_id', 'ordine', 'serie', 'rep_target', 'peso', 'recupero_secondi', 'note'];

    /**
     * Gli esercizi di un workout, in ordine, con nome e gruppo muscolare.
     */
    public function perWorkout(int $workoutId): array
    {
        return $this->select('workout_esercizi.*, esercizi.nome, esercizi.gruppo_muscolare')
            ->join('esercizi', 'esercizi.id = workout_esercizi.esercizio_id')
            ->where('workout_id', $workoutId)
            ->orderBy('ordine')
            ->findAll();
    }

    /**
     * Per ogni workout: quanti esercizi, quante serie totali e quali gruppi muscolari.
     * Ritorna [workout_id => ['quanti' => 6, 'serie' => 20, 'gruppi' => ['Petto', ...]]]
     */
    public function riepilogoPerWorkout(array $workoutIds): array
    {
        $righe = $this->select('workout_esercizi.workout_id, workout_esercizi.serie, esercizi.gruppo_muscolare')
            ->join('esercizi', 'esercizi.id = workout_esercizi.esercizio_id')
            ->whereIn('workout_id', $workoutIds)
            ->orderBy('ordine')
            ->findAll();

        $riepilogo = [];

        foreach ($righe as $riga) {
            $id = $riga['workout_id'];
            $riepilogo[$id] ??= ['quanti' => 0, 'serie' => 0, 'gruppi' => []];

            $riepilogo[$id]['quanti']++;
            $riepilogo[$id]['serie'] += (int) $riga['serie'];

            $gruppo = $riga['gruppo_muscolare'];
            if ($gruppo && ! in_array($gruppo, $riepilogo[$id]['gruppi'], true)) {
                $riepilogo[$id]['gruppi'][] = $gruppo;
            }
        }

        return $riepilogo;
    }

    /**
     * Butta via gli esercizi vecchi del workout e salva quelli nuovi.
     * (Lo storico NON si rompe: le serie fatte puntano all'esercizio, non a questa tabella.)
     */
    public function sostituisci(int $workoutId, array $esercizi): void
    {
        $this->where('workout_id', $workoutId)->delete();

        foreach ($esercizi as $ordine => $esercizio) {
            $this->insert($esercizio + [
                'workout_id' => $workoutId,
                'ordine'     => $ordine + 1,
            ]);
        }
    }
}
