<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksOwnership;
use App\Http\Requests\StoreSessionRequest;
use App\Models\SetState;
use App\Models\Workout;
use App\Services\Body\BodyLog;
use App\Services\Training\LastSessionLookup;
use App\Services\Training\NextWorkoutPicker;
use App\Services\Training\SessionRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ALLENAMENTO (la pagina da usare in palestra)
 * index -> scegli la scheda | show -> segni le serie | store -> salvi
 */
class TrainController extends Controller
{
    use ChecksOwnership;

    public function index(Request $request, NextWorkoutPicker $picker): View
    {
        return view('train.index', ['workouts' => $picker->ordered($request->user())]);
    }

    public function show(Request $request, Workout $workout, LastSessionLookup $lookup): View
    {
        $this->ensureOwns($workout);
        $last = $lookup->forWorkout($request->user(), $workout);

        return view('train.show', [
            'workout' => $workout->load('items.exercise'),
            'last' => $last,
            'lastSets' => $lookup->setsByExercise($last),
            'states' => SetState::orderBy('position')->get(),
        ]);
    }

    public function store(StoreSessionRequest $request, Workout $workout, SessionRecorder $recorder, BodyLog $body): RedirectResponse
    {
        $this->ensureOwns($workout);

        $session = $recorder->record($request->user(), $workout, $request->input('sets', []), [
            'date' => $request->date('date')->toDateString(),
            'duration_minutes' => $request->integer('duration_minutes') ?: null,
            'notes' => $request->input('notes'),
        ]);

        if ($session === null) {
            return back()->withInput()->withErrors(['sets' => 'Segna almeno una serie (servono le rep) prima di salvare.']);
        }

        if ($request->filled('body_weight')) {
            $body->record($request->user(), [
                'measured_on' => $request->date('date')->toDateString(),
                'weight_kg' => $request->float('body_weight'),
            ]);
        }

        return redirect()->route('history.show', $session)
            ->with('success', 'Allenamento salvato.')
            ->with('clear_draft', $workout->id);
    }
}
