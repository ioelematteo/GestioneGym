<?php

namespace App\Http\Controllers;

use App\Exceptions\AiUnavailableException;
use App\Services\Coach\Coach;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** COACH IA: fai una domanda, l'IA legge i tuoi dati e risponde. */
class CoachController extends Controller
{
    public function index(Request $request): View
    {
        return view('coach.index', [
            'reports' => $request->user()->coachReports()->latest()->take(10)->get(),
            'configured' => filled(config('services.deepseek.key')),
        ]);
    }

    public function store(Request $request, Coach $coach): RedirectResponse
    {
        $request->validate(['question' => ['nullable', 'string', 'max:1000']]);

        try {
            $coach->ask($request->user(), $request->input('question'));
        } catch (AiUnavailableException $e) {
            return back()->withInput()->withErrors(['question' => $e->getMessage()]);
        }

        return redirect()->route('coach.index')->with('success', 'Il coach ha risposto.');
    }
}
