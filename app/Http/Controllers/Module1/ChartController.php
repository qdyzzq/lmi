<?php

namespace App\Http\Controllers\Module1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    /**
     * API Method: Returns quarterly data for Chart.js updates
     * Used by: Labor Force Chart & Unemployment Chart
     * Route: GET /api/quarterly/{year}
     */
    public function getQuarterlyData($year)
    {
        // Query the view created for quarterly trends
        $data = DB::table('view_quarterly_labor_metrics')
            ->where('year', $year)
            ->orderBy('quarter', 'asc') // Sort Q1-Q4 for graph lines
            ->get();

        // Return JSON response for Chart.js
        return response()->json($data);
    }

    /**
     * Optional: Get data for a specific year range
     * Route: GET /api/quarterly-range?start=2024&end=2025
     */
    public function getQuarterlyRange(Request $request)
    {
        $startYear = $request->query('start', 2024);
        $endYear = $request->query('end', 2025);

        $data = DB::table('view_quarterly_labor_metrics')
            ->whereBetween('year', [$startYear, $endYear])
            ->orderBy('year', 'asc')
            ->orderBy('quarter', 'asc')
            ->get();

        return response()->json($data);
    }

    /**
     * Optional: Get all available years for dropdowns
     * Route: GET /api/available-years
     */
    public function getAvailableYears()
{
    try {
        // Get unique years from your main table
        $years = DB::table('regional_labor_market_statistics')
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching available years',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Optional: Get latest quarter data (for KPI cards)
     * Route: GET /api/latest-quarter
     */
    public function getLatestQuarter()
    {
        $latest = DB::table('view_quarterly_labor_metrics')
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->first();

        return response()->json($latest);
    }
    
}