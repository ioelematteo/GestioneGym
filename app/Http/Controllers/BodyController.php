<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksOwnership;
use App\Http\Requests\BodyEntryRequest;
use App\Models\BodyEntry;
use App\Services\Body\BodyLog;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** FORMA FISICA: peso, misure e foto. */
class BodyController extends Controller
{
    use ChecksOwnership;

    public function index(Request $request, BodyLog $body): View
    {
        $user = $request->user();

        return view('body.index', [
            'entries' => $user->bodyEntries()->latest('measured_on')->take(30)->get(),
            'chart' => $body->since($user, CarbonImmutable::today()->subMonths(6))
                ->map(fn ($e) => ['label' => $e->measured_on->translatedFormat('j M'), 'value' => $e->weight_kg])->values(),
            'photos' => $user->progressPhotos()->latest('taken_on')->latest('id')->get()->groupBy(fn ($p) => $p->taken_on->toDateString()),
        ]);
    }

    public function store(BodyEntryRequest $request, BodyLog $body): RedirectResponse
    {
        $body->record($request->user(), $request->validated());

        return back()->with('success', 'Misurazione salvata.');
    }

    public function destroy(BodyEntry $entry): RedirectResponse
    {
        $this->ensureOwns($entry);
        $entry->delete();

        return back()->with('success', 'Misurazione eliminata.');
    }
}
