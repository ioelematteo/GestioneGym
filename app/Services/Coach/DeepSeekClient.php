<?php

namespace App\Services\Coach;

use App\Contracts\AiClient;
use App\Exceptions\AiUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as Http;

/**
 * DEEPSEEK (API compatibile con OpenAI: POST /chat/completions).
 * Chiave e modello arrivano dal costruttore (vedi GymServiceProvider), non da qui dentro.
 *
 * Nota: i modelli di DeepSeek leggono solo testo, non le immagini.
 */
class DeepSeekClient implements AiClient
{
    public function __construct(
        private Http $http,
        private string $apiKey,
        private string $baseUrl,
        private string $model,
        private int $timeout = 90,
    ) {}

    public function chat(string $systemPrompt, string $userMessage): AiReply
    {
        try {
            $response = $this->http
                ->withToken($this->apiKey)
                ->timeout($this->timeout)
                ->acceptJson()
                ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                    'model' => $this->model,
                    'temperature' => 0.6,
                    'max_tokens' => 1500,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);
        } catch (ConnectionException $e) {
            throw new AiUnavailableException('DeepSeek non raggiungibile: '.$e->getMessage(), previous: $e);
        }

        if ($response->failed()) {
            throw new AiUnavailableException("DeepSeek ha risposto con errore {$response->status()}.");
        }

        $text = trim((string) $response->json('choices.0.message.content'));

        if ($text === '') {
            throw new AiUnavailableException('DeepSeek ha risposto senza testo.');
        }

        return new AiReply($text, (string) ($response->json('model') ?? $this->model));
    }
}
