<?php

use App\Libraries\Statistiche;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * TEST DELLA LIBRERIA STATISTICHE
 * Lancia con: vendor/bin/phpunit tests/unit/StatisticheTest.php
 *
 * Ogni test = "dato questo, mi aspetto quest'altro".
 *
 * @internal
 */
final class StatisticheTest extends CIUnitTestCase
{
    public function testContaGliAllenamentiPerSettimana(): void
    {
        // oggi = venerdì 23 ottobre 2026 -> la settimana in corso parte lunedì 19
        $date = ['2026-10-19', '2026-10-21', '2026-10-14', '2026-09-01'];

        $settimane = Statistiche::contaPerSettimana($date, 2, '2026-10-23');

        $this->assertCount(2, $settimane);
        $this->assertSame('12 Ott', $settimane[0]['etichetta']);
        $this->assertSame(1, $settimane[0]['valore']); // solo il 14
        $this->assertSame('19 Ott', $settimane[1]['etichetta']);
        $this->assertSame(2, $settimane[1]['valore']); // il 19 e il 21
    }

    public function testLaDomenicaStaNellaSettimanaChePartiDalLunedi(): void
    {
        $settimane = Statistiche::contaPerSettimana(['2026-10-25'], 1, '2026-10-25');

        $this->assertSame('19 Ott', $settimane[0]['etichetta']);
        $this->assertSame(1, $settimane[0]['valore']);
    }

    public function testContaGliAllenamentiPerMese(): void
    {
        $mesi = Statistiche::contaPerMese(['2026-10-01', '2026-10-20', '2026-09-30'], 3, '2026-10-23');

        $this->assertSame(['Ago', 'Set', 'Ott'], array_column($mesi, 'etichetta'));
        $this->assertSame([0, 1, 2], array_column($mesi, 'valore'));
    }

    public function testVariazionePercentuale(): void
    {
        $this->assertSame(6.7, Statistiche::variazione(75, 80));
        $this->assertSame(-50.0, Statistiche::variazione(10, 5));
        $this->assertNull(Statistiche::variazione(0, 5)); // da zero non si può calcolare
    }

    public function testMassimaleStimato(): void
    {
        $this->assertSame(100.0, Statistiche::massimaleStimato(100, 1));
        $this->assertSame(96.0, Statistiche::massimaleStimato(80, 6)); // 80 x (1 + 6/30)
    }

    public function testAcorpoLiberoContanoLeRep(): void
    {
        $this->assertSame(12.0, Statistiche::punteggioSerie(0, 12));
    }
}
