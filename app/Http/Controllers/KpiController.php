<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    /**
     * Get available periods (years and months)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailablePeriods()
    {
        try {
            $periods = DB::table('regional_labor_market_statistics')
                ->select('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function($period) {
                    return [
                        'year' => $period->year,
                        'month' => $period->month,
                        'label' => date('F Y', mktime(0, 0, 0, $period->month, 1, $period->year)),
                        'value' => $period->year . '-' . str_pad($period->month, 2, '0', STR_PAD_LEFT)
                    ];
                })
                ->unique('value')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $periods
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available periods',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get KPI cards data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKpiCards(Request $request)
    {
        try {
            $fromView = false;
            
            // If year and month are provided, use table directly
            if ($request->has('year') && $request->has('month')) {
                $kpiData = DB::table('regional_labor_market_statistics')
                    ->where('year', $request->year)
                    ->where('month', $request->month)
                    ->first();
                $fromView = false;
            } else {
                // Try to use the view first, fallback to latest record from table
                $kpiData = DB::table('view_kpi_cards')->first();
                
                if ($kpiData) {
                    $fromView = true;
                } else {
                    // Fallback to latest record from table
                    $kpiData = DB::table('regional_labor_market_statistics')
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->first();
                    $fromView = false;
                }
            }

            if (!$kpiData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No KPI data available'
                ], 404);
            }

            // Format response based on source (view vs table)
            if ($fromView) {
                // Data from view: uses employed_persons, unemployed_persons, etc.
                $response = [
                    'success' => true,
                    'data' => [
                        'employment_rate' => [
                            'rate' => number_format($kpiData->employment_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->employment_rate,
                            'count' => number_format($kpiData->employed_persons),
                            'label' => 'Employed Persons',
                            'count_formatted' => $this->formatLargeNumber($kpiData->employed_persons)
                        ],
                        'unemployment_rate' => [
                            'rate' => number_format($kpiData->unemployment_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->unemployment_rate,
                            'count' => number_format($kpiData->unemployed_persons),
                            'label' => 'Unemployed Persons',
                            'count_formatted' => $this->formatLargeNumber($kpiData->unemployed_persons)
                        ],
                        'underemployment_rate' => [
                            'rate' => number_format($kpiData->underemployment_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->underemployment_rate,
                            'count' => number_format($kpiData->seeking_more_hours),
                            'label' => 'Seeking More Hours',
                            'count_formatted' => $this->formatLargeNumber($kpiData->seeking_more_hours)
                        ],
                        'participation_rate' => [
                            'rate' => number_format($kpiData->participation_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->participation_rate,
                            'active_workforce' => number_format($kpiData->active_workforce),
                            'population' => number_format($kpiData->population_15_plus),
                            'label' => 'Active Workforce vs Pop 15+'
                        ],
                        'period' => [
                            'year' => $kpiData->year,
                            'month' => $kpiData->month,
                            'formatted' => date('F Y', mktime(0, 0, 0, $kpiData->month, 1, $kpiData->year))
                        ]
                    ]
                ];
            } else {
                // Data from table: uses employed, unemployed, etc.
                $response = [
                    'success' => true,
                    'data' => [
                        'employment_rate' => [
                            'rate' => number_format($kpiData->employment_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->employment_rate,
                            'count' => number_format($kpiData->employed),
                            'label' => 'Employed Persons',
                            'target' => '>95.0%'
                        ],
                        'unemployment_rate' => [
                            'rate' => number_format($kpiData->unemployment_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->unemployment_rate,
                            'count' => number_format($kpiData->unemployed),
                            'label' => 'Unemployed Persons',
                            'count_formatted' => $this->formatLargeNumber($kpiData->unemployed)
                        ],
                        'underemployment_rate' => [
                            'rate' => number_format($kpiData->underemployment_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->underemployment_rate,
                            'count' => number_format($kpiData->underemployed),
                            'label' => 'Seeking More Hours',
                            'count_formatted' => $this->formatLargeNumber($kpiData->underemployed)
                        ],
                        'participation_rate' => [
                            'rate' => number_format($kpiData->labor_force_participation_rate, 1) . '%',
                            'raw_value' => (float)$kpiData->labor_force_participation_rate,
                            'active_workforce' => number_format($kpiData->labor_force),
                            'population' => number_format($kpiData->household_population),
                            'label' => 'Active Workforce vs Pop 15+'
                        ],
                        'period' => [
                            'year' => $kpiData->year,
                            'month' => $kpiData->month,
                            'formatted' => date('F Y', mktime(0, 0, 0, $kpiData->month, 1, $kpiData->year))
                        ]
                    ]
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching KPI data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format large numbers (e.g., 86000 -> 86k, 241000 -> 241k)
     *
     * @param int $number
     * @return string
     */
    private function formatLargeNumber($number)
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 0) . 'k';
        }
        return number_format($number);
    }

    /**
     * Get KPI cards data for a specific period
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKpiCardsByPeriod(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12'
        ]);

        try {
            $kpiData = DB::table('regional_labor_market_statistics')
                ->where('year', $request->year)
                ->where('month', $request->month)
                ->first();

            if (!$kpiData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified period'
                ], 404);
            }

            $response = [
                'success' => true,
                'data' => [
                    'employment_rate' => [
                        'rate' => number_format($kpiData->employment_rate, 1) . '%',
                        'raw_value' => $kpiData->employment_rate,
                        'count' => number_format($kpiData->employed),
                        'label' => 'Employed Persons'
                    ],
                    'unemployment_rate' => [
                        'rate' => number_format($kpiData->unemployment_rate, 1) . '%',
                        'raw_value' => $kpiData->unemployment_rate,
                        'count' => number_format($kpiData->unemployed),
                        'label' => 'Unemployed Persons',
                        'count_formatted' => $this->formatLargeNumber($kpiData->unemployed)
                    ],
                    'underemployment_rate' => [
                        'rate' => number_format($kpiData->underemployment_rate, 1) . '%',
                        'raw_value' => $kpiData->underemployment_rate,
                        'count' => number_format($kpiData->underemployed),
                        'label' => 'Seeking More Hours',
                        'count_formatted' => $this->formatLargeNumber($kpiData->underemployed)
                    ],
                    'participation_rate' => [
                        'rate' => number_format($kpiData->labor_force_participation_rate, 1) . '%',
                        'raw_value' => $kpiData->labor_force_participation_rate,
                        'active_workforce' => number_format($kpiData->labor_force),
                        'population' => number_format($kpiData->household_population),
                        'label' => 'Active Workforce vs Pop 15+'
                    ],
                    'period' => [
                        'year' => $kpiData->year,
                        'month' => $kpiData->month,
                        'formatted' => date('F Y', mktime(0, 0, 0, $kpiData->month, 1, $kpiData->year))
                    ]
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching KPI data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}