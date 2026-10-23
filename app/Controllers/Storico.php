<?php

namespace App\Controllers;

use App\Libraries\Statistiche;
use App\Models\SerieEseguitaModel;
use App\Models\SingoloWorkoutModel;
use App\Models\StatoModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * STORICO: tutti gli allenamenti fatti, dal più recente.
 */
class Storico extends BaseController
{
    public function index(): string
    {
        $model = model(SingoloWorkoutModel::class);

        // paginate(12) = 12 allenamenti per pagina; legge da solo ?page=2 dall'URL
        $allenamenti = $model->conWorkout()
            ->where('singolo_workout.user_id', $this->userId())
            ->orderBy('data_esecuzione', 'DESC')
            ->paginate(12);

        return view('storico/index', [
            'titolo'      => 'Storico',
            'allenamenti' => $model->aggiungiVolume($allenamenti),
            'pager'       => $model->pager,
        ]);
    }

    public function dettaglio(int $id): string
    {
        $allenamento = model(SingoloWorkoutModel::class)->trovaDelUtente($id, $this->userId()) ?? $this->nonTrovato();
        $serie       = model(SerieEseguitaModel::class)->perAllenamento($id);
        $esercizi    = $this->raggruppaPerEsercizio($serie);

        return view('storico/dettaglio', [
            'titolo'      => $allenamento['workout_nome'],
            'allenamento' => $allenamento,
            'esercizi'    => $this->segnaRecord($esercizi, $allenamento['data_esecuzione']),
            'stati'       => model(StatoModel::class)->perId(),
            'volume'      => array_sum(array_map(static fn ($s) => $s['peso'] * $s['rep'], $serie)),
            'n_serie'     => count($serie),
        ]);
    }

    public function elimina(int $id): RedirectResponse
    {
        model(SingoloWorkoutModel::class)->trovaDelUtente($id, $this->userId()) ?? $this->nonTrovato();

        // le serie si cancellano da sole (foreign key con CASCADE)
        model(SingoloWorkoutModel::class)->delete($id);

        return redirect()->to('storico')->with('successo', 'Allenamento eliminato.');
    }

    /**
     * Da una lista piatta di serie a: [esercizio_id => ['nome' => 'Panca', 'serie' => [...]]]
     */
    private function raggruppaPerEsercizio(array $serie): array
    {
        $esercizi = [];

        foreach ($serie as $riga) {
            $id = $riga['esercizio_id'];
            $esercizi[$id] ??= ['nome' => $riga['esercizio_nome'], 'gruppo' => $riga['gruppo_muscolare'], 'serie' => []];
            $esercizi[$id]['serie'][] = $riga;
        }

        return $esercizi;
    }

    /**
     * RECORD PERSONALE: per ogni esercizio, la serie migliore di oggi
     * batte tutte quelle fatte prima? Allora è un record (PR).
     */
    private function segnaRecord(array $esercizi, string $data): array
    {
        $precedenti = model(SerieEseguitaModel::class)->miglioriPunteggiPrima($this->userId(), array_keys($esercizi), $data);

        foreach ($esercizi as $id => &$esercizio) {
            $migliore = max(array_map(
                static fn ($s) => Statistiche::punteggioSerie((float) $s['peso'], (int) $s['rep']),
                $esercizio['serie'],
            ));

            // se non l'avevi mai fatto prima non è un "record", è la prima volta
            $esercizio['record'] = isset($precedenti[$id]) && $migliore > $precedenti[$id];
        }

        return $esercizi;
    }
}
