<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DATI FINTI PER PROVARE L'APP (opzionale!)
 * Lancialo con: php spark db:seed DemoSeeder
 *
 * Crea a Matteo 3 workout (Push A, Pull B, Legs) e ~10 settimane
 * di allenamenti e pesate, così la Home ha dei grafici da mostrare.
 * NON lanciarlo se stai già usando l'app con i tuoi dati veri.
 */
class DemoSeeder extends Seeder
{
    private const EMAIL = 'ioelematteo@gmail.com';
    private const SETTIMANE = 10;

    /**
     * I workout di esempio.
     * Per ogni esercizio: [nome, serie, rep target, recupero, peso iniziale, kg in più a settimana, note]
     */
    private array $schede = [
        'Push A' => ['colore' => 'verde', 'esercizi' => [
            ['Panca piana', 4, '6-8', 120, 70, 1.25, 'Fermo al petto, scapole addotte'],
            ['Military press', 3, '6-8', 120, 37.5, 0.75, null],
            ['Panca inclinata manubri', 3, '8-10', 90, 22, 0.5, 'Panca a 30°'],
            ['Alzate laterali', 3, '12-15', 60, 8, 0.2, null],
            ['French press', 3, '10-12', 60, 20, 0.5, null],
            ['Pushdown ai cavi', 3, '12-15', 60, 25, 0.5, 'Corda'],
        ]],
        'Pull B' => ['colore' => 'blu', 'esercizi' => [
            ['Stacco da terra', 3, '4-6', 180, 100, 2.5, 'Cintura sull\'ultima serie'],
            ['Trazioni', 4, '6-8', 120, 5, 1.5, 'Peso = zavorra'],
            ['Rematore bilanciere', 3, '8-10', 90, 55, 1, null],
            ['Lat machine', 3, '10-12', 90, 50, 1, null],
            ['Curl bilanciere', 3, '8-10', 60, 25, 0.5, null],
            ['Face pull', 3, '15', 60, 15, 0.5, null],
        ]],
        'Legs' => ['colore' => 'viola', 'esercizi' => [
            ['Squat', 4, '5-6', 180, 85, 1.75, 'Sotto il parallelo'],
            ['Stacco rumeno', 3, '8-10', 120, 70, 1.5, null],
            ['Leg press', 3, '10-12', 90, 140, 4, 'Piedi alti'],
            ['Leg curl', 3, '10-12', 60, 35, 1, null],
            ['Hip thrust', 3, '8-10', 90, 80, 3, null],
            ['Calf raise in piedi', 4, '12-15', 60, 60, 1.5, null],
        ]],
    ];

    public function run(): void
    {
        mt_srand(42); // stessi numeri "casuali" ogni volta

        $user = auth()->getProvider()->findByCredentials(['email' => self::EMAIL]);

        if ($user === null) {
            echo "  ! Prima lancia: php spark db:seed DatabaseSeeder\n";

            return;
        }

        if ($this->db->table('workouts')->where('user_id', $user->id)->countAllResults() > 0) {
            echo "  - Matteo ha già dei workout, non creo dati demo.\n";

            return;
        }

        $workoutIds = $this->creaWorkouts($user->id);
        $this->creaStorico($user->id, $workoutIds);
        $this->creaPesate($user->id);

        echo "  + dati demo creati\n";
    }

    /**
     * Crea i 3 workout e ritorna [nome => id].
     */
    private function creaWorkouts(int $userId): array
    {
        $ids    = [];
        $adesso = date('Y-m-d H:i:s');

        foreach ($this->schede as $nome => $scheda) {
            $this->db->table('workouts')->insert([
                'user_id'    => $userId,
                'nome'       => $nome,
                'colore'     => $scheda['colore'],
                'created_at' => $adesso,
                'updated_at' => $adesso,
            ]);
            $ids[$nome] = $this->db->insertID();

            foreach ($scheda['esercizi'] as $ordine => $es) {
                $this->db->table('workout_esercizi')->insert([
                    'workout_id'       => $ids[$nome],
                    'esercizio_id'     => $this->idEsercizio($es[0]),
                    'ordine'           => $ordine + 1,
                    'serie'            => $es[1],
                    'rep_target'       => $es[2],
                    'peso'             => $this->arrotonda($es[4] + $es[5] * self::SETTIMANE),
                    'recupero_secondi' => $es[3],
                    'note'             => $es[6],
                ]);
            }
        }

        return $ids;
    }

