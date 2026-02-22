<?php

namespace App\Http\Controllers;

use App\Models\DisciplineGraduate;
use App\Models\DisciplineEnrollment; // Assuming you have an enrollment model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisciplineGraduateController extends Controller
{
    /**
     * Show the discipline graduate form
     */
    public function showForm()
    {
        return view('admin.graduateForm');
    }

    /**
     * Get available enrollment years (for the dropdown)
     */
    public function getEnrollmentYears()
    {
        try {
            // Get distinct academic years from enrollment data, ordered by most recent
            $years = DisciplineEnrollment::distinct()
                ->orderBy('academic_year', 'desc')
                ->pluck('academic_year');

            return response()->json([
                'success' => true,
                'years' => $years
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching enrollment years: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enrollment data for a specific year, province, and institution type
     */
    public function getEnrollmentData($academicYear, Request $request)
    {
        try {
            $province = $request->query('province');
            $institutionType = $request->query('institution_type');

            if (!$province || !$institutionType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Province and institution_type are required'
                ], 400);
            }

            $enrollment = DisciplineEnrollment::where('academic_year', $academicYear)
                ->where('province', $province)
                ->where('institution_type', $institutionType)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No enrollment data found for the selected criteria'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'academic_year' => $enrollment->academic_year,
                    'province' => $enrollment->province,
                    'institution_type' => $enrollment->institution_type,
                    'grand_total' => $enrollment->grand_total,
                    'disciplines' => [
                        'agriculture' => $enrollment->agriculture,
                        'architecture' => $enrollment->architecture,
                        'business' => $enrollment->business,
                        'criminal_justice' => $enrollment->criminal_justice,
                        'education' => $enrollment->education,
                        'engineering' => $enrollment->engineering,
                        'arts' => $enrollment->arts,
                        'humanities' => $enrollment->humanities,
                        'it' => $enrollment->it,
                        'law' => $enrollment->law,
                        'maritime' => $enrollment->maritime,
                        'mass_comm' => $enrollment->mass_comm,
                        'mathematics' => $enrollment->mathematics,
                        'medical' => $enrollment->medical,
                        'natural_science' => $enrollment->natural_science,
                        'religion' => $enrollment->religion,
                        'service_trades' => $enrollment->service_trades,
                        'social_sciences' => $enrollment->social_sciences,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching enrollment data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store projection-based graduate data
     * This method calculates projected graduates based on enrollment data × percentage
     */
    public function storeProjection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enrollment_year' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'graduation_year' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'province' => 'required|string',
            'institution_type' => 'required|in:Private,Public',
            'graduation_percentage' => 'required|numeric|min:0|max:100',
            'enrollment_data' => 'required|array',
            'enrollment_data.grand_total' => 'required|integer|min:0',
            'enrollment_data.disciplines' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $enrollmentYear = $request->enrollment_year;
            $graduationYear = $request->graduation_year;
            $province = $request->province;
            $institutionType = $request->institution_type;
            $percentage = $request->graduation_percentage / 100; // Convert to decimal
            $enrollmentDisciplines = $request->enrollment_data['disciplines'];
            $submittedBy = Auth::id();

            // Calculate projected graduates for each discipline
            $projectedDisciplines = [];
            $grandTotal = 0;

            foreach ($enrollmentDisciplines as $key => $enrollmentCount) {
                $projected = round($enrollmentCount * $percentage);
                $projectedDisciplines[$key] = $projected;
                $grandTotal += $projected;
            }

            // Use updateOrCreate to handle both insert and update
            $graduate = DisciplineGraduate::updateOrCreate(
                [
                    'academic_year' => $graduationYear,
                    'province' => $province,
                    'institution_type' => $institutionType
                ],
                [
                    'agriculture' => $projectedDisciplines['agriculture'] ?? 0,
                    'architecture' => $projectedDisciplines['architecture'] ?? 0,
                    'business' => $projectedDisciplines['business'] ?? 0,
                    'criminal_justice' => $projectedDisciplines['criminal_justice'] ?? 0,
                    'education' => $projectedDisciplines['education'] ?? 0,
                    'engineering' => $projectedDisciplines['engineering'] ?? 0,
                    'arts' => $projectedDisciplines['arts'] ?? 0,
                    'humanities' => $projectedDisciplines['humanities'] ?? 0,
                    'it' => $projectedDisciplines['it'] ?? 0,
                    'law' => $projectedDisciplines['law'] ?? 0,
                    'maritime' => $projectedDisciplines['maritime'] ?? 0,
                    'mass_comm' => $projectedDisciplines['mass_comm'] ?? 0,
                    'mathematics' => $projectedDisciplines['mathematics'] ?? 0,
                    'medical' => $projectedDisciplines['medical'] ?? 0,
                    'natural_science' => $projectedDisciplines['natural_science'] ?? 0,
                    'religion' => $projectedDisciplines['religion'] ?? 0,
                    'service_trades' => $projectedDisciplines['service_trades'] ?? 0,
                    'social_sciences' => $projectedDisciplines['social_sciences'] ?? 0,
                    'grand_total' => $grandTotal,
                    'submitted_by' => $submittedBy,
                    // Store metadata about the projection
                    'enrollment_year' => $enrollmentYear,
                    'graduation_percentage' => $request->graduation_percentage,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Graduate projection data saved successfully.',
                'data' => $graduate
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
     * Check if data exists for a specific year + province + institution type and return it
     */
    public function checkYear($academicYear, Request $request)
    {
        try {
            $province = $request->query('province');
            $institutionType = $request->query('institution_type');

            if (!$province || !$institutionType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Province and institution_type are required'
                ], 400);
            }

            $graduate = DisciplineGraduate::where('academic_year', $academicYear)
                ->where('province', $province)
                ->where('institution_type', $institutionType)
                ->first();

            if (!$graduate) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'exists' => true,
                'data' => [
                    'id' => $graduate->id,
                    'academic_year' => $graduate->academic_year,
                    'enrollment_year' => $graduate->enrollment_year ?? null,
                    'graduation_percentage' => $graduate->graduation_percentage ?? null,
                    'province' => $graduate->province,
                    'institution_type' => $graduate->institution_type,
                    'disciplines' => [
                        'agriculture' => $graduate->agriculture,
                        'architecture' => $graduate->architecture,
                        'business' => $graduate->business,
                        'criminal_justice' => $graduate->criminal_justice,
                        'education' => $graduate->education,
                        'engineering' => $graduate->engineering,
                        'arts' => $graduate->arts,
                        'humanities' => $graduate->humanities,
                        'it' => $graduate->it,
                        'law' => $graduate->law,
                        'maritime' => $graduate->maritime,
                        'mass_comm' => $graduate->mass_comm,
                        'mathematics' => $graduate->mathematics,
                        'medical' => $graduate->medical,
                        'natural_science' => $graduate->natural_science,
                        'religion' => $graduate->religion,
                        'service_trades' => $graduate->service_trades,
                        'social_sciences' => $graduate->social_sciences,
                    ],
                    'grand_total' => $graduate->grand_total,
                    'created_at' => $graduate->created_at,
                    'updated_at' => $graduate->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking year: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific graduate record
     */
    public function delete($academicYear)
    {
        try {
            $deleted = DisciplineGraduate::where('academic_year', $academicYear)->delete();

            if ($deleted > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found to delete'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics summary
     */
    public function statistics(Request $request)
    {
        try {
            $academicYear = $request->input('academic_year');
            $province = $request->input('province');
            $institutionType = $request->input('institution_type');

            $query = DisciplineGraduate::query();

            if ($academicYear) {
                $query->where('academic_year', $academicYear);
            }
            if ($province && $province !== 'All Provinces') {
                $query->where('province', $province);
            }
            if ($institutionType) {
                $query->where('institution_type', $institutionType);
            }

            $graduates = $query->get();

            if ($graduates->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found'
                ], 404);
            }

            $stats = [
                'total_graduates' => $graduates->sum('grand_total'),
                'by_province' => $graduates->groupBy('province')
                    ->map(fn($group) => $group->sum('grand_total'))
                    ->toArray(),
                'by_institution_type' => $graduates->groupBy('institution_type')
                    ->map(fn($group) => $group->sum('grand_total'))
                    ->toArray(),
                'top_disciplines' => [
                    'Agriculture, Forestry, Fisheries' => $graduates->sum('agriculture'),
                    'Architecture and Town Planning' => $graduates->sum('architecture'),
                    'Business Administration and Related' => $graduates->sum('business'),
                    'Criminal Justice Education' => $graduates->sum('criminal_justice'),
                    'Education Science and Teacher Training' => $graduates->sum('education'),
                    'Engineering and Technology' => $graduates->sum('engineering'),
                    'Fine and Applied Arts' => $graduates->sum('arts'),
                    'Humanities' => $graduates->sum('humanities'),
                    'IT-Related Disciplines' => $graduates->sum('it'),
                    'Law and Jurisprudence' => $graduates->sum('law'),
                    'Maritime' => $graduates->sum('maritime'),
                    'Mass Communication and Documentation' => $graduates->sum('mass_comm'),
                    'Mathematics' => $graduates->sum('mathematics'),
                    'Medical and Allied' => $graduates->sum('medical'),
                    'Natural Science' => $graduates->sum('natural_science'),
                    'Religion and Theology' => $graduates->sum('religion'),
                    'Service Trades' => $graduates->sum('service_trades'),
                    'Social and Behavioral Sciences' => $graduates->sum('social_sciences'),
                ]
            ];

            arsort($stats['top_disciplines']);

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available academic years
     */
    public function getYears()
    {
        $years = DisciplineGraduate::distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return response()->json($years);
    }

    /**
     * Get all available provinces
     */
    public function getProvinces()
    {
        $provinces = DisciplineGraduate::distinct()
            ->orderBy('province', 'asc')
            ->pluck('province');

        return response()->json($provinces);
    }

    /**
     * Get top disciplines by graduates
     */
    public function topDisciplines(Request $request)
    {
        $academicYear = $request->input('academic_year');
        $province = $request->input('province');
        $institutionType = $request->input('institution_type');
        $limit = $request->input('limit', 10);
        
        $query = DisciplineGraduate::query();
        
        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }
        if ($province) {
            $query->where('province', $province);
        }
        if ($institutionType) {
            $query->where('institution_type', $institutionType);
        }
        
        $graduates = $query->get();
        
        if ($graduates->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found'
            ], 404);
        }

        $disciplines = [
            'Agriculture, Forestry, Fisheries' => $graduates->sum('agriculture'),
            'Architecture and Town Planning' => $graduates->sum('architecture'),
            'Business Administration and Related' => $graduates->sum('business'),
            'Criminal Justice Education' => $graduates->sum('criminal_justice'),
            'Education Science and Teacher Training' => $graduates->sum('education'),
            'Engineering and Technology' => $graduates->sum('engineering'),
            'Fine and Applied Arts' => $graduates->sum('arts'),
            'Humanities' => $graduates->sum('humanities'),
            'IT-Related Disciplines' => $graduates->sum('it'),
            'Law and Jurisprudence' => $graduates->sum('law'),
            'Maritime' => $graduates->sum('maritime'),
            'Mass Communication and Documentation' => $graduates->sum('mass_comm'),
            'Mathematics' => $graduates->sum('mathematics'),
            'Medical and Allied' => $graduates->sum('medical'),
            'Natural Science' => $graduates->sum('natural_science'),
            'Religion and Theology' => $graduates->sum('religion'),
            'Service Trades' => $graduates->sum('service_trades'),
            'Social and Behavioral Sciences' => $graduates->sum('social_sciences'),
        ];

        arsort($disciplines);
        $topDisciplines = array_slice($disciplines, 0, $limit, true);
            
        return response()->json($topDisciplines);
    }

    /**
     * Get trend data (year-over-year comparison)
     */
    public function trends(Request $request)
    {
        $province = $request->input('province');
        $institutionType = $request->input('institution_type');

        $query = DisciplineGraduate::query();

        if ($province) {
            $query->where('province', $province);
        }
        if ($institutionType) {
            $query->where('institution_type', $institutionType);
        }

        $graduates = $query->orderBy('academic_year', 'asc')
            ->orderBy('province', 'asc')
            ->orderBy('institution_type', 'asc')
            ->get();

        $trends = ['data' => []];
        $groupedByYear = $graduates->groupBy('academic_year');

        foreach ($groupedByYear as $year => $yearGraduates) {
            $trends['data'][] = [
                'academic_year' => $year,
                'total_graduates' => $yearGraduates->sum('grand_total'),
                'by_province' => $yearGraduates->groupBy('province')
                    ->map(fn($group) => $group->sum('grand_total'))
                    ->toArray(),
                'by_institution_type' => $yearGraduates->groupBy('institution_type')
                    ->map(fn($group) => $group->sum('grand_total'))
                    ->toArray(),
            ];
        }

        return response()->json($trends);
    }
}