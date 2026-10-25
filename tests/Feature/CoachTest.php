<?php

namespace Tests\Feature;

use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoachTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasics();
    }

    public function test_manda_i_dati_a_deepseek_e_salva_la_risposta(): void
    {
        config(['services.deepseek.key' => 'chiave-di-test']);
        Http::fake(['api.deepseek.com/*' => Http::response([
            'model' => 'deepseek-chat',
            'choices' => [['message' => ['content' => "### Cosa noto\nSquat fermo a 10 kg."]]],
        ])]);

        $user = $this->athlete();
        $workout = $this->workoutFor($user);
        $squat = Exercise::where('name', 'Squat')->value('id');
        $this->actingAs($user)->post("/allenamento/{$workout->id}", [
            'date' => now()->toDateString(),
            'sets' => [$squat => [1 => ['weight' => '10', 'reps' => '8']]],
        ]);

        $this->post('/coach', ['question' => 'Come va lo squat?'])->assertRedirect('/coach');

        Http::assertSent(function (Request $request) {
            $messages = $request['messages'];

            return $request->hasHeader('Authorization', 'Bearer chiave-di-test')
                && str_contains($messages[0]['content'], 'coach personale')   // system prompt
                && str_contains($messages[1]['content'], 'Squat: 10×8')       // le sue serie
                && str_contains($messages[1]['content'], 'Come va lo squat?');
        });

        $this->assertSame("### Cosa noto\nSquat fermo a 10 kg.", $user->coachReports()->first()->answer);
        $this->get('/coach')->assertSee('Squat fermo a 10 kg.');
    }

    public function test_senza_chiave_mostra_un_errore_chiaro(): void
    {
        config(['services.deepseek.key' => null]);
        Http::fake();

        $this->actingAs($this->athlete())->post('/coach', ['question' => 'Ciao'])->assertSessionHasErrors('question');
        Http::assertNothingSent();
    }
}
