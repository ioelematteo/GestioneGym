<?php

namespace App\Libraries;

use DateTimeImmutable;

/**
 * CALCOLI PURI (niente database qui dentro!)
 *
 * Gli dai dei numeri o delle date, ti ridà dei numeri.
 * Essendo "puri" sono facili da testare: vedi tests/unit/StatisticheTest.php
 *
 * Sono metodi "static": li chiami senza fare new, così:
 *   Statistiche::variazione(75, 80)  ->  6.67
 */
class Statistiche
{
    public const MESI = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];

    /**
     * Quanti allenamenti per settimana, nelle ultime N settimane (lunedì -> domenica).
     *
     * @param list<string> $date ['2026-10-20', '2026-10-22', ...]
     *
     * @return list<array{etichetta: string, inizio: string, valore: int}>
     */
    public static function contaPerSettimana(array $date, int $settimane, string $oggi = 'today'): array
    {
        $lunediCorrente = (new DateTimeImmutable($oggi))->modify('monday this week');

        // 1. preparo le settimane vuote, dalla più vecchia alla più nuova
        $risultato = [];
        for ($i = $settimane - 1; $i >= 0; $i--) {
            $lunedi = $lunediCorrente->modify("-{$i} weeks");

            $risultato[$lunedi->format('Y-m-d')] = [
                'etichetta' => $lunedi->format('j') . ' ' . self::MESI[$lunedi->format('n') - 1],
                'inizio'    => $lunedi->format('Y-m-d'),
                'valore'    => 0,
            ];
        }

        // 2. per ogni data trovo il suo lunedì e aggiungo 1
        foreach ($date as $data) {
            $chiave = (new DateTimeImmutable($data))->modify('monday this week')->format('Y-m-d');

            if (isset($risultato[$chiave])) {
                $risultato[$chiave]['valore']++;
            }
        }

        return array_values($risultato);
    }

    /**
     * Quanti allenamenti per mese, negli ultimi N mesi.
     *
     * @return list<array{etichetta: string, valore: int}>
     */
    public static function contaPerMese(array $date, int $mesi, string $oggi = 'today'): array
    {
        $meseCorrente = (new DateTimeImmutable($oggi))->modify('first day of this month');

        $risultato = [];
        for ($i = $mesi - 1; $i >= 0; $i--) {
            $mese = $meseCorrente->modify("-{$i} months");

            $risultato[$mese->format('Y-m')] = [
                'etichetta' => self::MESI[$mese->format('n') - 1],
                'valore'    => 0,
            ];
        }

        foreach ($date as $data) {
            $chiave = substr($data, 0, 7); // '2026-10-20' -> '2026-10'

            if (isset($risultato[$chiave])) {
                $risultato[$chiave]['valore']++;
            }
        }

        return array_values($risultato);
    }

    /**
     * Variazione percentuale da $prima a $dopo.
     * 75 -> 80 = +6.67   |   se prima è 0 non si può calcolare -> null
     */
    public static function variazione(float $prima, float $dopo): ?float
    {
        if ($prima == 0) {
            return null;
        }

        return round(($dopo - $prima) / $prima * 100, 1);
    }

    /**
     * Massimale stimato (1RM) con la formula di Epley: peso x (1 + rep / 30)
     * Serve per confrontare serie diverse: 80x6 è meglio di 85x2?
     */
    public static function massimaleStimato(float $peso, int $rep): float
    {
        if ($rep <= 1) {
            return $peso;
        }

        return round($peso * (1 + $rep / 30), 1);
    }

    /**
     * Un "punteggio" per capire qual è la serie migliore.
     * Con il peso uso il massimale stimato, a corpo libero (peso 0) uso le rep.
     */
    public static function punteggioSerie(float $peso, int $rep): float
    {
        return $peso > 0 ? self::massimaleStimato($peso, $rep) : (float) $rep;
    }
}
