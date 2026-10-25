<?php

namespace Tests\Unit;

use App\Services\Progress\Formula;
use App\Services\Progress\Frequency;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase
{
    public function test_variazione_percentuale(): void
    {
        $this->assertSame(6.7, Formula::change(75, 80));
        $this->assertNull(Formula::change(0, 5));
    }

    public function test_massimale_stimato_e_corpo_libero(): void
    {
        $this->assertSame(96.0, Formula::oneRepMax(80, 6));
        $this->assertSame(12.0, Formula::setScore(0, 12)); // a corpo libero contano le rep
    }

    public function test_conta_per_settimana_con_settimane_vuote(): void
    {
        $weeks = (new Frequency)->perWeek(['2026-10-19', '2026-10-21', '2026-10-05'], 3, CarbonImmutable::parse('2026-10-23'));

        $this->assertSame([1, 0, 2], array_column($weeks, 'value')); // 5 ott, 12 ott (vuota), 19 ott
    }
}
