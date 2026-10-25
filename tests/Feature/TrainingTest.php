<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\TrainingSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasics();
    }

    public function test_salva_allenamento_con_un_peso_per_ogni_serie(): void
    {
        $user = $this->athlete();
        $workout = $this->workoutFor($user);
        $squat = Exercise::where('name', 'Squat')->value('id');

        $response = $this->actingAs($user)->post("/allenamento/{$workout->id}", [
            'date' => now()->toDateString(),
            'body_weight' => '58,4',
            'sets' => [$squat => [
                1 => ['weight' => '10', 'reps' => '10'],
                2 => ['weight' => '10', 'reps' => '9'],
                3 => ['weight' => '9,5', 'reps' => '8', 'state' => 1],
                4 => ['weight' => '', 'reps' => ''], // vuota = non fatta
            ]],
        ]);

        $session = TrainingSession::firstOrFail();
        $response->assertRedirect("/storico/{$session->id}");

        $this->assertSame([10.0, 10.0, 9.5], $session->sets->pluck('weight')->all());
        $this->assertSame(1, $session->sets->last()->set_state_id);
        $this->assertSame(58.4, $user->bodyEntries()->first()->weight_kg);
    }

    public function test_senza_serie_non_salva(): void
    {
        $user = $this->athlete();
        $workout = $this->workoutFor($user);

        $this->actingAs($user)->post("/allenamento/{$workout->id}", ['date' => now()->toDateString(), 'sets' => []])
            ->assertSessionHasErrors('sets');
        $this->assertDatabaseCount('training_sessions', 0);
    }

    public function test_la_pagina_mostra_i_pesi_dell_ultima_volta(): void
    {
        $user = $this->athlete();
        $workout = $this->workoutFor($user);
        $squat = Exercise::where('name', 'Squat')->value('id');

        $this->actingAs($user)->post("/allenamento/{$workout->id}", [
            'date' => now()->subDays(3)->toDateString(),
            'sets' => [$squat => [1 => ['weight' => '12.5', 'reps' => '7']]],
        ]);

        $this->get("/allenamento/{$workout->id}")
            ->assertOk()
            ->assertSee('placeholder="12.5"', false)
            ->assertSee('ultima', false);
    }

    public function test_non_puoi_aprire_la_scheda_di_un_altro(): void
    {
        $workout = $this->workoutFor($this->athlete());

        $this->actingAs($this->athlete())->get("/allenamento/{$workout->id}")->assertNotFound();
    }

    public function test_crea_scheda_con_peso_e_blocca_i_doppioni(): void
    {
        $user = $this->athlete();
        $row = fn ($name, $kg) => ['name' => $name, 'sets' => 3, 'reps_target' => '8-10', 'weight_target' => $kg, 'rest_seconds' => 90];

        $this->actingAs($user)->post('/schede', ['name' => 'Allenamento A', 'color' => 'rosso', 'rows' => [$row('Panca inclinata', '16'), $row('panca inclinata', '16')]])
            ->assertSessionHasErrors('rows');

        $this->post('/schede', ['name' => 'Allenamento A', 'color' => 'rosso', 'rows' => [$row('Panca inclinata', '16'), $row('Dip', '0')]])
            ->assertRedirect('/allenamento');

        $this->assertSame([16.0, 0.0], $user->workouts()->first()->items->pluck('weight_target')->all());
    }

    public function test_tracker_pesi_calcola_il_peso_medio(): void
    {
        $user = $this->athlete();
        $workout = $this->workoutFor($user);
        $squat = Exercise::where('name', 'Squat')->value('id');

        $this->actingAs($user)->post("/allenamento/{$workout->id}", [
            'date' => now()->toDateString(),
            'sets' => [$squat => [1 => ['weight' => '10', 'reps' => '10'], 2 => ['weight' => '9', 'reps' => '10']]],
        ]);

        $this->get('/pesi')->assertOk()->assertSee('Squat')->assertSee('9.5');
        $this->get("/pesi/{$squat}")->assertOk()->assertSee('9.5');
    }
}
