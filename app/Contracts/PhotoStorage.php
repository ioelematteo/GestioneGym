<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * DOVE FINISCONO LE FOTO DEI PROGRESSI.
 * Oggi: un disco di Laravel (locale o bucket di Laravel Cloud).
 * Domani puoi scrivere un'altra classe (es. Cloudinary) che implementa questa interfaccia
 * e cambiare solo il "binding" in GymServiceProvider: il resto dell'app non se ne accorge.
 */
interface PhotoStorage
{
    /** Salva il file e ritorna il percorso da tenere a database. */
    public function store(UploadedFile $file, int $userId): string;

    /** Restituisce la foto al browser. */
    public function respond(string $path): StreamedResponse;

    public function delete(string $path): void;
}
