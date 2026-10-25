<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Services\Body\BodyLog;
use App\Services\Progress\Formula;
use App\Services\Progress\Frequency;
use App\Services\Progress\WeightProgress;
use App\Services\Training\NextWorkoutPicker;
use Carbon\CarbonImmutable;

/**
 * PREPARA LA DASHBOARD: non calcola niente da sola, chiede agli altri servizi.
 * Periodo = ultime N settimane fino a oggi; confronto = le N settimane prima.
 */
class DashboardData
{
    public function __construct(
        private Frequency $frequency,
        private WeightProgress $weights,
        private BodyLog $body,
        private NextWorkoutPicker $next,
    ) {}

    public function for(User $user, int $weeks): array
    {
        $end = CarbonImmutable::tomorrow();
        $start = $end->subWeeks($weeks);
        $previous = $start->subWeeks($weeks);

        $now = count($this->frequency->dates($user, $start, $end));
        $before = count($this->frequency->dates($user, $previous, $start));
        $allDates = $this->frequency->dates($user, CarbonImmutable::today()->startOfMonth()->subMonths(5));

        $volume = $this->weights->volumePerSession($user, $start, $end);
        $volumeBefore = $this->weights->volumePerSession($user, $previous, $start)->sum();

        $bodyEntries = $this->body->since($user, $start);
        if ($bodyEntries->count() < 2) {
            $bodyEntries = $this->body->latest($user, 8);
        }

        return [
            'sessions' => ['value' => $now, 'delta' => $now - $before],
            'per_week' => ['value' => round($now / $weeks, 1), 'delta' => round(($now - $before) / $weeks, 1), 'per_month' => round($now / ($weeks * 7) * 30.4, 1)],
            'volume' => ['value' => $volume->sum(), 'change' => Formula::change($volumeBefore, $volume->sum()), 'series' => $volume->all()],
            'body' => [
                'value' => $bodyEntries->last()?->weight_kg,
                'delta' => $bodyEntries->count() > 1 ? round($bodyEntries->last()->weight_kg - $bodyEntries->first()->weight_kg, 1) : null,
                'series' => $bodyEntries->pluck('weight_kg')->all(),
            ],
            'frequency' => [
                'weeks' => $this->frequency->perWeek($allDates, 8),
                'months' => $this->frequency->perMonth($allDates, 6),
            ],
            'lifts' => $this->weights->summary($user, $start)->filter(fn ($row) => $row['sessions'] > 1)->take(6)->values(),
            'recent' => $user->trainingSessions()->with(['workout', 'sets'])->latest('performed_at')->take(5)->get(),
            'next' => $this->next->ordered($user)->take(3),
        ];
    }
}
