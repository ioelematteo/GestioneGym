<?php

namespace App\Services\Progress;

use App\Models\User;
use Carbon\CarbonImmutable;

/**
 * FREQUENZA: quanti allenamenti per settimana e per mese.
 * I conteggi li faccio in PHP, così le settimane vuote ci sono (a 0) e funziona con ogni database.
 */
class Frequency
{
    /** Le date (Y-m-d) degli allenamenti dell'utente da $from in poi. */
    public function dates(User $user, CarbonImmutable $from, ?CarbonImmutable $to = null): array
    {
        return $user->trainingSessions()
            ->where('performed_at', '>=', $from)
            ->when($to, fn ($q) => $q->where('performed_at', '<', $to))
            ->orderBy('performed_at')
            ->pluck('performed_at')
            ->map(fn ($date) => $date->toDateString())
            ->all();
    }

    /** @return list<array{label: string, value: int}> le ultime $weeks settimane, lunedì -> domenica */
    public function perWeek(array $dates, int $weeks, ?CarbonImmutable $today = null): array
    {
        $monday = ($today ?? CarbonImmutable::today())->startOfWeek();
        $buckets = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = $monday->subWeeks($i);
            $buckets[$start->toDateString()] = ['label' => $start->translatedFormat('j M'), 'value' => 0];
        }

        foreach ($dates as $date) {
            $key = CarbonImmutable::parse($date)->startOfWeek()->toDateString();
            if (isset($buckets[$key])) {
                $buckets[$key]['value']++;
            }
        }

        return array_values($buckets);
    }

    /** @return list<array{label: string, value: int}> gli ultimi $months mesi */
    public function perMonth(array $dates, int $months, ?CarbonImmutable $today = null): array
    {
        $first = ($today ?? CarbonImmutable::today())->startOfMonth();
        $buckets = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $first->subMonthsNoOverflow($i);
            $buckets[$month->format('Y-m')] = ['label' => $month->translatedFormat('M'), 'value' => 0];
        }

        foreach ($dates as $date) {
            $key = substr($date, 0, 7);
            if (isset($buckets[$key])) {
                $buckets[$key]['value']++;
            }
        }

        return array_values($buckets);
    }
}
