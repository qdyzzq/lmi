<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Module1\ChartController;
use App\Http\Controllers\Module1\DashboardController;
use App\Http\Controllers\Module2\JobMarketDemandsController;
use App\Http\Controllers\Module1\KpiController;
use App\Http\Controllers\Module1\PendingLaborMarketController;
use App\Http\Controllers\Module2\AutocompleteController;
use App\Http\Controllers\Module2\JobTitleController;
use App\Http\Controllers\Module1\AnalysisTemplateController;
use App\Http\Controllers\Module3\LicensureRateController;
use App\Http\Controllers\Module3\DisciplineEnrollmentController;
use App\Http\Controllers\Module3\DisciplineGraduateController;
use App\Http\Controllers\Module3\ProvincialProgressionController;
use App\Http\Controllers\Module3\GraduationRateController;
use App\Http\Controllers\Module3\SupplySideAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These endpoints provide JSON data for charts, statistics, and data retrieval
| Base URL: /api/... (Laravel adds this automatically)
|
| Read endpoints stay public so the public dashboard works without a login.
| Anything that writes or deletes data carries $writeAccess below, and
| unpublished drafts carry $staffRead. All /api routes are rate limited
| ('api' limiter, see AppServiceProvider).
| The 'web' group is required because API routes are stateless by default -
| without it there is no session for 'auth' and 'otp.verified' to read, and
| CSRF would not apply. The admin/statistician front-end already sends the
| X-CSRF-TOKEN header on these calls.
*/

$writeAccess = ['web', 'auth', 'otp.verified', 'no.duplicate', 'role:admin,statistician', 'throttle:staff-write', 'audit'];

// Unpublished drafts (pending / approved-but-not-published) are only for staff.
$staffRead = ['web', 'auth', 'otp.verified', 'role:admin,statistician'];

// ==================== CHART ROUTES ====================
Route::get('/quarterly/{year}', [ChartController::class, 'getQuarterlyData']);
Route::get('/quarterly-range', [ChartController::class, 'getQuarterlyRange']);
Route::get('/available-years', [ChartController::class, 'getAvailableYears']);
Route::get('/latest-quarter', [ChartController::class, 'getLatestQuarter']);
Route::get('/job-market/chart-data', [JobMarketDemandsController::class, 'chartData']);
Route::get('/job-market/hard-to-fill-data', [JobMarketDemandsController::class, 'hardToFillData']);
// ↓ INSERTED: Critical Skills Requirements year/month filter endpoint
Route::get('/job-market/matrix-data', [JobMarketDemandsController::class, 'matrixData']);
Route::get('/job-market/matrix-date-options', [JobMarketDemandsController::class, 'matrixDateOptions']);
// ↑ END INSERTED

// ==================== KPI ROUTES ====================
Route::get('/kpi-cards', [KpiController::class, 'getKpiCards']);
Route::get('/kpi-cards/periods', [KpiController::class, 'getAvailablePeriods']);

// ==================== LABOR MARKET ROUTES ====================
Route::get('/autocomplete-data', [AutocompleteController::class, 'getAutocompleteData']);
Route::get('/autocomplete/search', [AutocompleteController::class, 'search']);
Route::get('/job-titles/{year}', [JobTitleController::class, 'getByYear']);
Route::get('/job-market/sector-skills-data', [JobMarketDemandsController::class, 'sectorSkillsData']);

// ==================== ANALYSIS TEMPLATES ROUTES ====================
Route::prefix('analysis-templates')->group(function () use ($writeAccess, $staffRead) {
    Route::get('/', [AnalysisTemplateController::class, 'index']);
    Route::get('/preview-data', [AnalysisTemplateController::class, 'previewData']);

    // ── NEW: Pending workflow ──
    Route::get('/pending-all', [AnalysisTemplateController::class, 'allPending'])->middleware($staffRead);
    Route::get('/pending-count', [AnalysisTemplateController::class, 'pendingCount'])->middleware($staffRead);
    Route::get('/pending-show', [AnalysisTemplateController::class, 'pendingShow'])->middleware($staffRead);
    Route::post('/submit', [AnalysisTemplateController::class, 'adminSubmit'])->middleware($writeAccess);

    // ── NEW: Approved workflow ──
    Route::get('/approved-all', [AnalysisTemplateController::class, 'allApproved'])->middleware($staffRead);
    Route::get('/approved-count', [AnalysisTemplateController::class, 'approvedCount'])->middleware($staffRead);

    // Wildcard routes 
    Route::put('/{key}', [AnalysisTemplateController::class, 'update'])->middleware($writeAccess);
    Route::post('/{key}/reset', [AnalysisTemplateController::class, 'reset'])->middleware($writeAccess);
});

// ==================== LICENSURE RATES API ROUTES ====================
Route::prefix('licensure-rates')->group(function () {
    Route::get('/', [LicensureRateController::class, 'index']);
    Route::get('/year/{year}', [LicensureRateController::class, 'getByYear']);
    Route::get('/statistics', [LicensureRateController::class, 'statistics']);
    Route::get('/years', [LicensureRateController::class, 'getYears']);
    Route::get('/sectors', [LicensureRateController::class, 'getSectors']);
});

