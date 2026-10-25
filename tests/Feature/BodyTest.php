<?php

namespace Tests\Feature;

use App\Models\ProgressPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BodyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasics();
    }

    public function test_una_misurazione_al_giorno(): void
    {
        $user = $this->actingAs($this->athlete())->athleteUser();

        $this->post('/corpo/misure', ['weight_kg' => '58,2'])->assertSessionHasNoErrors();
        $this->post('/corpo/misure', ['weight_kg' => '58.0', 'body_fat' => '13'])->assertSessionHasNoErrors();

        $this->assertSame(1, $user->bodyEntries()->count());
        $this->assertSame(58.0, $user->bodyEntries()->first()->weight_kg);
    }

    public function test_foto_private_salvate_sul_disco_configurato(): void
    {
        Storage::fake('local'); // il disco finto: nessun file vero
        $owner = $this->athlete();

        $this->actingAs($owner)->post('/corpo/foto', [
            'taken_on' => now()->toDateString(),
            'pose' => 'fronte',
            'photos' => [UploadedFile::fake()->image('fronte.jpg', 600, 800)],
        ])->assertSessionHasNoErrors();

        $photo = ProgressPhoto::firstOrFail();
        Storage::disk('local')->assertExists($photo->path);

        $this->get("/corpo/foto/{$photo->id}")->assertOk();
        $this->actingAs($this->athlete())->get("/corpo/foto/{$photo->id}")->assertNotFound(); // non è tua
    }

    /** piccolo aiuto: l'utente loggato */
    private function athleteUser()
    {
        return auth()->user();
    }
}