    /**
     * Crea gli allenamenti delle ultime settimane: lunedì, mercoledì, venerdì
     * (e ogni tanto anche il sabato), girando Push -> Pull -> Legs.
     */
    private function creaStorico(int $userId, array $workoutIds): void
    {
        $nomi   = array_keys($this->schede);
        $giro   = 0;
        $oggi   = new \DateTime('today');
        $inizio = (new \DateTime('monday this week'))->modify('-' . self::SETTIMANE . ' weeks');

        for ($settimana = 0; $settimana <= self::SETTIMANE; $settimana++) {
            $giorni = mt_rand(1, 100) <= 40 ? [0, 2, 4, 5] : [0, 2, 4];

            foreach ($giorni as $spostamento) {
                $giorno = (clone $inizio)->modify("+{$settimana} weeks +{$spostamento} days");

                // oggi lo lascio libero: è il giorno in cui ti alleni davvero
                if ($giorno >= $oggi) {
                    return;
                }

                $nome = $nomi[$giro % 3];
                $this->creaAllenamento($userId, $workoutIds[$nome], $nome, $giorno, $settimana);
                $giro++;
            }
        }
    }

    private function creaAllenamento(int $userId, int $workoutId, string $nome, \DateTime $giorno, int $settimana): void
    {
        $data = $giorno->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', mt_rand(17, 19), mt_rand(0, 59));

        $this->db->table('singolo_workout')->insert([
            'user_id'         => $userId,
            'workout_id'      => $workoutId,
            'data_esecuzione' => $data,
            'durata_minuti'   => mt_rand(55, 85),
            'note'            => mt_rand(1, 6) === 1 ? 'Giornata pesante, poco sonno.' : null,
            'created_at'      => $data,
            'updated_at'      => $data,
        ]);
        $singoloId = $this->db->insertID();

        foreach ($this->schede[$nome]['esercizi'] as $es) {
            [$nomeEs, $numSerie, $repTarget, , $pesoBase, $aumento] = $es;

            $peso        = $this->arrotonda($pesoBase + $aumento * $settimana);
            [$min, $max] = $this->rangeRep($repTarget);

            for ($n = 1; $n <= $numSerie; $n++) {
                // le ultime serie a volte calano di 1-2 rep
                $rep = mt_rand($min, $max) - ($n === $numSerie ? mt_rand(0, 2) : 0);

                $this->db->table('serie_eseguite')->insert([
                    'singolo_workout_id' => $singoloId,
                    'esercizio_id'       => $this->idEsercizio($nomeEs),
                    'numero_serie'       => $n,
                    'peso'               => $peso,
                    'rep'                => max(1, $rep),
                    'stato_id'           => $this->statoCasuale($n === $numSerie),
                ]);
            }
        }
    }

    private function creaPesate(int $userId): void
    {
        $peso   = 54.0;
        $giorno = new \DateTime('-' . self::SETTIMANE . ' weeks');
        $oggi   = new \DateTime('today');

        while ($giorno <= $oggi) {
            $peso += mt_rand(-2, 8) / 20; // sale piano piano, con un po' di rumore

            $this->db->table('peso_corporeo')->insert([
                'user_id'    => $userId,
                'peso'       => round($peso, 1),
                'data'       => $giorno->format('Y-m-d'),
                'created_at' => $giorno->format('Y-m-d 08:00:00'),
                'updated_at' => $giorno->format('Y-m-d 08:00:00'),
            ]);
            $giorno->modify('+' . mt_rand(3, 5) . ' days');
        }
    }

    // ---------------------------------------------------------------
    // Piccole funzioni di aiuto
    // ---------------------------------------------------------------

    private function idEsercizio(string $nome): int
    {
        $riga = $this->db->table('esercizi')->where('nome', $nome)->get()->getRowArray();

        return (int) $riga['id'];
    }

    /**
     * "6-8" -> [6, 8]   |   "15" -> [15, 15]
     */
    private function rangeRep(string $target): array
    {
        $parti = array_map('intval', explode('-', $target));

        return [$parti[0], $parti[1] ?? $parti[0]];
    }

    private function arrotonda(float $peso): float
    {
        return $peso < 20 ? round($peso) : round($peso / 2.5) * 2.5;
    }

    /**
     * Ogni tanto, sull'ultima serie, metto uno stato (kippate, spottato, ...).
     */
    private function statoCasuale(bool $ultimaSerie): ?int
    {
        if (! $ultimaSerie || mt_rand(1, 100) > 30) {
            return null;
        }

        $stati = $this->db->table('stati')->select('id')->get()->getResultArray();

        return (int) $stati[array_rand($stati)]['id'];
    }
}
