<?php

namespace App\Http\Controllers\Module3;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module3\LicensureRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LicensureRateController extends Controller
{
     public function showForm()
    {
        return view('admin.Module3.licensureRatesForm');
    }
    

    public function store(Request $request)
    {
        // Validate the incoming data - fields are now optional
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'sectors' => 'required|array',
            'sectors.*.sector' => 'required|string',
            'sectors.*.data' => 'required|array',
            'sectors.*.data.*.profession' => 'required|string',
            'sectors.*.data.*.takers' => 'nullable|integer|min:0',
            'sectors.*.data.*.passers' => 'nullable|integer|min:0',
            'sectors.*.data.*.passing_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Additional validation: passers cannot exceed takers (when both are present)
        foreach ($validated['sectors'] as $sectorData) {
            foreach ($sectorData['data'] as $professionData) {
                if (!is_null($professionData['passers']) && !is_null($professionData['takers'])) {
                    if ($professionData['passers'] > $professionData['takers']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Invalid data for {$professionData['profession']}: Passers ({$professionData['passers']}) cannot exceed Takers ({$professionData['takers']})."
                        ], 422);
                    }
                }
            }
        }

        try {
            DB::beginTransaction();

            $year = $validated['year'];
            $submittedBy = Auth::id();

            // Delete existing data for this year (to allow updates)
            LicensureRate::where('year', $year)->delete();

            // Store each sector's profession data
            foreach ($validated['sectors'] as $sectorData) {
                $sectorName = $sectorData['sector'];
                
                foreach ($sectorData['data'] as $professionData) {
                    // Only create record if there's actual data
                    if (!is_null($professionData['takers']) && !is_null($professionData['passers'])) {
                        LicensureRate::create([
                            'year' => $year,
                            'sector' => $sectorName,
                            'profession' => $professionData['profession'],
                            'takers' => $professionData['takers'],
                            'passers' => $professionData['passers'],
                            'passing_rate' => $professionData['passing_rate'],
                            'submitted_by' => $submittedBy,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Licensure passing rate data submitted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if data exists for a specific year and return it
     */
    public function checkYear($year)
    {
        $data = LicensureRate::forYear($year)
            ->orderBy('sector')
            ->orderBy('profession')
            ->get();

        $exists = $data->count() > 0;
        
        // Count incomplete entries (professions with null data in defined sectors)
        $incompleteCount = 0;
        
        // Get all expected professions from the sectors configuration
        // This would need to match your JavaScript sectorsData
        // For now, we'll just return what exists
        
        return response()->json([
            'exists' => $exists,
            'data' => $data,
            'incomplete_count' => $incompleteCount
        ]);
    }

    /**
     * Get all licensure rates (optional - for viewing data)
     */
    public function index(Request $request)
    {
        $query = LicensureRate::query();

        // Filter by year if provided
        if ($request->has('year')) {
            $query->forYear($request->year);
        }

        // Filter by sector if provided
        if ($request->has('sector')) {
            $query->bySector($request->sector);
        }

        $licensureRates = $query->with('submitter')
            ->orderBy('year', 'desc')
            ->orderBy('sector')
            ->orderBy('profession')
            ->get();

        return response()->json($licensureRates);
    }

    /**
     * Get data for a specific year
     */
    public function getByYear($year)
    {
        $data = LicensureRate::forYear($year)
            ->orderBy('sector')
            ->orderBy('profession')
            ->get();

        return response()->json($data);
    }

    /**
     * Update a specific entry
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'takers' => 'sometimes|required|integer|min:0',
            'passers' => 'sometimes|required|integer|min:0',
            'passing_rate' => 'sometimes|required|numeric|min:0|max:100',
        ]);

        // Validate passers <= takers if both are being updated
        if (isset($validated['takers']) && isset($validated['passers'])) {
            if ($validated['passers'] > $validated['takers']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Passers cannot exceed takers.'
                ], 422);
            }
        }

        $licensureRate = LicensureRate::findOrFail($id);
        
        // If only takers or passers is updated, recalculate passing_rate
        if (isset($validated['takers']) || isset($validated['passers'])) {
            $takers = $validated['takers'] ?? $licensureRate->takers;
            $passers = $validated['passers'] ?? $licensureRate->passers;
            
            if ($passers > $takers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Passers cannot exceed takers.'
                ], 422);
            }
            
            if ($takers > 0) {
                $validated['passing_rate'] = round(($passers / $takers) * 100, 2);
            }
        }
        
        $licensureRate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully.',
            'data' => $licensureRate
        ]);
    }

    /**
     * Delete a specific entry
     */
    public function destroy($id)
    {
        $licensureRate = LicensureRate::findOrFail($id);
        $licensureRate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entry deleted successfully.'
        ]);
    }

    /**
     * Delete all data for a specific year
     * Used when changing year - deletes old year data
     */
    public function deleteYear($year)
    {
        try {
            $deleted = LicensureRate::where('year', $year)->delete();

            return response()->json([
                'success' => true,
                'message' => "Data for year {$year} deleted successfully.",
                'deleted' => $deleted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics/summary
     */
    public function statistics(Request $request)
    {
        $year = $request->input('year');
        
        $query = LicensureRate::query();
        
        if ($year) {
            $query->forYear($year);
        }

        $stats = [
            'total_records' => $query->count(),
            'sectors' => $query->distinct('sector')->count('sector'),
            'total_takers' => $query->sum('takers'),
            'total_passers' => $query->sum('passers'),
            'average_passing_rate' => round($query->avg('passing_rate'), 2),
            'highest_passing_rate' => $query->max('passing_rate'),
            'lowest_passing_rate' => $query->min('passing_rate'),
        ];

        // Get data grouped by sector
        $bySector = $query->select(
                'sector', 
                DB::raw('SUM(takers) as total_takers'),
                DB::raw('SUM(passers) as total_passers'),
                DB::raw('AVG(passing_rate) as avg_rate')
            )
            ->groupBy('sector')
            ->orderBy('avg_rate', 'desc')
            ->get();

        $stats['by_sector'] = $bySector;

        return response()->json($stats);
    }

    /**
     * Get all available years
     */
    public function getYears()
    {
        $years = LicensureRate::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json($years);
    }

    /**
     * Get all sectors
     */
    public function getSectors()
    {
        $sectors = LicensureRate::select('sector')
            ->distinct()
            ->orderBy('sector')
            ->pluck('sector');

        return response()->json($sectors);
    }

    /**
     * Get top performing professions by passing rate
     */
    public function topPerformers(Request $request)
    {
        $year = $request->input('year');
        $limit = $request->input('limit', 10);
        
        $query = LicensureRate::query();
        
        if ($year) {
            $query->forYear($year);
        }
        
        $topPerformers = $query->orderBy('passing_rate', 'desc')
            ->limit($limit)
            ->get();
            
        return response()->json($topPerformers);
    }
}