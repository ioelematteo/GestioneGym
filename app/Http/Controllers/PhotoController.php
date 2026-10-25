<?php

namespace App\Http\Controllers;

use App\Contracts\PhotoStorage;
use App\Http\Controllers\Concerns\ChecksOwnership;
use App\Http\Requests\PhotoRequest;
use App\Models\ProgressPhoto;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * FOTO DEI PROGRESSI. Non sa DOVE finiscono i file: lo sa PhotoStorage (iniettato).
 */
class PhotoController extends Controller
{
    use ChecksOwnership;

    public function __construct(private PhotoStorage $storage) {}

    public function store(PhotoRequest $request): RedirectResponse
    {
        foreach ($request->file('photos') as $file) {
            $request->user()->progressPhotos()->create([
                'taken_on' => $request->date('taken_on'),
                'pose' => $request->input('pose'),
                'notes' => $request->input('notes'),
                'path' => $this->storage->store($file, $request->user()->id),
            ]);
        }

        return back()->with('success', 'Foto caricate.');
    }

    public function show(ProgressPhoto $photo): StreamedResponse
    {
        $this->ensureOwns($photo);

        return $this->storage->respond($photo->path);
    }

    public function destroy(ProgressPhoto $photo): RedirectResponse
    {
        $this->ensureOwns($photo);
        $this->storage->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto eliminata.');
    }
}
