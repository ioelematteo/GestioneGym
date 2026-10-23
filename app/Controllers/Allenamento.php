<?php

namespace App\Controllers;

use App\Models\PesoCorporeoModel;
use App\Models\SerieEseguitaModel;
use App\Models\SingoloWorkoutModel;
use App\Models\StatoModel;
use App\Models\WorkoutEsercizioModel;
use App\Models\WorkoutModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * WORKOUT: la pagina che usi IN PALESTRA.
 *
 * 1. index()  -> scegli quale workout stai facendo
 * 2. esegui() -> vedi esercizi e serie, con le rep dell'ultima volta come placeholder
 * 3. salva()  -> salva tutto a db: diventa "l'ultimo" e lo storico resta
 */
class Allenamento extends BaseController
{
    public function index(): string
    {
        return view('allenamento/index', [
            'titolo'   => 'Workout',
            'workouts' => model(WorkoutModel::class)->prossimi($this->userId()),
        ]);
    }

    public function esegui(int $id): string
    {
        $workout = $this->workoutDelUtente($id);
        $ultimo  = model(SingoloWorkoutModel::class)->ultimoPerWorkout($this->userId(), $id);

        return view('allenamento/esegui', [
            'titolo'   => $workout['nome'],
            'workout'  => $workout,
            'esercizi' => model(WorkoutEsercizioModel::class)->perWorkout($id),
            'stati'    => model(StatoModel::class)->elenco(),
            'ultimo'   => $ultimo,
            // [esercizio_id][numero_serie] => serie dell'ultima volta (vuoto se è la prima volta)
            'ultime'   => $ultimo ? model(SerieEseguitaModel::class)->perEsercizioESerie($ultimo['id']) : [],
        ]);
    }

    public function salva(int $id): RedirectResponse
    {
        $this->workoutDelUtente($id);

        $dati = [
            'data'          => $this->request->getPost('data') ?: date('Y-m-d'),
            'durata_minuti' => $this->request->getPost('durata_minuti'),
            'peso_corporeo' => str_replace(',', '.', (string) $this->request->getPost('peso_corporeo')),
            'note'          => trim((string) $this->request->getPost('note')),
        ];

        $regole = [
            'data'          => ['label' => 'Data', 'rules' => 'required|valid_date[Y-m-d]'],
            'durata_minuti' => ['label' => 'Durata', 'rules' => 'permit_empty|is_natural|less_than[600]'],
            'peso_corporeo' => ['label' => 'Peso corporeo', 'rules' => 'permit_empty|decimal|greater_than[20]|less_than[400]'],
            'note'          => ['label' => 'Note', 'rules' => 'permit_empty|max_length[1000]'],
        ];

        if (! $this->validateData($dati, $regole)) {
            return $this->indietroConErrori($this->validator->getErrors());
        }

        $serie = $this->serieDalForm($id);

        if ($serie === []) {
            return $this->indietroConErrori(['serie' => 'Segna almeno una serie (servono le rep) prima di salvare.']);
        }

        $singoloId = model(SingoloWorkoutModel::class)->salvaConSerie([
            'user_id'         => $this->userId(),
            'workout_id'      => $id,
            'data_esecuzione' => $this->dataEsecuzione($dati['data']),
            'durata_minuti'   => $dati['durata_minuti'] ?: null,
            'note'            => $dati['note'] ?: null,
        ], $serie);

        if ($dati['peso_corporeo'] !== '') {
            model(PesoCorporeoModel::class)->registra($this->userId(), (float) $dati['peso_corporeo'], $dati['data']);
        }

        return redirect()->to('storico/' . $singoloId)
            ->with('successo', 'Allenamento salvato. Ottimo lavoro!')
            ->with('bozza_da_cancellare', $id); // il JS cancella la bozza salvata nel telefono
    }

    /**
     * Trasforma i campi del form in righe pronte per la tabella serie_eseguite.
     *
     * Dal form arriva:  serie[esercizio_id][numero_serie][peso | rep | stato_id]
     * Le serie senza rep vengono saltate (= non le hai fatte).
     */
    private function serieDalForm(int $workoutId): array
    {
        $eserciziValidi = array_column(model(WorkoutEsercizioModel::class)->perWorkout($workoutId), 'esercizio_id');
        $statiValidi    = array_column(model(StatoModel::class)->elenco(), 'id');
        $righe          = [];

        foreach ((array) $this->request->getPost('serie') as $esercizioId => $serieEsercizio) {
            // un esercizio che non è in questo workout? lo ignoro
            if (! in_array($esercizioId, $eserciziValidi)) {
                continue;
            }

            foreach ((array) $serieEsercizio as $numero => $valori) {
                $rep = (int) ($valori['rep'] ?? 0);

                if ($rep <= 0 || $numero < 1 || $numero > 30) {
                    continue;
                }

                $peso  = (float) str_replace(',', '.', (string) ($valori['peso'] ?? 0));
                $stato = $valori['stato_id'] ?? null;

                $righe[] = [
                    'esercizio_id' => (int) $esercizioId,
                    'numero_serie' => (int) $numero,
                    'peso'         => min(max($peso, 0), 9999),
                    'rep'          => min($rep, 255),
                    'stato_id'     => in_array($stato, $statiValidi) ? (int) $stato : null,
                ];
            }
        }

        return $righe;
    }

    /**
     * Se l'allenamento è di oggi uso l'ora attuale, se è di un altro giorno metto mezzogiorno.
     * Le date nel futuro diventano oggi.
     */
    private function dataEsecuzione(string $data): string
    {
        if ($data >= date('Y-m-d')) {
            return date('Y-m-d H:i:s');
        }

        return $data . ' 12:00:00';
    }

    /**
     * Il workout se è dell'utente loggato, altrimenti pagina 404.
     */
    private function workoutDelUtente(int $id): array
    {
        return model(WorkoutModel::class)->trovaDelUtente($id, $this->userId()) ?? $this->nonTrovato();
    }
}
