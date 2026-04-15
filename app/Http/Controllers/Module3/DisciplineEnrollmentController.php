<?php

namespace App\Http\Controllers\Module3;

use App\Models\Module3\DisciplineEnrollment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisciplineEnrollmentController extends Controller
{
    /**
     * Show the discipline enrollment form
     */
    public function showForm()
    {
        return view('admin.Module3.enrollmentForm');
    }

    /**
     * Store new discipline enrollment data
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'province' => 'required|string',
            'institution_type' => 'required|in:Private,Public',
            'disciplines' => 'required|array',
            'disciplines.agriculture' => 'nullable|integer|min:0',
            'disciplines.architecture' => 'nullable|integer|min:0',
            'disciplines.business' => 'nullable|integer|min:0',
            'disciplines.criminal_justice' => 'nullable|integer|min:0',
            'disciplines.education' => 'nullable|integer|min:0',
            'disciplines.engineering' => 'nullable|integer|min:0',
            'disciplines.arts' => 'nullable|integer|min:0',
            'disciplines.general' => 'nullable|integer|min:0',
            'disciplines.home_economics' => 'nullable|integer|min:0',
            'disciplines.humanities' => 'nullable|integer|min:0',
            'disciplines.it' => 'nullable|integer|min:0',
            'disciplines.law' => 'nullable|integer|min:0',
            'disciplines.maritime' => 'nullable|integer|min:0',
            'disciplines.mass_comm' => 'nullable|integer|min:0',
            'disciplines.mathematics' => 'nullable|integer|min:0',
            'disciplines.medical' => 'nullable|integer|min:0',
            'disciplines.natural_science' => 'nullable|integer|min:0',
            'disciplines.other_disciplines' => 'nullable|integer|min:0',
            'disciplines.religion' => 'nullable|integer|min:0',
            'disciplines.service_trades' => 'nullable|integer|min:0',
            'disciplines.social_sciences' => 'nullable|integer|min:0',
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

            $academicYear = $request->academic_year;
            $province = $request->province;
            $institutionType = $request->institution_type;
            $submittedBy = Auth::id();

            // Calculate grand total
            $grandTotal = array_sum(array_map(fn($val) => (int)($val ?? 0), $request->disciplines));

            // Use updateOrCreate to handle both insert and update
            $enrollment = DisciplineEnrollment::updateOrCreate(
                [
                    'academic_year' => $academicYear,
                    'province' => $province,
                    'institution_type' => $institutionType
                ],
                [
                    'agriculture' => $request->disciplines['agriculture'] ?? 0,
                    'architecture' => $request->disciplines['architecture'] ?? 0,
                    'business' => $request->disciplines['business'] ?? 0,
                    'criminal_justice' => $request->disciplines['criminal_justice'] ?? 0,
                    'education' => $request->disciplines['education'] ?? 0,
                    'engineering' => $request->disciplines['engineering'] ?? 0,
                    'arts' => $request->disciplines['arts'] ?? 0,
                    'general' => $request->disciplines['general'] ?? 0,
                    'home_economics' => $request->disciplines['home_economics'] ?? 0,
                    'humanities' => $request->disciplines['humanities'] ?? 0,
                    'it' => $request->disciplines['it'] ?? 0,
                    'law' => $request->disciplines['law'] ?? 0,
                    'maritime' => $request->disciplines['maritime'] ?? 0,
                    'mass_comm' => $request->disciplines['mass_comm'] ?? 0,
                    'mathematics' => $request->disciplines['mathematics'] ?? 0,
                    'medical' => $request->disciplines['medical'] ?? 0,
                    'natural_science' => $request->disciplines['natural_science'] ?? 0,
                    'other_disciplines' => $request->disciplines['other_disciplines'] ?? 0,
                    'religion' => $request->disciplines['religion'] ?? 0,
                    'service_trades' => $request->disciplines['service_trades'] ?? 0,
                    'social_sciences' => $request->disciplines['social_sciences'] ?? 0,
                    'grand_total' => $grandTotal,
                    'submitted_by' => $submittedBy,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Discipline enrollment data submitted successfully.',
                'data' => $enrollment
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
     * Helper: check if province means "all" (supports both old and new label)
     */
    private function isAllProvinces(?string $province): bool
    {
        return !$province || $province === 'All Provinces' || $province === 'Davao Region';
    }

    /**
     * Check if data exists for a specific year + province + institution type and return it
     */
    public function checkYear($academicYear, Request $request)
    {
        try {
            $province = $request->query('province');
            $institutionType = $request->query('institution_type');

            // Validate query parameters
            if (!$province || !$institutionType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Province and institution_type are required'
                ], 400);
            }

            // Fetch the record for the given year, province, and institution type
            $data = DisciplineEnrollment::where('academic_year', $academicYear)
                ->where('province', $province)
                ->where('institution_type', $institutionType)
                ->first();

            if (!$data) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'data' => null
                ], 200);
            }
            
            return response()->json([
                'success' => true,
                'exists' => true,
                'data' => [
                    'id' => $data->id,
                    'academic_year' => $data->academic_year,
                    'province' => $data->province,
                    'institution_type' => $data->institution_type,
                    'disciplines' => [
                        'agriculture' => $data->agriculture,
                        'architecture' => $data->architecture,
                        'business' => $data->business,
                        'criminal_justice' => $data->criminal_justice,
                        'education' => $data->education,
                        'engineering' => $data->engineering,
                        'arts' => $data->arts,
                        'general' => $data->general ?? 0,
                        'home_economics' => $data->home_economics ?? 0,
                        'humanities' => $data->humanities,
                        'it' => $data->it,
                        'law' => $data->law,
                        'maritime' => $data->maritime,
                        'mass_comm' => $data->mass_comm,
                        'mathematics' => $data->mathematics,
                        'medical' => $data->medical,
                        'natural_science' => $data->natural_science,
                        'other_disciplines' => $data->other_disciplines ?? 0,
                        'religion' => $data->religion,
                        'service_trades' => $data->service_trades,
                        'social_sciences' => $data->social_sciences,
                    ],
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                    'is_aggregated' => false,
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
     * Get all discipline enrollment records (with optional filters)
     */
    public function index(Request $request)
    {
        $query = DisciplineEnrollment::query();

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->has('province')) {
            $query->where('province', $request->province);
        }
        if ($request->has('institution_type')) {
            $query->where('institution_type', $request->institution_type);
        }

        $enrollments = $query->with('submitter')
            ->orderBy('academic_year', 'desc')
            ->orderBy('province', 'asc')
            ->orderBy('institution_type', 'asc')
            ->get();

        return response()->json($enrollments);
    }

    /**
     * Get data for a specific academic year
     */
    public function getByYear($academicYear)
    {
        $data = DisciplineEnrollment::where('academic_year', $academicYear)->get();

        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for academic year ' . $academicYear
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * Show details of a specific enrollment record
     */
    public function show($id)
    {
        $enrollment = DisciplineEnrollment::with(['submitter'])->findOrFail($id);
        return response()->json($enrollment);
    }

    /**
     * Delete a specific combination
     */
    public function deleteYear($academicYear, Request $request)
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

            $deleted = DisciplineEnrollment::where('academic_year', $academicYear)
                ->where('province', $province)
                ->where('institution_type', $institutionType)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Data for {$academicYear} - {$province} - {$institutionType} deleted successfully.",
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
     * Get statistics/summary with province and institution type breakdown
     */
    public function statistics(Request $request)
    {
        $academicYear = $request->input('academic_year');
        $province = $request->input('province');
        $institutionType = $request->input('institution_type');
        
        $query = DisciplineEnrollment::query();
        
        if ($academicYear) $query->where('academic_year', $academicYear);
        if ($province) $query->where('province', $province);
        if ($institutionType) $query->where('institution_type', $institutionType);

        $enrollments = $query->get();
        
        $stats = [
            'total_records' => $enrollments->count(),
            'total_enrollment' => $enrollments->sum('grand_total'),
            'average_per_record' => $enrollments->avg('grand_total'),
            'by_province' => $enrollments->groupBy('province')->map(fn($g) => $g->sum('grand_total'))->toArray(),
            'by_institution_type' => $enrollments->groupBy('institution_type')->map(fn($g) => $g->sum('grand_total'))->toArray(),
            'by_discipline' => [
                'Agriculture, Forestry, Fisheries' => $enrollments->sum('agriculture'),
                'Architecture and Town Planning' => $enrollments->sum('architecture'),
                'Business Administration and Related' => $enrollments->sum('business'),
                'Criminal Justice Education' => $enrollments->sum('criminal_justice'),
                'Education Science and Teacher Training' => $enrollments->sum('education'),
                'Engineering and Technology' => $enrollments->sum('engineering'),
                'Fine and Applied Arts' => $enrollments->sum('arts'),
                'Humanities' => $enrollments->sum('humanities'),
                'IT-Related Disciplines' => $enrollments->sum('it'),
                'Law and Jurisprudence' => $enrollments->sum('law'),
                'Maritime' => $enrollments->sum('maritime'),
                'Mass Communication and Documentation' => $enrollments->sum('mass_comm'),
                'Mathematics' => $enrollments->sum('mathematics'),
                'Medical and Allied' => $enrollments->sum('medical'),
                'Natural Science' => $enrollments->sum('natural_science'),
                'Religion and Theology' => $enrollments->sum('religion'),
                'Service Trades' => $enrollments->sum('service_trades'),
                'Social and Behavioral Sciences' => $enrollments->sum('social_sciences'),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get all available academic years, optionally filtered by province.
     */
    public function getYears(Request $request)
    {
        $province = $request->query('province');

        $query = DisciplineEnrollment::distinct()
            ->orderBy('academic_year', 'desc');

        if ($province) {
            if ($this->isAllProvinces($province)) {
                // For Davao Region: return all years for Davao Region rows
                $query->where('province', 'Davao Region');
            } else {
                // Specific province: filter by that province
                $query->where('province', $province);
            }
        }
        // No province param = return all years (used by KPI cards, etc.)

        $years = $query->pluck('academic_year');

        return response()->json($years);
    }

    /**
     * Get all available provinces
     */
    public function getProvinces()
    {
        $provinces = DisciplineEnrollment::distinct()
            ->orderBy('province', 'asc')
            ->pluck('province');

        return response()->json($provinces);
    }

    /**
     * Get top disciplines by enrollment
     */
    public function topDisciplines(Request $request)
    {
        $academicYear = $request->input('academic_year');
        $province = $request->input('province');
        $institutionType = $request->input('institution_type');
        $limit = $request->input('limit', 10);
        
        $query = DisciplineEnrollment::query();
        
        if ($academicYear) $query->where('academic_year', $academicYear);
        if ($province) $query->where('province', $province);
        if ($institutionType) $query->where('institution_type', $institutionType);
        
        $enrollments = $query->get();
        
        if ($enrollments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No data found'], 404);
        }

        $disciplines = [
            'Agriculture, Forestry, Fisheries' => $enrollments->sum('agriculture'),
            'Architecture and Town Planning' => $enrollments->sum('architecture'),
            'Business Administration and Related' => $enrollments->sum('business'),
            'Criminal Justice Education' => $enrollments->sum('criminal_justice'),
            'Education Science and Teacher Training' => $enrollments->sum('education'),
            'Engineering and Technology' => $enrollments->sum('engineering'),
            'Fine and Applied Arts' => $enrollments->sum('arts'),
            'Humanities' => $enrollments->sum('humanities'),
            'IT-Related Disciplines' => $enrollments->sum('it'),
            'Law and Jurisprudence' => $enrollments->sum('law'),
            'Maritime' => $enrollments->sum('maritime'),
            'Mass Communication and Documentation' => $enrollments->sum('mass_comm'),
            'Mathematics' => $enrollments->sum('mathematics'),
            'Medical and Allied' => $enrollments->sum('medical'),
            'Natural Science' => $enrollments->sum('natural_science'),
            'Religion and Theology' => $enrollments->sum('religion'),
            'Service Trades' => $enrollments->sum('service_trades'),
            'Social and Behavioral Sciences' => $enrollments->sum('social_sciences'),
        ];

        arsort($disciplines);
        $topDisciplines = array_slice($disciplines, 0, $limit, true);
            
        return response()->json($topDisciplines);
    }

    /**
     * Get trend data (year-over-year comparison) with optional province/type filters
     */
    public function trends(Request $request)
    {
        $province = $request->input('province');
        $institutionType = $request->input('institution_type');

        $query = DisciplineEnrollment::query();

        if ($province) $query->where('province', $province);
        if ($institutionType) $query->where('institution_type', $institutionType);

        $enrollments = $query->orderBy('academic_year', 'asc')
            ->orderBy('province', 'asc')
            ->orderBy('institution_type', 'asc')
            ->get();

        $trends = ['data' => []];
        $groupedByYear = $enrollments->groupBy('academic_year');

        foreach ($groupedByYear as $year => $yearEnrollments) {
            $trends['data'][] = [
                'academic_year' => $year,
                'total_enrollment' => $yearEnrollments->sum('grand_total'),
                'by_province' => $yearEnrollments->groupBy('province')->map(fn($g) => $g->sum('grand_total'))->toArray(),
                'by_institution_type' => $yearEnrollments->groupBy('institution_type')->map(fn($g) => $g->sum('grand_total'))->toArray(),
            ];
        }

        return response()->json($trends);
    }

    /**
     * Get enrollment trend data for the "Enrollment Trend" chart
     * Supports both "Davao Region" and "All Provinces" as the all-data sentinel value.
     */
    public function getEnrollmentTrend(Request $request)
    {
        try {
            $year = $request->query('year');
            $province = $request->query('province', 'Davao Region');
            
            if (!$year) {
                return response()->json([
                    'error' => 'Year parameter is required',
                    'message' => 'Please provide an academic year (e.g., ?year=2024-2025)'
                ], 400);
            }
            
            $query = DisciplineEnrollment::where('academic_year', $year)
                ->where('province', $province); // Always filter by exact province
            
            $enrollmentData = $query->get();
            
            if ($enrollmentData->isEmpty()) {
                return response()->json([
                    'year' => $year,
                    'province' => $province,
                    'disciplines' => [],
                    'publicSchools' => [],
                    'privateSchools' => [],
                    'totals' => ['public' => 0, 'private' => 0, 'combined' => 0],
                    'message' => 'No data found for the specified year and province'
                ]);
            }
            
            $aggregated = [];
            
            $disciplineMap = [
                'agriculture' => 'Agri & Forestry',
                'architecture' => 'Architecture',
                'business' => 'Business & Admin',
                'criminal_justice' => 'Criminal Justice',
                'education' => 'Education',
                'engineering' => 'Engineering & Tech',
                'arts' => 'Fine Arts',
                'general' => 'General',
                'home_economics' => 'Home Economics',
                'humanities' => 'Humanities',
                'it' => 'IT & Related',
                'law' => 'Law',
                'maritime' => 'Maritime',
                'mass_comm' => 'Mass Comm',
                'mathematics' => 'Mathematics',
                'medical' => 'Medical & Allied',
                'natural_science' => 'Natural Science',
                'other_disciplines' => 'Other Disciplines',
                'religion' => 'Religion',
                'service_trades' => 'Service Trades',
                'social_sciences' => 'Social Sciences'
            ];
            
            foreach ($enrollmentData as $record) {
                $institutionType = $record->institution_type;
                
                foreach ($disciplineMap as $key => $displayName) {
                    $count = (int)($record->$key ?? 0);
                    
                    if (!isset($aggregated[$displayName])) {
                        $aggregated[$displayName] = ['public' => 0, 'private' => 0, 'total' => 0];
                    }
                    
                    if ($institutionType === 'Public') {
                        $aggregated[$displayName]['public'] += $count;
                    } elseif ($institutionType === 'Private') {
                        $aggregated[$displayName]['private'] += $count;
                    }
                }
            }
            
            uasort($aggregated, function ($a, $b) {
                return ($b['public'] + $b['private']) - ($a['public'] + $a['private']);
            });
            
            $disciplines = [];
            $publicSchools = [];
            $privateSchools = [];
            $totalPublic = 0;
            $totalPrivate = 0;
            
            foreach ($aggregated as $discipline => $data) {
                if ($data['public'] > 0 || $data['private'] > 0) {
                    $disciplines[] = $discipline;
                    $publicSchools[] = $data['public'];
                    $privateSchools[] = $data['private'];
                    $totalPublic += $data['public'];
                    $totalPrivate += $data['private'];
                }
            }
            
            return response()->json([
                'year' => $year,
                'province' => $province,
                'disciplines' => $disciplines,
                'publicSchools' => $publicSchools,
                'privateSchools' => $privateSchools,
                'totals' => [
                    'public' => $totalPublic,
                    'private' => $totalPrivate,
                    'combined' => $totalPublic + $totalPrivate
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve enrollment trend data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compare two records
     */
    public function compare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'record1_year' => 'required|string',
            'record1_province' => 'required|string',
            'record1_type' => 'required|in:Private,Public',
            'record2_year' => 'required|string',
            'record2_province' => 'required|string',
            'record2_type' => 'required|in:Private,Public',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $enrollment1 = DisciplineEnrollment::where('academic_year', $request->record1_year)
            ->where('province', $request->record1_province)
            ->where('institution_type', $request->record1_type)
            ->first();

        $enrollment2 = DisciplineEnrollment::where('academic_year', $request->record2_year)
            ->where('province', $request->record2_province)
            ->where('institution_type', $request->record2_type)
            ->first();

        if (!$enrollment1 || !$enrollment2) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found for one or both records'
            ], 404);
        }

        return response()->json([
            'record1' => [
                'academic_year' => $enrollment1->academic_year,
                'province' => $enrollment1->province,
                'institution_type' => $enrollment1->institution_type,
                'grand_total' => $enrollment1->grand_total,
            ],
            'record2' => [
                'academic_year' => $enrollment2->academic_year,
                'province' => $enrollment2->province,
                'institution_type' => $enrollment2->institution_type,
                'grand_total' => $enrollment2->grand_total,
            ],
            'difference' => [
                'grand_total' => $enrollment2->grand_total - $enrollment1->grand_total,
                'percentage_change' => $enrollment1->grand_total > 0 
                    ? round((($enrollment2->grand_total - $enrollment1->grand_total) / $enrollment1->grand_total) * 100, 2)
                    : 0,
            ]
        ]);
    }
}