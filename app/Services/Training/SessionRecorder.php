<?php

namespace App\Services\Training;

use App\Models\SetState;
use App\Models\TrainingSession;
use App\Models\User;
use App\Models\Workout;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * SALVA UN ALLENAMENTO con tutte le sue serie.
 * Diventa "l'ultimo" di quella scheda; quelli vecchi restano nello storico.
 */
class SessionRecorder
{
    /**
     * @param  array  $sets  dal form: [exercise_id => [set_number => ['weight' => .., 'reps' => .., 'state' => ..]]]
     * @param  array{date: string, duration_minutes: ?int, notes: ?string}  $meta
     */
    public function record(User $user, Workout $workout, array $sets, array $meta): ?TrainingSession
    {
        $rows = $this->cleanSets($workout, $sets);

        if ($rows === []) {
            return null; // nessuna serie con le rep: non salvo niente
        }

        // transazione: o si salva tutto (allenamento + serie) o niente
        return DB::transaction(function () use ($user, $workout, $rows, $meta) {
            $session = $user->trainingSessions()->create([
                'workout_id' => $workout->id,
                'performed_at' => $this->performedAt($meta['date']),
                'duration_minutes' => $meta['duration_minutes'] ?: null,
                'notes' => $meta['notes'] ?: null,
            ]);

            $session->sets()->createMany($rows);

            return $session;
        });
    }

    /**
     * Tiene solo le serie valide: esercizio della scheda, rep > 0, stato esistente.
     * Ogni serie ha il SUO peso (puoi fare 16, 16, 14 kg).
     */
    private function cleanSets(Workout $workout, array $sets): array
    {
        $exerciseIds = $workout->items()->pluck('exercise_id')->all();
        $stateIds = SetState::pluck('id')->all();
        $rows = [];

        foreach ($sets as $exerciseId => $numbers) {
            if (! in_array((int) $exerciseId, $exerciseIds, true)) {
                continue;
            }

            foreach ((array) $numbers as $number => $values) {
                $reps = (int) ($values['reps'] ?? 0);

                if ($reps <= 0 || $number < 1 || $number > 30) {
                    continue;
                }

                $state = (int) ($values['state'] ?? 0);

                $rows[] = [
                    'exercise_id' => (int) $exerciseId,
                    'set_number' => (int) $number,
                    'weight' => min(max((float) str_replace(',', '.', (string) ($values['weight'] ?? 0)), 0), 9999),
                    'reps' => min($reps, 999),
                    'set_state_id' => in_array($state, $stateIds, true) ? $state : null,
                ];
            }
        }

        return $rows;
    }

    /** Oggi -> ora attuale. Un giorno passato -> mezzogiorno. Il futuro diventa oggi. */
    private function performedAt(string $date): CarbonImmutable
    {
        $day = CarbonImmutable::parse($date);

        return $day->isToday() || $day->isFuture() ? CarbonImmutable::now() : $day->setTime(12, 0);
    }
}
