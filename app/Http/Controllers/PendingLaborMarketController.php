<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegionalLaborMarketStatistic;
use App\Models\PendingLaborMarketData;
use Illuminate\Support\Facades\Auth;

class LaborMarketController extends Controller
{
    // Live polling endpoint — returns current pending record count
    public function counts()
    {
        return response()->json([
            'pending' => PendingLaborMarketData::count(),
        ]);
    }

    // Display the statistician review page
    public function index()
    {
        $pendingRecords = PendingLaborMarketData::with('submittedBy')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('statisticianReview', compact('pendingRecords'));
    }

    // Check for duplicates in PENDING and FINAL tables (called from dashboard when admin submits)
    public function check(Request $request)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer'
            ]);

            // Check if data already exists in PENDING table
            $existsInPending = PendingLaborMarketData::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($existsInPending) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' is already pending verification.'
                ]);
            }

            // Check if data already exists in FINAL table
            $existsInFinal = RegionalLaborMarketStatistic::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($existsInFinal) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the database.'
                ]);
            }

            return response()->json([
                'exists' => false
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'exists' => true,
                'message' => 'Error checking data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Admin submits data to PENDING table (awaiting statistician review)
    public function submitPending(Request $request)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer',
                'household_population' => 'nullable|numeric',
                'labor_force' => 'nullable|numeric',
                'employed' => 'nullable|numeric',
                'underemployed' => 'nullable|numeric',
                'unemployed' => 'nullable|numeric',
                'labor_force_participation_rate' => 'nullable|numeric',
                'employment_rate' => 'nullable|numeric',
                'underemployment_rate' => 'nullable|numeric',
                'unemployment_rate' => 'nullable|numeric',
            ]);

            // Add the user who submitted (the admin)
            $validated['submitted_by'] = Auth::id();

            // Save to PENDING table (NOT final table)
            $pendingData = PendingLaborMarketData::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data submitted for verification successfully!',
                'data' => $pendingData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Check for duplicates in FINAL table (called from statistician page before posting)
    public function checkPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer'
            ]);

            // Check if data already exists in the FINAL database
            $exists = RegionalLaborMarketStatistic::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the database.'
                ]);
            }

            return response()->json([
                'exists' => false
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'exists' => true,
                'message' => 'Error checking data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Statistician posts verified/edited data from PENDING to FINAL table
    public function post(Request $request)
    {
        try {
            $validated = $request->validate([
                'pending_id' => 'required|exists:pending_labor_market_data,id',
                'year' => 'required|integer',
                'month' => 'required|integer',
                'household_population' => 'nullable|numeric',
                'labor_force' => 'nullable|numeric',
                'employed' => 'nullable|numeric',
                'underemployed' => 'nullable|numeric',
                'unemployed' => 'nullable|numeric',
                'labor_force_participation_rate' => 'nullable|numeric',
                'employment_rate' => 'nullable|numeric',
                'underemployment_rate' => 'nullable|numeric',
                'unemployment_rate' => 'nullable|numeric',
                'overwrite' => 'nullable|boolean'
            ]);

            $pendingId = $validated['pending_id'];
            $overwrite = $validated['overwrite'] ?? false;
            
            // Remove non-data fields
            unset($validated['pending_id']);
            unset($validated['overwrite']);

            // Check if data already exists in FINAL table
            $existingRecord = RegionalLaborMarketStatistic::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->first();

            if ($existingRecord) {
                if (!$overwrite) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the database.'
                    ], 422);
                }
                
                // Update existing record
                $existingRecord->update($validated);
                $finalData = $existingRecord;
                $message = 'Data successfully updated in the database!';
            } else {
                // Create new record in FINAL table
                $finalData = RegionalLaborMarketStatistic::create($validated);
                $message = 'Data successfully posted to database!';
            }

            // Delete from PENDING table (data has been verified and posted)
            PendingLaborMarketData::destroy($pendingId);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $finalData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}