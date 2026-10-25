<?php

namespace Database\Seeders;

use App\Models\BodyEntry;
use App\Models\SetState;
use App\Models\TrainingSession;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * DATI FINTI (facoltativo): 10 settimane di allenamenti A/B e pesate.
 * I pesi partono un po' più leggeri e arrivano a quelli delle schede.
 * NON lanciarlo se usi già l'app con dati veri.
 */
class DemoSeeder extends Seeder
{
    private const WEEKS = 10;

    public function run(): void
    {
        $this->call(DatabaseSeeder::class);
        mt_srand(42);

        $matteo = User::where('email', 'ioelematteo@gmail.com')->firstOrFail();

        if ($matteo->trainingSessions()->exists()) {
            $this->command?->warn('Matteo ha già degli allenamenti: niente dati demo.');

            return;
        }

        $this->sessions($matteo);
        $this->bodyEntries($matteo);
    }

    /** Lunedì, mercoledì, venerdì (a volte anche sabato), alternando A e B. */
    private function sessions(User $user): void
    {
        $workouts = $user->workouts()->with('items')->orderBy('name')->get();
        $states = SetState::pluck('id')->all();
        $start = CarbonImmutable::now()->startOfWeek()->subWeeks(self::WEEKS);
        $turn = 0;

        for ($week = 0; $week <= self::WEEKS; $week++) {
            foreach (mt_rand(1, 100) <= 40 ? [0, 2, 4, 5] : [0, 2, 4] as $dayOffset) {
                $day = $start->addWeeks($week)->addDays($dayOffset)->setTime(mt_rand(17, 19), mt_rand(0, 59));

                if ($day->isToday() || $day->isFuture()) {
                    return;
                }

                $workout = $workouts[$turn++ % $workouts->count()];
                $session = TrainingSession::factory()->for($user)->for($workout)->create([
                    'performed_at' => $day,
                    'notes' => mt_rand(1, 7) === 1 ? 'Dormito poco, energia bassa.' : null,
                ]);

                foreach ($workout->items as $item) {
                    $weeksBack = self::WEEKS - $week;
                    $weight = $this->round($item->weight_target * (1 - 0.02 * $weeksBack));
                    [$min, $max] = $this->repRange($item->reps_target);

                    for ($n = 1; $n <= $item->sets; $n++) {
                        $last = $n === $item->sets;
                        $session->sets()->create([
                            'exercise_id' => $item->exercise_id,
                            'set_number' => $n,
                            // l'ultima serie a volte un po' più leggera
                            'weight' => $last && mt_rand(1, 4) === 1 ? $this->round($weight * 0.9) : $weight,
                            'reps' => max(1, mt_rand($min, $max) - ($last ? mt_rand(0, 2) : 0)),
                            'set_state_id' => $last && mt_rand(1, 100) <= 30 ? $states[array_rand($states)] : null,
                        ]);
                    }
                }
            }
        }
    }

    private function bodyEntries(User $user): void
    {
        $weight = 54.0;

        for ($day = CarbonImmutable::today()->subWeeks(self::WEEKS); $day->lte(CarbonImmutable::today()); $day = $day->addDays(mt_rand(3, 5))) {
            $weight += mt_rand(-2, 8) / 20;

            BodyEntry::factory()->for($user)->create([
                'measured_on' => $day->toDateString(),
                'weight_kg' => round($weight, 1),
                'body_fat' => mt_rand(1, 3) === 1 ? round(13 + mt_rand(-10, 10) / 10, 1) : null,
                'waist_cm' => null,
            ]);
        }
    }

    /** "8-10" -> [8, 10] */
    private function repRange(?string $target): array
    {
        $parts = array_map('intval', explode('-', $target ?: '10'));

        return [$parts[0], $parts[1] ?? $parts[0]];
    }

    /** Sotto i 20 kg al mezzo kg (manubri), sopra al 2.5 (dischi). */
    private function round(float $kg): float
    {
        return $kg < 20 ? round($kg * 2) / 2 : round($kg / 2.5) * 2.5;
    }
}
