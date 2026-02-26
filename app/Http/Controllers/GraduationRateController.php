<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GraduationRate;
use App\Models\DisciplineEnrollment; // ✅ CHANGED: Use enrollment model, not graduate
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
            'graduate_year' => 'required|string',
            'graduation_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string'
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
                    'enrollment_year' => $enrollmentYear,
                    'graduation_rate' => $validated['graduation_rate'],
                    'base_enrollees' => $totalEnrollees,
                    'projected_graduates' => $projectedGraduates,
                    'notes' => $validated['notes'] ?? null
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Graduation rate saved successfully',
                'data' => $graduationRate
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
     * Get all graduation rates
     */
    public function getAllGraduationRates()
    {
        try {
            $rates = GraduationRate::orderBy('graduate_year', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $rates
            ]);
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
            'enrollment_year' => $record->enrollment_year,   // adjust if your column name differs
            'base_enrollees'  => $record->base_enrollees,    // adjust if your column name differs
            'graduation_rate' => $record->graduation_rate,
            'is_default'      => false,
        ],
    ], 200);
}

    /**
     * Get total enrollees for a given academic year.
     * Prefers the Davao Region / Total row (region-wide figure).
     * Falls back to summing all specific-province rows if no Total row exists.
     */
    private function getTotalEnrollees(string $year): int
    {
        $regionRow = DisciplineEnrollment::where('academic_year', $year)
            ->where('province', 'Davao Region')
            ->where('institution_type', 'Total')
            ->first();

        if ($regionRow) {
            return (int) $regionRow->grand_total;
        }

        // Fallback: sum individual province rows (exclude Davao Region to avoid double-count)
        return (int) DisciplineEnrollment::where('academic_year', $year)
            ->where('province', '!=', 'Davao Region')
            ->sum('grand_total');
    }
}