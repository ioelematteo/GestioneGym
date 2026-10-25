<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardData;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** DASHBOARD: chiede tutto a DashboardData e lo passa alla vista. */
class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardData $dashboard): View
    {
        $periods = config('gymlog.periods');
        $weeks = array_key_exists((int) $request->query('periodo'), $periods) ? (int) $request->query('periodo') : 4;

        return view('dashboard', [
            'data' => $dashboard->for($request->user(), $weeks),
            'weeks' => $weeks,
            'periods' => $periods,
        ]);
    }
}
