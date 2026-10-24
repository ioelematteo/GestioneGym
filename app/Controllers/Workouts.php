<?php

namespace App\Controllers;

use App\Models\EsercizioModel;
use App\Models\WorkoutEsercizioModel;
use App\Models\WorkoutModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CREA WORKOUT: crei (o modifichi) una scheda con i suoi esercizi.
 *
 * Il form manda gli esercizi come "array paralleli":
 *   esercizi[nome][]   = ['Panca piana', 'Squat']
 *   esercizi[serie][]  = ['4', '3']
 *   ...
 * La riga 0 di ogni array è il primo esercizio, la riga 1 il secondo, ecc.
 */
class Workouts extends BaseController
{
    public function nuovo(): string
    {
        return $this->mostraForm(null, []);
    }

    public function crea(): RedirectResponse
    {
        $errori = $this->controllaForm();

        if ($errori !== []) {
            return $this->indietroConErrori($errori);
        }

        model(WorkoutModel::class)->salvaConEsercizi($this->datiWorkout(), $this->eserciziDalForm());

        return redirect()->to('workout')->with('successo', 'Workout creato. Quando sei in palestra, sceglilo da qui.');
    }

    public function modifica(int $id): string
    {
        $workout = $this->workoutDelUtente($id);

        return $this->mostraForm($workout, model(WorkoutEsercizioModel::class)->perWorkout($id));
    }

    public function aggiorna(int $id): RedirectResponse
    {
        $this->workoutDelUtente($id);
        $errori = $this->controllaForm();

        if ($errori !== []) {
            return $this->indietroConErrori($errori);
        }

        model(WorkoutModel::class)->salvaConEsercizi($this->datiWorkout(), $this->eserciziDalForm(), $id);

        return redirect()->to('workout')->with('successo', 'Workout aggiornato.');
    }

    public function elimina(int $id): RedirectResponse
    {
        $workout = $this->workoutDelUtente($id);

        // soft delete: sparisce dalle liste, ma lo storico degli allenamenti resta
        model(WorkoutModel::class)->delete($id);

        return redirect()->to('workout')->with('successo', "«{$workout['nome']}» eliminato. I tuoi allenamenti passati restano nello storico.");
    }

    // ---------------------------------------------------------------
    // Pezzi usati dai metodi sopra
    // ---------------------------------------------------------------

    /**
     * La stessa vista serve sia per "nuovo" ($workout = null) che per "modifica".
     */
    private function mostraForm(?array $workout, array $esercizi): string
    {
        $gym = config('Gym');

        return view('workouts/form', [
            'titolo'   => $workout ? 'Modifica workout' : 'Crea workout',
            'workout'  => $workout,
            'righe'    => $this->righeDaMostrare($esercizi),
            'catalogo' => model(EsercizioModel::class)->elenco(),
            'colori'   => $gym->coloriWorkout,
            'recuperi' => $gym->recuperi,
        ]);
    }

    /**
     * Se il form è tornato indietro con un errore, rimostro quello che avevi scritto.
     * Altrimenti mostro gli esercizi salvati (o una riga vuota se il workout è nuovo).
     */
    private function righeDaMostrare(array $daDatabase): array
    {
        $vecchi = old('esercizi', null, false); // false = valori "grezzi", li escapa la vista

        if (is_array($vecchi) && isset($vecchi['nome'])) {
            return $this->zippa($vecchi);
        }

        if ($daDatabase !== []) {
            return $daDatabase;
        }

        return [['nome' => '', 'peso' => '', 'serie' => 3, 'rep_target' => '8-10', 'recupero_secondi' => 90, 'note' => '']];
    }

