<?php

namespace App\Services\Coach;

/** La risposta dell'IA: il testo e quale modello l'ha scritta. */
final readonly class AiReply
{
    public function __construct(
        public string $text,
        public string $model,
    ) {}
}
