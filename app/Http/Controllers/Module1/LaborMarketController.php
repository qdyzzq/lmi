<?php

namespace App\Http\Controllers\Module1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module1\RegionalLaborMarketStatistic;
use App\Models\Module1\PendingLaborMarketData;
use Illuminate\Support\Facades\DB;

class LaborMarketController extends Controller
{
    public function index()
    {
        // Add pagination here - 10 items per page
        $pendingRecords = PendingLaborMarketData::with('submittedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(1); // Changed from get() to paginate()
        
        return view('statistician.Module1.statisticianReview', compact('pendingRecords'));
    }

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
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' is already pending review.'
                ]);
            }

            // Check if data already exists in FINAL database
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
                'unemployment_rate' => 'nullable|numeric'
            ]);

            // Double-check for duplicates
            $existsInPending = PendingLaborMarketData::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($existsInPending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' is already pending review.'
                ], 422);
            }

            $existsInFinal = RegionalLaborMarketStatistic::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($existsInFinal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the database.'
                ], 422);
            }

            // Add the authenticated user's ID
            $validated['submitted_by'] = auth()->id();

            // Create pending record
            $pendingData = PendingLaborMarketData::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data submitted successfully to pending queue!',
                'data' => $pendingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method for statistician to check before posting (matches route name)
    public function checkPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer'
            ]);

            // Only check FINAL database (not pending)
            $existsInFinal = RegionalLaborMarketStatistic::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($existsInFinal) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the final database.'
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

    // Method for statistician to post verified data (matches route name)
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
                'unemployment_rate' => 'nullable|numeric'
            ]);

            // Start transaction
            DB::beginTransaction();

            try {
                // Double-check for duplicates in final database
                $existsInFinal = RegionalLaborMarketStatistic::where('year', $validated['year'])
                    ->where('month', $validated['month'])
                    ->exists();

                if ($existsInFinal) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the final database.'
                    ], 422);
                }

                // Create record in final database
                $finalData = RegionalLaborMarketStatistic::create([
                    'year' => $validated['year'],
                    'month' => $validated['month'],
                    'household_population' => $validated['household_population'],
                    'labor_force' => $validated['labor_force'],
                    'employed' => $validated['employed'],
                    'underemployed' => $validated['underemployed'],
                    'unemployed' => $validated['unemployed'],
                    'labor_force_participation_rate' => $validated['labor_force_participation_rate'],
                    'employment_rate' => $validated['employment_rate'],
                    'underemployment_rate' => $validated['underemployment_rate'],
                    'unemployment_rate' => $validated['unemployment_rate'],
                ]);

                // Delete from pending table
                PendingLaborMarketData::where('id', $validated['pending_id'])->delete();

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data successfully posted to the database!',
                    'data' => $finalData
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error posting data: ' . $e->getMessage()
            ], 500);
        }
    }

    // New method: Check if data exists in FINAL database before posting
    public function checkBeforePost(Request $request)
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer'
            ]);

            // Only check FINAL database (not pending)
            $existsInFinal = RegionalLaborMarketStatistic::where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($existsInFinal) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the final database.'
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

    // New method: Post verified data to final database
    public function postVerifiedData(Request $request)
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
                'unemployment_rate' => 'nullable|numeric'
            ]);

            // Start transaction
            DB::beginTransaction();

            try {
                // Double-check for duplicates in final database
                $existsInFinal = RegionalLaborMarketStatistic::where('year', $validated['year'])
                    ->where('month', $validated['month'])
                    ->exists();

                if ($existsInFinal) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data for ' . date('F', mktime(0, 0, 0, $validated['month'], 1)) . ' ' . $validated['year'] . ' already exists in the final database.'
                    ], 422);
                }

                // Create record in final database
                $finalData = RegionalLaborMarketStatistic::create([
                    'year' => $validated['year'],
                    'month' => $validated['month'],
                    'household_population' => $validated['household_population'],
                    'labor_force' => $validated['labor_force'],
                    'employed' => $validated['employed'],
                    'underemployed' => $validated['underemployed'],
                    'unemployed' => $validated['unemployed'],
                    'labor_force_participation_rate' => $validated['labor_force_participation_rate'],
                    'employment_rate' => $validated['employment_rate'],
                    'underemployment_rate' => $validated['underemployment_rate'],
                    'unemployment_rate' => $validated['unemployment_rate'],
                ]);

                // Delete from pending table
                PendingLaborMarketData::where('id', $validated['pending_id'])->delete();

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data successfully posted to the database!',
                    'data' => $finalData
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error posting data: ' . $e->getMessage()
            ], 500);
        }
    }
}