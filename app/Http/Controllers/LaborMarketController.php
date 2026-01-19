<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LaborMarketController extends Controller
{
    /**
     * Store labor market data
     * Route: POST /api/labor-market/store
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|in:1,4,7,10',
            'household_population' => 'required|integer|min:0',
            'labor_force' => 'nullable|integer|min:0',
            'employed' => 'nullable|integer|min:0',
            'underemployed' => 'nullable|integer|min:0',
            'unemployed' => 'nullable|integer|min:0',
            'labor_force_participation_rate' => 'required|numeric|min:0|max:100',
            'employment_rate' => 'required|numeric|min:0|max:100',
            'underemployment_rate' => 'required|numeric|min:0|max:100',
            'unemployment_rate' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prepare data for insertion/update
            $data = [
                'household_population' => $request->household_population ? (int)$request->household_population : null,
                'labor_force' => $request->labor_force ? (int)$request->labor_force : null,
                'employed' => $request->employed ? (int)$request->employed : null,
                'underemployed' => $request->underemployed ? (int)$request->underemployed : null,
                'unemployed' => $request->unemployed ? (int)$request->unemployed : null,
                'labor_force_participation_rate' => $request->labor_force_participation_rate,
                'employment_rate' => $request->employment_rate,
                'underemployment_rate' => $request->underemployment_rate,
                'unemployment_rate' => $request->unemployment_rate,
                'updated_at' => now()
            ];

            // Check if record already exists for this year and month
            $exists = DB::table('regional_labor_market_statistics')
                ->where('year', $request->year)
                ->where('month', $request->month)
                ->exists();

            if ($exists) {
                // Update existing record
                DB::table('regional_labor_market_statistics')
                    ->where('year', $request->year)
                    ->where('month', $request->month)
                    ->update($data);

                return response()->json([
                    'success' => true,
                    'message' => "Labor market data for {$this->getMonthName($request->month)} {$request->year} updated successfully!",
                    'data' => [
                        'year' => $request->year,
                        'month' => $request->month,
                        'action' => 'updated'
                    ]
                ]);
            } else {
                // Insert new record
                $data['year'] = $request->year;
                $data['month'] = $request->month;
                $data['created_at'] = now();

                DB::table('regional_labor_market_statistics')->insert($data);

                return response()->json([
                    'success' => true,
                    'message' => "Labor market data for {$this->getMonthName($request->month)} {$request->year} saved successfully!",
                    'data' => [
                        'year' => $request->year,
                        'month' => $request->month,
                        'action' => 'created'
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to get month name
     */
    private function getMonthName($month)
    {
        $months = [
            1 => 'January',
            4 => 'April',
            7 => 'July',
            10 => 'October'
        ];
        
        return $months[$month] ?? 'Unknown';
    }
}