// ==================== DISCIPLINE ENROLLMENT API ROUTES ====================
Route::prefix('discipline-enrollment')->name('discipline-enrollment.')->group(function () {
    Route::get('/', [DisciplineEnrollmentController::class, 'index'])->name('index');
    Route::get('/meta/years', [DisciplineEnrollmentController::class, 'getYears'])->name('years');
    Route::get('/provinces', [DisciplineEnrollmentController::class, 'getProvinces'])->name('provinces');
    Route::get('/stats/summary', [DisciplineEnrollmentController::class, 'statistics'])->name('statistics');
    Route::get('/stats/top-disciplines', [DisciplineEnrollmentController::class, 'topDisciplines'])->name('top-disciplines');
    Route::get('/stats/trends', [DisciplineEnrollmentController::class, 'trends'])->name('trends');
    Route::get('/stats/compare', [DisciplineEnrollmentController::class, 'compare'])->name('compare');
    Route::get('/trend', [DisciplineEnrollmentController::class, 'getEnrollmentTrend'])->name('trend');
    Route::get('/year/{academicYear}', [DisciplineEnrollmentController::class, 'getByYear'])->name('by-year');
    // MUST be before /{id} wildcard or it gets swallowed
    Route::get('/check/{year}', [DisciplineEnrollmentController::class, 'checkYear'])->name('check');
    Route::get('/{id}', [DisciplineEnrollmentController::class, 'show'])->name('show');
});

// ==================== DISCIPLINE GRADUATE API ROUTES ====================
Route::prefix('discipline-graduate')->name('discipline-graduate.')->group(function () {
    Route::get('/stats/summary', [DisciplineGraduateController::class, 'statistics'])->name('statistics');
    Route::get('/meta/years', [DisciplineGraduateController::class, 'getYears'])->name('years');
    Route::get('/stats/top-disciplines', [DisciplineGraduateController::class, 'topDisciplines'])->name('top-disciplines');
    Route::get('/stats/trends', [DisciplineGraduateController::class, 'trends'])->name('trends');
    Route::get('/check/{year}', [DisciplineGraduateController::class, 'checkYear'])->name('check');
});

// ==================== PROVINCIAL PROGRESSION API ROUTES ====================
Route::prefix('provincial-progression')->name('provincial-progression.')->group(function () {
    Route::get('/', [ProvincialProgressionController::class, 'getProgressionData'])->name('data');
    Route::get('/provinces', [ProvincialProgressionController::class, 'getProvinces'])->name('provinces');
    Route::get('/meta/years', [ProvincialProgressionController::class, 'getYears'])->name('years');
});

// ==================== GRADUATION RATE API ROUTES ====================
Route::prefix('graduation-rate')->name('graduation-rate.')->group(function () use ($writeAccess, $staffRead) {

    Route::get('/enrollment-data/{academicYear}', [GraduationRateController::class, 'getEnrollmentData'])
        ->name('enrollment-data');

    // Read-only calculation, but a POST: staff only so it gets session + CSRF like every other POST.
    Route::post('/calculate', [GraduationRateController::class, 'calculateProjectedGraduates'])
        ->middleware($staffRead)
        ->name('calculate');

    Route::post('/save', [GraduationRateController::class, 'saveGraduationRate'])
        ->middleware($writeAccess)
        ->name('save');

    Route::get('/', [GraduationRateController::class, 'getAllGraduationRates'])
        ->name('all');

    
    Route::get('/check/{graduateYear}', [GraduationRateController::class, 'checkYear'])
        ->name('check');

    Route::get('/{graduateYear}', [GraduationRateController::class, 'getGraduationRate'])
        ->name('get');

    Route::delete('/{graduateYear}', [GraduationRateController::class, 'deleteGraduationRate'])
        ->middleware($writeAccess)
        ->name('delete');
});

// ==================== SUPPLY SIDE ANALYSIS ROUTES ====================
Route::prefix('supply-side-analysis')->group(function () use ($writeAccess, $staffRead) {

    // ── Existing routes (unchanged) ──
    Route::get('/options', [SupplySideAnalysisController::class, 'index']);
    Route::get('/show', [SupplySideAnalysisController::class, 'show']);
    Route::post('/save', [SupplySideAnalysisController::class, 'save'])->middleware($writeAccess);
    // Returns the default analysis text for the editors (read-only).
    Route::get('/reset', [SupplySideAnalysisController::class, 'reset'])->middleware($staffRead);
    Route::delete('/delete', [SupplySideAnalysisController::class, 'delete'])->middleware($writeAccess);
    Route::get('/archives', [SupplySideAnalysisController::class, 'getArchivedSis']);
    Route::get('/years', [SupplySideAnalysisController::class, 'getYears']);

    // ── NEW: Admin submits a draft (saved as 'pending') ──
    Route::post('/submit', [SupplySideAnalysisController::class, 'adminSubmit'])->middleware($writeAccess);

    // ── NEW: Load the pending draft (used by both admin & statistician editors) ──
    
    Route::get('/pending-show', [SupplySideAnalysisController::class, 'showPending'])->middleware($staffRead);
    Route::get('/pending-all', [SupplySideAnalysisController::class, 'allPending'])->middleware($staffRead);
    // ── NEW: Badge count for statistician sidebar polling ──
    Route::get('/pending-count', [SupplySideAnalysisController::class, 'pendingCount'])->middleware($staffRead);

    // ── NEW: Approved workflow ──
    Route::get('/approved-all', [SupplySideAnalysisController::class, 'allApproved'])->middleware($staffRead);
    Route::get('/approved-count', [SupplySideAnalysisController::class, 'approvedCount'])->middleware($staffRead);
});
