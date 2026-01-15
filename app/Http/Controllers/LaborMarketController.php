<?php

namespace App\Http\Controllers;

use App\Models\RegionalLaborMarketStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LaborMarketController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'household_population' => 'nullable|numeric|min:0',
            'labor_force' => 'nullable|numeric|min:0',
            'employed' => 'nullable|numeric|min:0',
            'underemployed' => 'nullable|numeric|min:0',
            'unemployed' => 'nullable|numeric|min:0',
            'labor_force_participation_rate' => 'nullable|numeric|min:0|max:100',
            'employment_rate' => 'nullable|numeric|min:0|max:100',
            'underemployment_rate' => 'nullable|numeric|min:0|max:100',
            'unemployment_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if record already exists for this year/month
            $existing = RegionalLaborMarketStatistic::where('year', $request->year)
                ->where('month', $request->month)
                ->first();

            if ($existing) {
                // Update existing record
                $existing->update($request->all());
                $statistic = $existing;
                $message = 'Labor market data updated successfully';
            } else {
                // Create new record
                $statistic = RegionalLaborMarketStatistic::create($request->all());
                $message = 'Labor market data saved successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $statistic
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving data: ' . $e->getMessage()
            ], 500);
        }
    }
}