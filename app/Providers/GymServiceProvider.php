<?php

namespace App\Providers;

use App\Contracts\AiClient;
use App\Contracts\PhotoStorage;
use App\Services\Body\DiskPhotoStorage;
use App\Services\Coach\CoachContext;
use App\Services\Coach\DeepSeekClient;
use App\Services\Coach\MissingKeyAiClient;
use App\Services\Coach\SystemPrompt;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * IL "QUADRO ELETTRICO" DI GYMLOG
 *
 * Qui decidi quale pezzo concreto usare per ogni interfaccia.
 * Le classi che chiedono AiClient o PhotoStorage nel costruttore ricevono quello che c'è scritto qui.
 * Per cambiare un pezzo (es. un'altra IA) cambi UNA riga in questo file.
 */
class GymServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiClient::class, function ($app) {
            $config = config('services.deepseek');

            if (empty($config['key'])) {
                return new MissingKeyAiClient;
            }

            return new DeepSeekClient($app->make(Http::class), $config['key'], $config['url'], $config['model'], (int) $config['timeout']);
        });

        $this->app->bind(PhotoStorage::class, fn () => new DiskPhotoStorage(Storage::disk(config('gymlog.photos_disk'))));

        $this->app->bind(SystemPrompt::class, fn () => new SystemPrompt(resource_path('prompts/coach.md')));

        $this->app->when(CoachContext::class)->needs('$weeks')->giveConfig('gymlog.coach.weeks_of_data');
    }
}
