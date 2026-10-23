<?php

namespace App\Libraries;

use App\Models\PesoCorporeoModel;
use App\Models\SerieEseguitaModel;
use App\Models\SingoloWorkoutModel;
use App\Models\WorkoutModel;
use DateTimeImmutable;

/**
 * PREPARA TUTTI I DATI DELLA HOME
 *
 * Il controller Home fa solo:  $dashboard = new Dashboard($userId, 4);
 * e poi chiede i pezzi uno alla volta: $dashboard->cards(), $dashboard->frequenza(), ...
 *
 * Periodo: le ultime N settimane fino a oggi (compreso).
 * Periodo precedente: le N settimane prima di quelle (per i "+3 vs prima").
 */
class Dashboard
{
    private string $inizio;           // primo giorno del periodo (Y-m-d)
    private string $fine;             // domani: le query usano "data < fine"
    private string $inizioPrecedente; // primo giorno del periodo precedente

    private SingoloWorkoutModel $allenamenti;
    private SerieEseguitaModel $serie;
    private PesoCorporeoModel $pesi;

    public function __construct(private int $userId, private int $settimane)
    {
        $domani = new DateTimeImmutable('tomorrow');

        $this->fine             = $domani->format('Y-m-d');
        $this->inizio           = $domani->modify("-{$settimane} weeks")->format('Y-m-d');
        $this->inizioPrecedente = $domani->modify('-' . ($settimane * 2) . ' weeks')->format('Y-m-d');

        $this->allenamenti = model(SingoloWorkoutModel::class);
        $this->serie       = model(SerieEseguitaModel::class);
        $this->pesi        = model(PesoCorporeoModel::class);
    }

    /**
     * Le 4 card in alto.
     */
    public function cards(): array
    {
        $adesso = count($this->allenamenti->dateTra($this->userId, $this->inizio, $this->fine));
        $prima  = count($this->allenamenti->dateTra($this->userId, $this->inizioPrecedente, $this->inizio));

        return [
            'allenamenti' => ['valore' => $adesso, 'delta' => $adesso - $prima],
            'media'       => $this->cardMedia($adesso, $prima),
            'peso'        => $this->cardPeso(),
            'volume'      => $this->cardVolume(),
        ];
    }

    /**
     * Media settimanale e mensile degli allenamenti nel periodo.
     */
    private function cardMedia(int $adesso, int $prima): array
    {
        $settimanale = $adesso / $this->settimane;
        $giorni      = $this->settimane * 7;

        return [
            'settimanale' => round($settimanale, 1),
            'mensile'     => round($adesso / $giorni * 30.4, 1),
            'delta'       => round($settimanale - $prima / $this->settimane, 1),
        ];
    }

    /**
     * Ultimo peso, quanto è cambiato nel periodo e i valori per il mini grafico.
     */
    private function cardPeso(): array
    {
        $pesate = $this->pesi->andamento($this->userId, $this->inizio);

        // poche pesate nel periodo? uso le ultime 8 in assoluto
        if (count($pesate) < 2) {
            $pesate = $this->pesi->ultime($this->userId, 8);
        }

        if ($pesate === []) {
            return ['valore' => null, 'delta' => null, 'valori' => []];
        }

        $valori = array_map('floatval', array_column($pesate, 'peso'));
        $ultimo = end($valori);

        return [
            'valore' => $ultimo,
            'delta'  => count($valori) > 1 ? round($ultimo - $valori[0], 1) : null,
            'valori' => $valori,
        ];
    }

    /**
     * Volume totale (kg x rep) nel periodo, confronto % e un valore per ogni allenamento.
     */
    private function cardVolume(): array
    {
        $volumi = $this->serie->volumiTra($this->userId, $this->inizio, $this->fine);
        $prima  = $this->serie->volumiTra($this->userId, $this->inizioPrecedente, $this->inizio);

        $totale      = array_sum(array_column($volumi, 'volume'));
        $totalePrima = array_sum(array_column($prima, 'volume'));

        return [
            'valore' => $totale,
            'delta'  => Statistiche::variazione($totalePrima, $totale),
            'valori' => array_column($volumi, 'volume'),
        ];
    }

    /**
     * Allenamenti per settimana (ultime 8) e per mese (ultimi 6) per il grafico a barre.
     */
    public function frequenza(): array
    {
        $da   = (new DateTimeImmutable('first day of this month'))->modify('-5 months')->format('Y-m-d');
        $date = $this->allenamenti->dateTra($this->userId, $da, $this->fine);

        return [
            'settimane' => Statistiche::contaPerSettimana($date, 8),
            'mesi'      => Statistiche::contaPerMese($date, 6),
        ];
    }

    /**
     * Le pesate degli ultimi 3 mesi per il grafico a linea.
     */
    public function graficoPeso(): array
    {
        $da = (new DateTimeImmutable('today'))->modify('-3 months')->format('Y-m-d');

        return array_map(static fn ($pesata) => [
            'data' => data_corta($pesata['data']),
            'peso' => (float) $pesata['peso'],
        ], $this->pesi->andamento($this->userId, $da));
    }

    public function ultimiAllenamenti(): array
    {
        return $this->allenamenti->ultimi($this->userId, 5);
    }

    public function prossimiWorkout(): array
    {
        return array_slice(model(WorkoutModel::class)->prossimi($this->userId), 0, 3);
    }

    /**
     * PROGRESSI PRINCIPALI
     * Per ogni esercizio fatto nel periodo confronto la serie migliore
     * dell'ultima volta con la serie migliore della prima volta nel periodo.
     * "Principali" = quelli che hai fatto più spesso.
     */
    public function progressi(int $quanti = 5): array
    {
        $migliori  = $this->miglioriSeriePerAllenamento();
        $progressi = [];

        foreach ($migliori as $perAllenamento) {
            // servono almeno 2 allenamenti per fare un confronto
            if (count($perAllenamento) < 2) {
                continue;
            }

            $ultima = reset($perAllenamento); // la prima della lista = la più recente
            $prima  = end($perAllenamento);   // l'ultima della lista = la più vecchia

            $progressi[] = [
                'esercizio'  => $ultima['esercizio_nome'],
                'ultima'     => $ultima,
                'prima'      => $prima,
                'variazione' => Statistiche::variazione($prima['punteggio'], $ultima['punteggio']),
                'volte'      => count($perAllenamento),
            ];
        }

        // prima quelli che fai più spesso
        usort($progressi, static fn ($a, $b) => $b['volte'] <=> $a['volte']);

        return array_slice($progressi, 0, $quanti);
    }

    /**
     * [esercizio_id => [singolo_workout_id => serie migliore]]
     * Gli allenamenti restano in ordine dal più recente al più vecchio.
     */
    private function miglioriSeriePerAllenamento(): array
    {
        $migliori = [];

        foreach ($this->serie->recentiPerUtente($this->userId, $this->inizio) as $serie) {
            $serie['punteggio'] = Statistiche::punteggioSerie((float) $serie['peso'], (int) $serie['rep']);

            $esercizio   = $serie['esercizio_id'];
            $allenamento = $serie['singolo_workout_id'];
            $attuale     = $migliori[$esercizio][$allenamento] ?? null;

            if ($attuale === null || $serie['punteggio'] > $attuale['punteggio']) {
                $migliori[$esercizio][$allenamento] = $serie;
            }
        }

        return $migliori;
    }
}
