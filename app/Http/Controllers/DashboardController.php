<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard page
     * Handles: Consolidated Regional Statistics Table
     */
    public function index(Request $request)
    {
        // ================================================
        // CONSOLIDATED REGIONAL STATISTICS TABLE
        // ================================================
        $regionalStats = DB::table('regional_labor_market_statistics')
            ->select(
                'year', 
                'month', 
                'labor_force', 
                'employed', 
                'unemployed', 
                'underemployed',
                'employment_rate as emp_rate',
                'unemployment_rate as unemp_rate',
                'underemployment_rate as underemp_rate',
                'labor_force_participation_rate as particip_rate'
            )
            ->whereIn('month', [1, 4, 7, 10])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($stat) {
                $quarterNames = [1 => 'Jan', 4 => 'Apr', 7 => 'Jul', 10 => 'Oct'];
                $stat->period = $quarterNames[$stat->month] . ' ' . $stat->year;
                return $stat;
            });

        return view('home', [
            'regionalStats' => $regionalStats
        ]);
    }
}