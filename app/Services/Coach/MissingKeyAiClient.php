<?php

namespace App\Services\Coach;

use App\Contracts\AiClient;
use App\Exceptions\AiUnavailableException;

/** Usato quando DEEPSEEK_API_KEY non è impostata: spiega cosa fare invece di rompersi. */
class MissingKeyAiClient implements AiClient
{
    public function chat(string $systemPrompt, string $userMessage): AiReply
    {
        throw new AiUnavailableException('Manca DEEPSEEK_API_KEY nel file .env (o nelle variabili di Laravel Cloud).');
    }
}
