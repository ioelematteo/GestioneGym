<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksOwnership;
use App\Models\TrainingSession;
use App\Services\Progress\Formula;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** STORICO: gli allenamenti fatti. */
class HistoryController extends Controller
{
    use ChecksOwnership;

    public function index(Request $request): View
    {
        return view('history.index', [
            'sessions' => $request->user()->trainingSessions()->with(['workout', 'sets'])->latest('performed_at')->paginate(15),
        ]);
    }

    public function show(Request $request, TrainingSession $session): View
    {
        $this->ensureOwns($session);
        $session->load(['workout', 'sets.exercise', 'sets.state']);

        return view('history.show', [
            'session' => $session,
            'exercises' => $session->sets->groupBy('exercise_id'),
            'records' => $this->records($request, $session),
        ]);
    }

    public function destroy(TrainingSession $session): RedirectResponse
    {
        $this->ensureOwns($session);
        $session->delete();

        return redirect()->route('history.index')->with('success', 'Allenamento eliminato.');
    }

    /** Esercizi dove la serie migliore di oggi batte tutte quelle precedenti. @return list<int> */
    private function records(Request $request, TrainingSession $session): array
    {
        $best = fn ($sets) => $sets->max(fn ($s) => Formula::setScore($s->weight, $s->reps));

        $before = \App\Models\SessionSet::query()
            ->join('training_sessions', 'training_sessions.id', '=', 'session_sets.training_session_id')
            ->where('training_sessions.user_id', $request->user()->id)
            ->where('training_sessions.performed_at', '<', $session->performed_at)
            ->whereIn('session_sets.exercise_id', $session->sets->pluck('exercise_id'))
            ->get(['session_sets.exercise_id', 'session_sets.weight', 'session_sets.reps'])
            ->groupBy('exercise_id');

        return $session->sets->groupBy('exercise_id')
            ->filter(fn ($sets, $id) => isset($before[$id]) && $best($sets) > $best($before[$id]))
            ->keys()
            ->all();
    }
}
