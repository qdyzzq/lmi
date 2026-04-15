<?php

namespace App\Http\Controllers\Module3;

use App\Http\Controllers\Controller;
use App\Models\Module3\GraduationRate;
use App\Models\Module3\DisciplineEnrollment; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraduationRateController extends Controller
{
    /**
     * Get enrollment data for a specific academic year
     */
    public function getEnrollmentData($academicYear)
    {
        try {
            // Get total enrollees for the specified year from ENROLLMENT table
            $totalEnrollees = $this->getTotalEnrollees($academicYear);

            return response()->json([
                'success' => true,
                'academic_year' => $academicYear,
                'total_enrollees' => $totalEnrollees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching enrollment data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate projected graduates based on enrollment from 4 years ago
     */
    public function calculateProjectedGraduates(Request $request)
    {
        $graduateYear = $request->input('graduate_year');
        $graduationRate = $request->input('graduation_rate', 60);

        // Calculate enrollment year (4 years prior)
        $enrollmentYear = GraduationRate::calculateEnrollmentYear($graduateYear);

        if (!$enrollmentYear) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid graduate year format'
            ], 400);
        }

        // Get enrollees from 4 years ago from ENROLLMENT table
        $totalEnrollees = $this->getTotalEnrollees($enrollmentYear);

        // Calculate projected graduates
        $projectedGraduates = round($totalEnrollees * ($graduationRate / 100));

        return response()->json([
            'success' => true,
            'graduate_year' => $graduateYear,
            'enrollment_year' => $enrollmentYear,
            'base_enrollees' => $totalEnrollees,
            'graduation_rate' => $graduationRate,
            'projected_graduates' => $projectedGraduates
        ]);
    }

    /**
     * Save or update graduation rate
     */
    public function saveGraduationRate(Request $request)
    {
        $validated = $request->validate([
            'graduate_year'  => 'required|string',
            'graduation_rate' => 'required|numeric|min:0|max:100',
            'notes'          => 'nullable|string',
            'description'    => 'nullable|string',
        ]);

        try {
            $enrollmentYear = GraduationRate::calculateEnrollmentYear($validated['graduate_year']);

            // Get enrollees from 4 years ago from ENROLLMENT table
            $totalEnrollees = $this->getTotalEnrollees($enrollmentYear);

            // Calculate projected graduates
            $projectedGraduates = round($totalEnrollees * ($validated['graduation_rate'] / 100));

            // Create or update graduation rate
            $graduationRate = GraduationRate::updateOrCreate(
                ['graduate_year' => $validated['graduate_year']],
                [
                    'enrollment_year'     => $enrollmentYear,
                    'graduation_rate'     => $validated['graduation_rate'],
                    'base_enrollees'      => $totalEnrollees,
                    'projected_graduates' => $projectedGraduates,
                    'notes'               => $validated['notes'] ?? null,
                    'description'         => $validated['description'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Graduation rate saved successfully',
                'data'    => $graduationRate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving graduation rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get graduation rate for a specific year
     */
    public function getGraduationRate($graduateYear)
    {
        try {
            $graduationRate = GraduationRate::where('graduate_year', $graduateYear)->first();

            // Always recalculate base_enrollees live from the enrollment table
            // so stale saved values never show up
            $enrollmentYear = GraduationRate::calculateEnrollmentYear($graduateYear);
            $liveEnrollees  = $this->getTotalEnrollees($enrollmentYear);

            if ($graduationRate) {
                // Update the stored base_enrollees + projected_graduates to stay in sync
                $graduationRate->base_enrollees      = $liveEnrollees;
                $graduationRate->projected_graduates = round($liveEnrollees * ($graduationRate->graduation_rate / 100));

                return response()->json([
                    'success' => true,
                    'data'    => $graduationRate
                ]);
            }

            // No saved record — return default (60%) projection
            return response()->json([
                'success' => true,
                'data' => [
                    'graduate_year'       => $graduateYear,
                    'enrollment_year'     => $enrollmentYear,
                    'graduation_rate'     => 60.00,
                    'base_enrollees'      => $liveEnrollees,
                    'projected_graduates' => round($liveEnrollees * 0.6),
                    'description'         => null,
                    'is_default'          => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching graduation rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all graduation rates — recalculates base_enrollees + projected_graduates
     * live so stale saved values (e.g. from old Total rows) are never returned.
     */
    public function getAllGraduationRates()
    {
        try {
            $rates = GraduationRate::orderBy('graduate_year', 'desc')->get();

            foreach ($rates as $rate) {
                $liveEnrollees             = $this->getTotalEnrollees($rate->enrollment_year);
                $rate->base_enrollees      = $liveEnrollees;
                $rate->projected_graduates = round($liveEnrollees * ($rate->graduation_rate / 100));
            }

            return response()->json($rates);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching graduation rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete graduation rate
     */
    public function deleteGraduationRate($graduateYear)
    {
        try {
            GraduationRate::where('graduate_year', $graduateYear)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Graduation rate deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting graduation rate: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkYear(string $graduateYear): \Illuminate\Http\JsonResponse
    {
        // Look for a real saved record (not a computed default)
        $record = \App\Models\GraduationRate::where('graduate_year', $graduateYear)->first();

        if (!$record) {
            // No saved record — return 404 so the blade falls through to loadNewYear()
            return response()->json([
                'exists' => false,
                'data'   => null,
            ], 404);
        }

        // Real record exists — return it so the blade can show the modal
        return response()->json([
            'exists' => true,
            'data'   => [
                'id'              => $record->id,
                'graduate_year'   => $record->graduate_year,
                'enrollment_year' => $record->enrollment_year,
                'base_enrollees'  => $record->base_enrollees,
                'graduation_rate' => $record->graduation_rate,
                'description'     => $record->description,
                'is_default'      => false,
            ],
        ], 200);
    }

    /**
     * Get total enrollees for a given academic year.
     * Sums Private + Public rows for Davao Region.
     * Falls back to summing all other province rows if no Davao Region data exists.
     */
    private function getTotalEnrollees(?string $year): int
    {
        if (!$year) return 0;

        // Sum Private + Public for Davao Region
        $regionTotal = (int) DisciplineEnrollment::where('academic_year', $year)
            ->where('province', 'Davao Region')
            ->whereIn('institution_type', ['Private', 'Public'])
            ->sum('grand_total');

        if ($regionTotal > 0) {
            return $regionTotal;
        }

        // Fallback: sum all specific-province rows (exclude Davao Region to avoid double-count)
        return (int) DisciplineEnrollment::where('academic_year', $year)
            ->where('province', '!=', 'Davao Region')
            ->whereIn('institution_type', ['Private', 'Public'])
            ->sum('grand_total');
    }
}