<?php

namespace App\Contracts;

use App\Services\Coach\AiReply;

/**
 * UN QUALSIASI MODELLO DI IA CHE RISPONDE A UNA DOMANDA.
 * Oggi: DeepSeek. Per cambiare (OpenAI, Claude, Mistral...) scrivi una classe che
 * implementa questa interfaccia e cambi il binding in GymServiceProvider. Fine.
 */
interface AiClient
{
    /**
     * @throws \App\Exceptions\AiUnavailableException se il servizio non risponde
     */
    public function chat(string $systemPrompt, string $userMessage): AiReply;
}
