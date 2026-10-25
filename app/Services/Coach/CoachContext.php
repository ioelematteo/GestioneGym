<?php

namespace App\Services\Coach;

use App\Models\User;
use App\Services\Body\BodyLog;
use App\Services\Progress\WeightProgress;
use Carbon\CarbonImmutable;

/**
 * RACCOGLIE I TUOI DATI e li mette in un testo leggibile per l'IA.
 * Ogni sezione è un metodo a parte: vuoi mandare qualcosa in più? Aggiungi un metodo.
 */
class CoachContext
{
    public function __construct(
        private WeightProgress $weights,
        private BodyLog $body,
        private int $weeks,
    ) {}

    public function build(User $user): string
    {
        $since = CarbonImmutable::today()->subWeeks($this->weeks);

        return implode("\n\n", [
            "# Dati di {$user->name} (oggi è ".CarbonImmutable::today()->translatedFormat('l j F Y').", ultime {$this->weeks} settimane)",
            $this->plans($user),
            $this->sessions($user, $since),
            $this->weightTrend($user, $since),
            $this->bodyData($user, $since),
            $this->photos($user, $since),
        ]);
    }

    private function plans(User $user): string
    {
        $lines = ['## Schede'];

        foreach ($user->workouts()->with('items.exercise')->get() as $workout) {
            $lines[] = "### {$workout->name}";
            foreach ($workout->items as $item) {
                $kg = $item->weight_target ? "{$item->weight_target} kg" : 'corpo libero';
                $lines[] = "- {$item->exercise->name}: {$item->sets} × {$item->reps_target}, {$kg}, recupero {$item->rest_seconds}s";
            }
        }

        return implode("\n", $lines);
    }

    private function sessions(User $user, CarbonImmutable $since): string
    {
        $sessions = $user->trainingSessions()
            ->with(['workout', 'sets.exercise', 'sets.state'])
            ->where('performed_at', '>=', $since)
            ->orderBy('performed_at')
            ->get();

        if ($sessions->isEmpty()) {
            return "## Allenamenti\nNessun allenamento nel periodo.";
        }

        $lines = ["## Allenamenti ({$sessions->count()})"];

        foreach ($sessions as $session) {
            $head = "### {$session->performed_at->format('d/m')} {$session->workout->name}";
            $head .= $session->duration_minutes ? " ({$session->duration_minutes} min)" : '';
            $lines[] = $head.($session->notes ? " — nota: {$session->notes}" : '');

            foreach ($session->sets->groupBy('exercise_id') as $sets) {
                $text = $sets->map(fn ($s) => "{$s->weight}×{$s->reps}".($s->state ? " [{$s->state->name}]" : ''))->implode(', ');
                $lines[] = "- {$sets->first()->exercise->name}: {$text}";
            }
        }

        return implode("\n", $lines);
    }

    private function weightTrend(User $user, CarbonImmutable $since): string
    {
        $lines = ['## Peso medio per esercizio (prima → ultima volta nel periodo)'];

        foreach ($this->weights->summary($user, $since) as $row) {
            $change = $row['change'] === null ? '' : " ({$row['change']}%)";
            $lines[] = "- {$row['exercise']}: {$row['first_avg']} → {$row['last_avg']} kg{$change}, {$row['sessions']} allenamenti";
        }

        return implode("\n", $lines);
    }

    private function bodyData(User $user, CarbonImmutable $since): string
    {
        $entries = $this->body->since($user, $since);

        if ($entries->isEmpty()) {
            return "## Corpo\nNessuna misurazione nel periodo.";
        }

        $lines = ['## Corpo'];
        foreach ($entries as $entry) {
            $extra = array_filter([
                $entry->body_fat ? "grasso {$entry->body_fat}%" : null,
                $entry->waist_cm ? "vita {$entry->waist_cm} cm" : null,
                $entry->notes,
            ]);
            $lines[] = "- {$entry->measured_on->format('d/m')}: {$entry->weight_kg} kg".($extra ? ' — '.implode(', ', $extra) : '');
        }

        return implode("\n", $lines);
    }

    private function photos(User $user, CarbonImmutable $since): string
    {
        $photos = $user->progressPhotos()->where('taken_on', '>=', $since->toDateString())->orderBy('taken_on')->get();

        if ($photos->isEmpty()) {
            return "## Foto\nNessuna foto nel periodo.";
        }

        $lines = ['## Foto dei progressi (non puoi vederle: solo data, posa e note)'];
        foreach ($photos as $photo) {
            $lines[] = "- {$photo->taken_on->format('d/m')} {$photo->pose}".($photo->notes ? ": {$photo->notes}" : '');
        }

        return implode("\n", $lines);
    }
}
