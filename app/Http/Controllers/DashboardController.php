<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index(Request $request)
    {
        // Static data for now - ready to be replaced with database queries
        $regionalStats = [
            [
                'period' => 'Jul 2025',
                'labor_force' => 2378,
                'employed' => 2293,
                'unemployed' => 86,
                'underemployed' => 241,
                'emp_rate' => 96.4,
                'unemp_rate' => 3.6,
                'underemp_rate' => 10.5,
                'particip_rate' => 57.7
            ],
            [
                'period' => 'Apr 2025',
                'labor_force' => 2385,
                'employed' => 2300,
                'unemployed' => 85,
                'underemployed' => 220,
                'emp_rate' => 96.4,
                'unemp_rate' => 3.6,
                'underemp_rate' => 9.6,
                'particip_rate' => 58.2
            ],
            [
                'period' => 'Jan 2025',
                'labor_force' => 2390,
                'employed' => 2310,
                'unemployed' => 80,
                'underemployed' => 200,
                'emp_rate' => 96.7,
                'unemp_rate' => 3.3,
                'underemp_rate' => 8.7,
                'particip_rate' => 58.6
            ],
            [
                'period' => 'Oct 2024',
                'labor_force' => 2405,
                'employed' => 2325,
                'unemployed' => 80,
                'underemployed' => 160,
                'emp_rate' => 96.7,
                'unemp_rate' => 3.3,
                'underemp_rate' => 6.9,
                'particip_rate' => 59.2
            ],
            [
                'period' => 'Jul 2024',
                'labor_force' => 2394,
                'employed' => 2320,
                'unemployed' => 74,
                'underemployed' => 155,
                'emp_rate' => 96.9,
                'unemp_rate' => 3.1,
                'underemp_rate' => 6.7,
                'particip_rate' => 59.3
            ],
            [
                'period' => 'Apr 2024',
                'labor_force' => 2410,
                'employed' => 2320,
                'unemployed' => 90,
                'underemployed' => 235,
                'emp_rate' => 96.3,
                'unemp_rate' => 3.7,
                'underemp_rate' => 10.1,
                'particip_rate' => 60.0
            ],
            [
                'period' => 'Jan 2024',
                'labor_force' => 2380,
                'employed' => 2290,
                'unemployed' => 90,
                'underemployed' => 230,
                'emp_rate' => 96.2,
                'unemp_rate' => 3.8,
                'underemp_rate' => 10.0,
                'particip_rate' => 59.5
            ]
        ];

        // Chart data (keeping your existing structure)
        $charts = [
            'labor_vs_employment' => [
                'labels' => ['2019', '2020', '2021', '2022', '2023', '2024', '2025'],
                'labor' => [2100, 2000, 2150, 2250, 2300, 2350, 2378],
                'employment_rate' => [94.5, 92.8, 95.2, 96.0, 96.5, 96.3, 96.4]
            ],
            'unemployment_volume' => [
                'labels' => ['2012', '2014', '2016', '2018', '2020', '2022', '2024', '2025'],
                'values' => [120, 110, 105, 95, 145, 88, 85, 86]
            ]
        ];

        return view('home', compact('charts', 'regionalStats'));
    }
}
