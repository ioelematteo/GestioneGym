<?php

namespace App\Services\Progress;

use App\Models\SessionSet;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * TRACKER DEI PESI
 *
 * Per ogni esercizio e per ogni allenamento calcola:
 *   - peso medio delle serie   (Squat 10, 10, 9, 9 -> 9.5 kg)
 *   - peso massimo
 *   - miglior massimale stimato
 *   - volume (kg × rep)
 */
class WeightProgress
{
    /**
     * Una riga per (esercizio, allenamento), dalla più vecchia alla più nuova.
     *
     * @return Collection<int, array{exercise_id: int, exercise: string, session_id: int, date: CarbonImmutable, avg: float, max: float, best: float, volume: float, sets: int}>
     */
    public function perSession(User $user, ?int $exerciseId = null, ?CarbonImmutable $since = null): Collection
    {
        $sets = SessionSet::query()
            ->select('session_sets.*', 'training_sessions.performed_at', 'exercises.name as exercise_name')
            ->join('training_sessions', 'training_sessions.id', '=', 'session_sets.training_session_id')
            ->join('exercises', 'exercises.id', '=', 'session_sets.exercise_id')
            ->where('training_sessions.user_id', $user->id)
            ->when($exerciseId, fn ($q) => $q->where('session_sets.exercise_id', $exerciseId))
            ->when($since, fn ($q) => $q->where('training_sessions.performed_at', '>=', $since))
            ->orderBy('training_sessions.performed_at')
            ->get();

        return $sets
            ->groupBy(fn (SessionSet $set) => $set->exercise_id.'-'.$set->training_session_id)
            ->map(function (Collection $group) {
                $first = $group->first();
                $weights = $group->pluck('weight')->all();

                return [
                    'exercise_id' => $first->exercise_id,
                    'exercise' => $first->exercise_name,
                    'session_id' => $first->training_session_id,
                    'date' => CarbonImmutable::parse($first->performed_at),
                    'avg' => round(Formula::average($weights), 1),
                    'max' => (float) max($weights),
                    'best' => $group->max(fn (SessionSet $s) => Formula::setScore($s->weight, $s->reps)),
                    'volume' => (float) $group->sum(fn (SessionSet $s) => $s->weight * $s->reps),
                    'sets' => $group->count(),
                ];
            })
            ->values();
    }

    /**
     * Riepilogo per esercizio (pagina "Pesi"): prima quelli fatti più di recente.
     *
     * @return Collection<int, array{exercise_id: int, exercise: string, sessions: int, last_date: CarbonImmutable, last_avg: float, first_avg: float, change: ?float, series: list<float>, max: float}>
     */
    public function summary(User $user, ?CarbonImmutable $since = null): Collection
    {
        return $this->perSession($user, null, $since)
            ->groupBy('exercise_id')
            ->map(function (Collection $rows) {
                $first = $rows->first();
                $last = $rows->last();

                return [
                    'exercise_id' => $last['exercise_id'],
                    'exercise' => $last['exercise'],
                    'sessions' => $rows->count(),
                    'last_date' => $last['date'],
                    'last_avg' => $last['avg'],
                    'first_avg' => $first['avg'],
                    'change' => $rows->count() > 1 ? Formula::change($first['avg'], $last['avg']) : null,
                    'series' => $rows->pluck('avg')->all(),
                    'max' => $rows->max('max'),
                ];
            })
            ->sortByDesc(fn ($row) => $row['last_date']->timestamp)
            ->values();
    }

    /** Volume totale di ogni allenamento nel periodo (per il mini grafico in dashboard). */
    public function volumePerSession(User $user, CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return $this->perSession($user, null, $from)
            ->filter(fn ($row) => $row['date']->lt($to))
            ->groupBy('session_id')
            ->map(fn (Collection $rows) => $rows->sum('volume'))
            ->values();
    }
}
