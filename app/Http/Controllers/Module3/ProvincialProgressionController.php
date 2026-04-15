<?php

namespace App\Http\Controllers\Module3;


use App\Models\Module3\DisciplineGraduate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Provincial Progression Analysis Controller
 * 
 * Handles data for the Provincial Progression chart which compares:
 * - Historical enrollment (4 years ago) - PLACEHOLDER for now
 * - Current graduates - ACTUAL data from DisciplineGraduate model
 * 
 * Filters: Province + Academic Year
 */
class ProvincialProgressionController extends Controller
{
    /**
     * Discipline mapping: database column => display name
     */
    private const DISCIPLINE_MAP = [
        'agriculture' => 'Agriculture, Forestry, Fisheries',
        'architecture' => 'Architecture and Town Planning',
        'business' => 'Business Administration and Related',
        'criminal_justice' => 'Criminal Justice Education',
        'education' => 'Education Science and Teacher Training',
        'engineering' => 'Engineering and Technology',
        'arts' => 'Fine and Applied Arts',
        'humanities' => 'Humanities',
        'it' => 'IT-Related Disciplines',
        'law' => 'Law and Jurisprudence',
        'maritime' => 'Maritime',
        'mass_comm' => 'Mass Communication and Documentation',
        'mathematics' => 'Mathematics',
        'medical' => 'Medical and Allied',
        'natural_science' => 'Natural Science',
        'religion' => 'Religion and Theology',
        'service_trades' => 'Service Trades',
        'social_sciences' => 'Social and Behavioral Sciences',
    ];

    /**
     * Get provincial progression data
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Query Parameters:
     * - year: Academic year (e.g., "2022-2023")
     * - province: Province name (e.g., "Davao City")
     * 
     * Response Format:
     * {
     *   "disciplines": ["Business Administration", "Engineering", ...],
     *   "enrolled": [0, 0, 0, ...],  // Placeholder - all zeros for now
     *   "projected": [850, 420, ...], // Actual graduate counts
     *   "totals": {
     *     "enrolled": 0,              // Placeholder
     *     "projected": 2060           // Sum of all graduates
     *   }
     * }
     */
    public function getProgressionData(Request $request)
    {
        $year = $request->query('year');
        $province = $request->query('province');
        
        // Validate required parameters
        if (!$year || !$province) {
            return response()->json([
                'success' => false,
                'message' => 'Year and province are required parameters'
            ], 400);
        }

        try {
            // Query graduate data for the selected province and year
            // We aggregate both Public and Private institutions
            $query = DisciplineGraduate::where('academic_year', $year)
                ->where('province', $province);
            
            $graduates = $query->get();
            
            // If no data found, return empty structure
            if ($graduates->isEmpty()) {
                return $this->emptyResponse($year, $province);
            }

            // Initialize arrays for response
            $disciplineLabels = [];
            $graduateData = [];
            $enrollmentData = [];  // All zeros - placeholder for future enrollment data
            
            // Process each discipline
            foreach (self::DISCIPLINE_MAP as $dbColumn => $displayName) {
                // Sum across Public and Private institutions
                $graduateCount = $graduates->sum($dbColumn);
                
                // Only include disciplines with data
                if ($graduateCount > 0) {
                    $disciplineLabels[] = $displayName;
                    $graduateData[] = (int) $graduateCount;
                    $enrollmentData[] = 0;  // Placeholder
                }
            }
            
            // Calculate totals
            $totalGraduates = array_sum($graduateData);
            
            return response()->json([
                'success' => true,
                'filters' => [
                    'year' => $year,
                    'province' => $province
                ],
                'disciplines' => $disciplineLabels,
                'projected' => $graduateData,      // Frontend expects "projected"
                'enrolled' => $enrollmentData,      // Placeholder zeros
                'totals' => [
                    'projected' => $totalGraduates,
                    'enrolled' => 0                 // Placeholder
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching progression data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available provinces from the database
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProvinces()
    {
        try {
            $provinces = DisciplineGraduate::distinct()
                ->orderBy('province', 'asc')
                ->pluck('province')
                ->toArray();
            
            return response()->json($provinces);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching provinces: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available academic years
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getYears()
    {
        try {
            $years = DisciplineGraduate::distinct()
                ->orderByDesc('academic_year')
                ->pluck('academic_year')
                ->toArray();
            
            return response()->json($years);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching years: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return empty response structure when no data found
     * 
     * @param string $year
     * @param string $province
     * @return \Illuminate\Http\JsonResponse
     */
    private function emptyResponse($year, $province)
    {
        return response()->json([
            'success' => true,
            'message' => "No graduate data found for {$province} in {$year}",
            'filters' => [
                'year' => $year,
                'province' => $province
            ],
            'disciplines' => [],
            'projected' => [],
            'enrolled' => [],
            'totals' => [
                'projected' => 0,
                'enrolled' => 0
            ]
        ]);
    }
}