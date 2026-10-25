<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksOwnership;
use App\Http\Requests\WorkoutRequest;
use App\Models\Exercise;
use App\Models\Workout;
use App\Services\Training\WorkoutPlanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** SCHEDE: crea, modifica, elimina. */
class WorkoutController extends Controller
{
    use ChecksOwnership;

    public function create(): View
    {
        return $this->form(new Workout(['color' => 'rosso']), []);
    }

    public function store(WorkoutRequest $request, WorkoutPlanner $planner): RedirectResponse
    {
        $planner->save($request->user(), $request->workoutData(), $request->rows());

        return redirect()->route('train.index')->with('success', 'Scheda creata.');
    }

    public function edit(Workout $workout): View
    {
        $this->ensureOwns($workout);

        $rows = $workout->items()->with('exercise')->get()->map(fn ($item) => [
            'name' => $item->exercise->name,
            'sets' => $item->sets,
            'reps_target' => $item->reps_target,
            'weight_target' => $item->weight_target,
            'rest_seconds' => $item->rest_seconds,
            'notes' => $item->notes,
        ])->all();

        return $this->form($workout, $rows);
    }

    public function update(WorkoutRequest $request, Workout $workout, WorkoutPlanner $planner): RedirectResponse
    {
        $this->ensureOwns($workout);
        $planner->save($request->user(), $request->workoutData(), $request->rows(), $workout);

        return redirect()->route('train.index')->with('success', 'Scheda aggiornata.');
    }

    public function destroy(Workout $workout): RedirectResponse
    {
        $this->ensureOwns($workout);
        $workout->delete(); // soft delete: lo storico resta

        return redirect()->route('train.index')->with('success', "«{$workout->name}» eliminata. Lo storico resta.");
    }

    private function form(Workout $workout, array $rows): View
    {
        // dopo un errore di validazione rimostro quello che avevi scritto
        $rows = old('rows', $rows ?: [['name' => '', 'sets' => 3, 'reps_target' => '8-10', 'weight_target' => null, 'rest_seconds' => 90, 'notes' => '']]);

        return view('workouts.form', [
            'workout' => $workout,
            'rows' => array_values($rows),
            'catalog' => Exercise::orderBy('name')->pluck('name'),
        ]);
    }
}
