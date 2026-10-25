<?php

namespace App\Services\Coach;

use App\Contracts\AiClient;
use App\Models\CoachReport;
use App\Models\User;

/**
 * IL COACH: unisce i tre pezzi e salva la risposta.
 *   SystemPrompt  -> chi è e come risponde l'IA
 *   CoachContext  -> i tuoi dati in testo
 *   AiClient      -> chi risponde (DeepSeek)
 */
class Coach
{
    public function __construct(
        private AiClient $ai,
        private SystemPrompt $prompt,
        private CoachContext $context,
    ) {}

    public function ask(User $user, ?string $question): CoachReport
    {
        $question = trim((string) $question) ?: 'Analizza le ultime settimane e dimmi cosa noti.';

        $reply = $this->ai->chat(
            $this->prompt->text(),
            $this->context->build($user)."\n\n# Domanda\n".$question,
        );

        return $user->coachReports()->create([
            'question' => $question,
            'answer' => $reply->text,
            'model' => $reply->model,
        ]);
    }
}
