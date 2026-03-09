<?php

namespace App\Http\Controllers;

use App\Models\SupplySideAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplySideAnalysisController extends Controller
{
    // ─── Views ────────────────────────────────────────────

    /**
     * Statistician editor view
     */
    public function editor()
    {
        return view('statistician.supplySide_editor');
    }

    /**
     * Admin editor view (drafts submitted for statistician to publish)
     */
    public function adminEditor()
    {
        return view('admin.supplySide_editor');
    }

    // ─── Helpers ──────────────────────────────────────────

    /**
     * Returns true when the province value represents the whole region.
     * Mirrors DisciplineEnrollmentController::isAllProvinces().
     */
    private function isAllProvinces(?string $province): bool
    {
        return !$province || $province === 'All Provinces' || $province === 'Davao Region';
    }

    // ─── Public / Shared ──────────────────────────────────

    /**
     * Get province list + default (Davao Region) academic years on initial page load.
     * The blade calls getYears() separately whenever the province dropdown changes.
     */
    public function index()
    {
        try {
            $tableName = $this->resolveTable();

            if (!$tableName) {
                return response()->json([
                    'success'        => true,
                    'provinces'      => ['Davao Region'],
                    'academic_years' => ['2022-2023', '2021-2022'],
                ]);
            }

            // Exclude 'Davao Region' from the per-province list — prepended below.
            $provinces = DB::table($tableName)
                ->select('province')
                ->whereNotNull('province')
                ->where('province', '!=', '')
                ->where('province', '!=', 'Davao Region')
                ->distinct()
                ->orderBy('province')
                ->pluck('province')
                ->unique()
                ->values()
                ->toArray();

            array_unshift($provinces, 'Davao Region');

            // Default years = Davao Region (the initial selected province)
            $academicYears = $this->fetchYearsForProvince($tableName, 'Davao Region');

            return response()->json([
                'success'        => true,
                'provinces'      => $provinces,
                'academic_years' => $academicYears,
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@index error', ['error' => $e->getMessage()]);

            return response()->json([
                'success'        => true,
                'provinces'      => ['Davao Region'],
                'academic_years' => ['2022-2023'],
            ]);
        }
    }

    /**
     * Return academic years available for a given province.
     * Called by the blade whenever the province dropdown changes.
     * Route: GET /api/supply-side-analysis/years?province=Davao+City
     */
    public function getYears(Request $request)
    {
        try {
            $province  = $request->query('province', 'Davao Region');
            $tableName = $this->resolveTable();

            if (!$tableName) {
                return response()->json(['success' => true, 'academic_years' => []]);
            }

            $years = $this->fetchYearsForProvince($tableName, $province);

            return response()->json(['success' => true, 'academic_years' => $years]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@getYears', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── Private helpers ──────────────────────────────────

    /**
     * Find the first populated source table.
     */
    private function resolveTable(): ?string
    {
        foreach (['discipline_enrollments'] as $table) {
            if (DB::getSchemaBuilder()->hasTable($table) && DB::table($table)->count() > 0) {
                return $table;
            }
        }
        return null;
    }

    /**
     * Return distinct academic years for the given province.
     *
     * - Davao Region → only rows where institution_type = 'Total'
     * - Any other province → all rows for that province (Private + Public)
     */
    private function fetchYearsForProvince(string $tableName, string $province): array
    {
        $query = DB::table($tableName)
            ->select('academic_year')
            ->whereNotNull('academic_year')
            ->where('academic_year', '!=', '');

        if ($this->isAllProvinces($province)) {
            $query->where('province', 'Davao Region')
                  ->where('institution_type', 'Total');
        } else {
            $query->where('province', $province);
        }

        $years = $query->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year')
            ->unique()
            ->values()
            ->toArray();

        // Fallback: return all years if nothing found for this province
        if (empty($years)) {
            $years = DB::table($tableName)
                ->select('academic_year')
                ->whereNotNull('academic_year')
                ->where('academic_year', '!=', '')
                ->distinct()
                ->orderByDesc('academic_year')
                ->pluck('academic_year')
                ->unique()
                ->values()
                ->toArray();
        }

        return $years;
    }

    /**
     * Show the currently PUBLISHED analysis for a province + year.
     * Used by the public SupplySide page — only returns published records.
     * Also used by the statistician editor to load what is currently live.
     */
    public function show(Request $request)
    {
        try {
            $province     = $request->query('province', 'Davao Region');
            $academicYear = $request->query('academic_year');

            if (!$academicYear) {
                return response()->json(['success' => false, 'error' => 'Academic year is required'], 400);
            }

            $analysis = SupplySideAnalysis::where('province', $province)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->where('status', 'published')
                ->first();

            if ($analysis) {
                return response()->json([
                    'success' => true,
                    'data'    => [
                        'id'            => $analysis->id,
                        'province'      => $analysis->province,
                        'academic_year' => $analysis->academic_year,
                        'analysis_text' => $analysis->analysis_text,
                        'is_active'     => $analysis->is_active,
                        'status'        => $analysis->status,
                        'updated_at'    => $analysis->updated_at,
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'            => null,
                    'province'      => $province,
                    'academic_year' => $academicYear,
                    'analysis_text' => SupplySideAnalysis::getDefaultText(),
                    'is_active'     => false,
                    'status'        => null,
                    'updated_at'    => null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@show', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the PENDING submission for a province + year.
     * Used by the admin editor to load their draft, and the statistician editor
     * to see what the admin submitted.
     */
    public function showPending(Request $request)
    {
        try {
            $province     = $request->query('province', 'Davao Region');
            $academicYear = $request->query('academic_year');

            if (!$academicYear) {
                return response()->json(['success' => false, 'error' => 'Academic year is required'], 400);
            }

            $pending = SupplySideAnalysis::where('province', $province)
                ->where('academic_year', $academicYear)
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->first();

            return response()->json([
                'success' => true,
                'data'    => $pending ? [
                    'id'            => $pending->id,
                    'province'      => $pending->province,
                    'academic_year' => $pending->academic_year,
                    'analysis_text' => $pending->analysis_text,
                    'status'        => $pending->status,
                    'submitted_by'  => $pending->submitted_by,
                    'submitted_at'  => $pending->submitted_at?->toDateTimeString(),
                    'updated_at'    => $pending->updated_at,
                ] : null,
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@showPending', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── Admin Actions ────────────────────────────────────

    /**
     * Admin submits a draft — saved as 'pending'.
     * If a pending submission already exists for this province+year, it is overwritten.
     */
    public function adminSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province'      => 'required|string',
            'academic_year' => 'required|string',
            'analysis_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Block submission if a published record already exists for this province + year
            $alreadyPublished = SupplySideAnalysis::where('province', $request->province)
                ->where('academic_year', $request->academic_year)
                ->where('status', 'published')
                ->exists();

            if ($alreadyPublished) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error'   => 'An analysis for this province and academic year has already been published. Submission is no longer allowed.',
                ], 403);
            }

            // Remove any existing pending draft for this province + year
            SupplySideAnalysis::where('province', $request->province)
                ->where('academic_year', $request->academic_year)
                ->where('status', 'pending')
                ->delete();

            $analysis = SupplySideAnalysis::create([
                'province'      => $request->province,
                'academic_year' => $request->academic_year,
                'analysis_text' => $request->analysis_text,
                'is_active'     => false,
                'status'        => 'pending',
                'submitted_by'  => auth()->user()?->name ?? auth()->id() ?? 'Admin',
                'submitted_at'  => now(),
                'updated_by'    => auth()->id() ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Analysis submitted for review successfully.',
                'data'    => $analysis,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SupplySideAnalysisController@adminSubmit', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── Statistician Actions ─────────────────────────────

    /**
     * Statistician saves directly as published (existing behaviour, unchanged).
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province'      => 'required|string',
            'academic_year' => 'required|string',
            'analysis_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Deactivate all previous published records for this province + year
            SupplySideAnalysis::where('province', $request->province)
                ->where('academic_year', $request->academic_year)
                ->where('status', 'published')
                ->update(['is_active' => false]);

            // Remove any pending draft that now becomes irrelevant
            SupplySideAnalysis::where('province', $request->province)
                ->where('academic_year', $request->academic_year)
                ->where('status', 'pending')
                ->delete();

            $analysis = SupplySideAnalysis::create([
                'province'      => $request->province,
                'academic_year' => $request->academic_year,
                'analysis_text' => $request->analysis_text,
                'is_active'     => true,
                'status'        => 'published',
                'updated_by'    => auth()->id() ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Analysis saved and published successfully.',
                'data'    => $analysis,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SupplySideAnalysisController@save', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * How many pending submissions are waiting for review.
     * Used for the badge count on the statistician sidebar.
     */
    public function pendingCount()
    {
        $count = SupplySideAnalysis::where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    // ─── Shared Utility ───────────────────────────────────

    /**
     * Returns published analyses, optionally filtered by province and/or academic year.
     *
     * NOTE: Method was previously misnamed "getArchivedAnalysis" in api.php — the route
     * must use "getArchivedSis" to match this method name.
     */
    public function getArchivedSis(Request $request)
    {
        try {
            $province     = $request->query('province');
            $academicYear = $request->query('academic_year');

            $query = SupplySideAnalysis::where('status', 'published');

            if ($province && !$this->isAllProvinces($province)) {
                // Specific province — filter exactly
                $query->where('province', $province);
            }
            // If province is null, 'All Provinces', or 'Davao Region' → return all records

            if ($academicYear) {
                $query->where('academic_year', $academicYear);
            }

            $archives = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($sis, $index) {
                    return [
                        'id'            => $sis->id,
                        'version'       => 'Version ' . ($index + 1),
                        'academic_year' => $sis->academic_year,
                        'province'      => $sis->province,
                        'analysis_text' => $sis->analysis_text,
                        'is_active'     => $sis->is_active,
                        'created_at'    => $sis->created_at->format('M d, Y'),
                        'updated_at'    => $sis->updated_at->format('M d, Y h:i A'),
                    ];
                });

            return response()->json([
                'success'  => true,
                'archives' => $archives,
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@getArchivedSis', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function reset()
    {
        return response()->json([
            'success'      => true,
            'default_text' => SupplySideAnalysis::getDefaultText(),
        ]);
    }

    public function delete(Request $request)
    {
        try {
            $province     = $request->query('province');
            $academicYear = $request->query('academic_year');

            $updated = SupplySideAnalysis::where('province', $province)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Analysis deactivated successfully',
                'deleted' => $updated > 0,
            ]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@delete', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function allPending()
    {
        $pending = SupplySideAnalysis::where('status', 'pending')
            ->orderByDesc('submitted_at')
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'province'      => $p->province,
                'academic_year' => $p->academic_year,
                'analysis_text' => $p->analysis_text,
                'submitted_by'  => $p->submitted_by,
                'submitted_at'  => $p->submitted_at?->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => $pending]);
    }
    // ─── Statistician: All approved (published) records ──

    public function allApproved()
    {
        try {
            $approved = SupplySideAnalysis::where('status', 'published')
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->get()
                ->map(fn($a) => [
                    'id'            => $a->id,
                    'province'      => $a->province,
                    'academic_year' => $a->academic_year,
                    'analysis_text' => $a->analysis_text,
                    'submitted_by'  => $a->submitted_by,
                    'approved_at'   => $a->updated_at?->toDateTimeString(),
                    'is_active'     => $a->is_active,
                ]);

            return response()->json(['success' => true, 'data' => $approved]);

        } catch (\Exception $e) {
            Log::error('SupplySideAnalysisController@allApproved', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── Statistician: Approved count for badge ───────────

    public function approvedCount()
    {
        $count = SupplySideAnalysis::where('status', 'published')->count();

        return response()->json(['success' => true, 'count' => $count]);
    }
}