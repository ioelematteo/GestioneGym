<?php

namespace App\Services\Coach;

/**
 * IL SYSTEM PROMPT del coach. Il testo sta in resources/prompts/coach.md:
 * lo modifichi lì, senza toccare il codice.
 */
class SystemPrompt
{
    public function __construct(private string $path) {}

    public function text(): string
    {
        return trim(file_get_contents($this->path));
    }
}
