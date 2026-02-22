<?php

namespace App\Http\Controllers;

use App\Models\SupplySideAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplySideAnalysisController extends Controller
{
    public function editor()
    {
        return view('statistician.supplySide_editor');
    }

    public function show(Request $request)
    {
        try {
            $province = $request->query('province', 'All Provinces');
            $academicYear = $request->query('academic_year');

            if (!$academicYear) {
                return response()->json([
                    'success' => false,
                    'error' => 'Academic year is required'
                ], 400);
            }

            $analysis = SupplySideAnalysis::where('province', $province)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->first();

            if ($analysis) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $analysis->id,
                        'province' => $analysis->province,
                        'academic_year' => $analysis->academic_year,
                        'analysis_text' => $analysis->analysis_text,
                        'is_active' => $analysis->is_active,
                        'updated_at' => $analysis->updated_at,
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => null,
                    'province' => $province,
                    'academic_year' => $academicYear,
                    'analysis_text' => SupplySideAnalysis::getDefaultText(),
                    'is_active' => false,
                    'updated_at' => null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@show', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔧 FIXED: Get ALL archived analyses for province + year
     */
    public function getArchivedAnalyses(Request $request)
    {
        try {
            $province = $request->query('province', 'All Provinces');
            $academicYear = $request->query('academic_year');
            
            $query = SupplySideAnalysis::where('province', $province);
            
            if ($academicYear) {
                $query->where('academic_year', $academicYear);
            }
            
            $archives = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function($analysis, $index) {
                    return [
                        'id' => $analysis->id,
                        'version' => 'Version ' . ($index + 1),
                        'academic_year' => $analysis->academic_year,
                        'analysis_text' => $analysis->analysis_text,
                        'is_active' => $analysis->is_active,
                        'created_at' => $analysis->created_at->format('M d, Y'),
                        'updated_at' => $analysis->updated_at->format('M d, Y h:i A'),
                    ];
                });

            return response()->json([
                'success' => true,
                'archives' => $archives
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@getArchivedAnalyses', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $filable = ['discipline_enrollments'];

            $tableName = null;
            foreach ($filable as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $count = DB::table($table)->count();
                    if ($count > 0) {
                        $tableName = $table;
                        break;
                    }
                }
            }

            if (!$tableName) {
                return response()->json([
                    'success' => true,
                    'provinces' => ['All Provinces'],
                    'academic_years' => ['2022-2023', '2021-2022'],
                ]);
            }

            $provinces = DB::table($tableName)
                ->select('province')
                ->whereNotNull('province')
                ->where('province', '!=', '')
                ->distinct()
                ->orderBy('province')
                ->pluck('province')
                ->unique()
                ->values()
                ->toArray();

            array_unshift($provinces, 'All Provinces');

            $academicYears = DB::table($tableName)
                ->select('academic_year')
                ->whereNotNull('academic_year')
                ->where('academic_year', '!=', '')
                ->distinct()
                ->orderByDesc('academic_year')
                ->pluck('academic_year')
                ->unique()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'provinces' => $provinces,
                'academic_years' => $academicYears,
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@index error', [
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => true,
                'provinces' => ['All Provinces'],
                'academic_years' => ['2022-2023'],
            ]);
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province' => 'required|string',
            'academic_year' => 'required|string',
            'analysis_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            SupplySideAnalysis::where('province', $request->province)
                ->where('academic_year', $request->academic_year)
                ->update(['is_active' => false]);

            $analysis = SupplySideAnalysis::create([
                'province' => $request->province,
                'academic_year' => $request->academic_year,
                'analysis_text' => $request->analysis_text,
                'updated_by' => auth()->id() ?? null,
                'is_active' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Analysis saved successfully',
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('SupplySideAnalysisController@save', [
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reset()
    {
        return response()->json([
            'success' => true,
            'default_text' => SupplySideAnalysis::getDefaultText(),
        ]);
    }

    public function delete(Request $request)
    {
        try {
            $province = $request->query('province');
            $academicYear = $request->query('academic_year');

            $updated = SupplySideAnalysis::where('province', $province)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Analysis deactivated successfully',
                'deleted' => $updated > 0
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@delete', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}