    /**
     * Da array paralleli a righe:
     * ['nome' => ['Panca', 'Squat'], 'serie' => [4, 3]]  ->  [['nome' => 'Panca', 'serie' => 4], ['nome' => 'Squat', 'serie' => 3]]
     */
    private function zippa(array $colonne): array
    {
        $righe = [];

        foreach ($colonne['nome'] ?? [] as $i => $nome) {
            $righe[] = [
                'nome'             => trim((string) $nome),
                'serie'            => $colonne['serie'][$i] ?? 3,
                'rep_target'       => trim((string) ($colonne['rep_target'][$i] ?? '')),
                'peso'             => str_replace(',', '.', trim((string) ($colonne['peso'][$i] ?? ''))),
                'recupero_secondi' => $colonne['recupero'][$i] ?? 90,
                'note'             => trim((string) ($colonne['note'][$i] ?? '')),
            ];
        }

        return $righe;
    }

    /**
     * Controlla il form. Ritorna gli errori ([] = tutto ok).
     */
    private function controllaForm(): array
    {
        $regole = [
            'nome'        => ['label' => 'Nome', 'rules' => 'required|max_length[100]'],
            'descrizione' => ['label' => 'Descrizione', 'rules' => 'permit_empty|max_length[255]'],
            'colore'      => ['label' => 'Colore', 'rules' => 'required|in_list[' . implode(',', array_keys(config('Gym')->coloriWorkout)) . ']'],
        ];

        $errori = $this->validate($regole) ? [] : $this->validator->getErrors();
        $righe  = array_filter($this->zippa((array) $this->request->getPost('esercizi')), static fn ($riga) => $riga['nome'] !== '');

        if ($righe === []) {
            $errori['esercizi'] = 'Aggiungi almeno un esercizio.';
        }

        // stesso esercizio scritto due volte? (senza guardare maiuscole/minuscole)
        $nomi     = array_map('mb_strtolower', array_column($righe, 'nome'));
        $doppioni = array_unique(array_diff_assoc($nomi, array_unique($nomi)));

        if ($doppioni !== []) {
            $errori['esercizi'] = 'Hai inserito due volte: ' . implode(', ', $doppioni) . '. Aumenta le serie invece di ripeterlo.';
        }

        foreach ($righe as $riga) {
            if ($riga['peso'] !== '' && ! is_numeric($riga['peso'])) {
                $errori['esercizi'] = "Il peso di «{$riga['nome']}» deve essere un numero (es. 82.5).";
            }

            if (mb_strlen($riga['nome']) > 100 || mb_strlen($riga['note']) > 255 || mb_strlen($riga['rep_target']) > 20) {
                $errori['esercizi'] = 'Uno degli esercizi ha un testo troppo lungo.';
            }
        }

        return $errori;
    }

    private function datiWorkout(): array
    {
        return [
            'user_id'     => $this->userId(),
            'nome'        => trim((string) $this->request->getPost('nome')),
            'descrizione' => trim((string) $this->request->getPost('descrizione')) ?: null,
            'colore'      => $this->request->getPost('colore'),
        ];
    }

    /**
     * Le righe degli esercizi pronte per il database.
     * Se un esercizio non esiste ancora nel catalogo, lo crea.
     */
    private function eserciziDalForm(): array
    {
        $recuperi = config('Gym')->recuperi;
        $righe    = [];

        foreach ($this->zippa((array) $this->request->getPost('esercizi')) as $riga) {
            if ($riga['nome'] === '') {
                continue; // riga lasciata vuota
            }

            $righe[] = [
                'esercizio_id'     => model(EsercizioModel::class)->trovaOCrea($riga['nome'], null),
                'serie'            => min(max((int) $riga['serie'], 1), 10),
                'rep_target'       => $riga['rep_target'] ?: null,
                'peso'             => is_numeric($riga['peso']) ? min(max((float) $riga['peso'], 0), 9999) : null,
                'recupero_secondi' => in_array((int) $riga['recupero_secondi'], $recuperi, true) ? (int) $riga['recupero_secondi'] : 90,
                'note'             => $riga['note'] ?: null,
            ];
        }

        return $righe;
    }

    private function workoutDelUtente(int $id): array
    {
        return model(WorkoutModel::class)->trovaDelUtente($id, $this->userId()) ?? $this->nonTrovato();
    }
}
