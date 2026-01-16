<?php

namespace App\Http\Controllers;
use App\Models\LaborStats;


use Illuminate\Http\Request;

class PeformanceController extends Controller
{
 public function getKpiData(Request $request)
{
    $year = $request->year;
    $quarter = $request->quarter;

    $data = LaborStats::where('year', $year)
        ->where('quarter', $quarter)
        ->first();

    if (!$data) {
        return response()->json(['message' => 'No data found'], 404);
    }

    return response()->json([
        'participation_rate'   => $data->labor_force_rate,
        'employment_rate'      => $data->employment_rate,
        'underemployment_rate' => $data->underemployment_rate,
        'unemployment_rate'    => $data->unemployment_rate,
    ]);
}
}
