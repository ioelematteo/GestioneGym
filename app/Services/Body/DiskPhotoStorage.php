<?php

namespace App\Services\Body;

use App\Contracts\PhotoStorage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Foto su un disco di Laravel. Il disco arriva da fuori (dependency injection):
 * in locale "local", su Laravel Cloud il bucket. Le foto NON sono pubbliche:
 * passano sempre da un controller che controlla che siano tue.
 */
class DiskPhotoStorage implements PhotoStorage
{
    public function __construct(private FilesystemAdapter $disk) {}

    public function store(UploadedFile $file, int $userId): string
    {
        return $this->disk->putFile("progress-photos/{$userId}", $file);
    }

    public function respond(string $path): StreamedResponse
    {
        return $this->disk->response($path, null, ['Cache-Control' => 'private, max-age=86400']);
    }

    public function delete(string $path): void
    {
        $this->disk->delete($path);
    }
}
