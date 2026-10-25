<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Services\Progress\WeightProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** TRACKER DEI PESI: tutti gli esercizi, e il dettaglio di uno. */
class ProgressController extends Controller
{
    public function index(Request $request, WeightProgress $weights): View
    {
        return view('progress.index', ['lifts' => $weights->summary($request->user())]);
    }

    public function show(Request $request, Exercise $exercise, WeightProgress $weights): View
    {
        $rows = $weights->perSession($request->user(), $exercise->id);
        abort_if($rows->isEmpty(), 404);

        return view('progress.show', [
            'exercise' => $exercise,
            'rows' => $rows->reverse()->values(),
            'chart' => $rows->map(fn ($r) => ['label' => $r['date']->translatedFormat('j M'), 'avg' => $r['avg'], 'max' => $r['max']])->values(),
        ]);
    }
}
