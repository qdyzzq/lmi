<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobMarketDemandsController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\LaborMarketController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\AnalysisTemplateController;
use App\Http\Controllers\LicensureRateController;
use App\Http\Controllers\DisciplineEnrollmentController;
use App\Http\Controllers\DisciplineGraduateController;
use App\Http\Controllers\ProvincialProgressionController;
use App\Http\Controllers\GraduationRateController;
use App\Http\Controllers\SupplySideAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These endpoints provide JSON data for charts, statistics, and data retrieval
| Base URL: /api/... (Laravel adds this automatically)
*/

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
Route::post('/labor-market/store', [LaborMarketController::class, 'store']);
Route::get('/autocomplete-data', [AutocompleteController::class, 'getAutocompleteData']);
Route::get('/autocomplete/search', [AutocompleteController::class, 'search']);
Route::get('/job-titles/{year}', [JobTitleController::class, 'getByYear']);

// ==================== ANALYSIS TEMPLATES ROUTES ====================
Route::prefix('analysis-templates')->group(function () {
    Route::get('/', [AnalysisTemplateController::class, 'index']);
    Route::get('/placeholders', [AnalysisTemplateController::class, 'placeholders']);
    Route::get('/preview-data', [AnalysisTemplateController::class, 'previewData']);

    // ── NEW: Pending workflow ──
    Route::get('/pending-all', [AnalysisTemplateController::class, 'allPending']);
    Route::get('/pending-count', [AnalysisTemplateController::class, 'pendingCount']);
    Route::get('/pending-show', [AnalysisTemplateController::class, 'pendingShow']);
    Route::post('/submit', [AnalysisTemplateController::class, 'adminSubmit']);

    // ── NEW: Approved workflow ──
    Route::get('/approved-all', [AnalysisTemplateController::class, 'allApproved']);
    Route::get('/approved-count', [AnalysisTemplateController::class, 'approvedCount']);

    // ⚠️ Wildcard routes MUST stay last
    Route::get('/{key}', [AnalysisTemplateController::class, 'show']);
    Route::put('/{key}', [AnalysisTemplateController::class, 'update']);
    Route::post('/{key}/reset', [AnalysisTemplateController::class, 'reset']);
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
    Route::get('/', [DisciplineGraduateController::class, 'index'])->name('index');
    Route::get('/stats/summary', [DisciplineGraduateController::class, 'statistics'])->name('statistics');
    Route::get('/meta/years', [DisciplineGraduateController::class, 'getYears'])->name('years');
    Route::get('/stats/top-disciplines', [DisciplineGraduateController::class, 'topDisciplines'])->name('top-disciplines');
    Route::get('/stats/trends', [DisciplineGraduateController::class, 'trends'])->name('trends');
    Route::get('/stats/compare', [DisciplineGraduateController::class, 'compare'])->name('compare');
    Route::get('/year/{academicYear}', [DisciplineGraduateController::class, 'getByYear'])->name('check-year');
    // MUST be before /{id} wildcard or it gets swallowed
    Route::get('/check/{year}', [DisciplineGraduateController::class, 'checkYear'])->name('check');
    Route::get('/{id}', [DisciplineGraduateController::class, 'show'])->name('show');
});

// ==================== PROVINCIAL PROGRESSION API ROUTES ====================
Route::prefix('provincial-progression')->name('provincial-progression.')->group(function () {
    Route::get('/', [ProvincialProgressionController::class, 'getProgressionData'])->name('data');
    Route::get('/provinces', [ProvincialProgressionController::class, 'getProvinces'])->name('provinces');
    Route::get('/meta/years', [ProvincialProgressionController::class, 'getYears'])->name('years');
});

// ==================== GRADUATION RATE API ROUTES ====================
Route::prefix('graduation-rate')->name('graduation-rate.')->group(function () {

    Route::get('/enrollment-data/{academicYear}', [GraduationRateController::class, 'getEnrollmentData'])
        ->name('enrollment-data');

    Route::post('/calculate', [GraduationRateController::class, 'calculateProjectedGraduates'])
        ->name('calculate');

    Route::post('/save', [GraduationRateController::class, 'saveGraduationRate'])
        ->name('save');

    Route::get('/', [GraduationRateController::class, 'getAllGraduationRates'])
        ->name('all');

    // 🔥 MUST come BEFORE /{graduateYear} to avoid being swallowed by the wildcard
    Route::get('/check/{graduateYear}', [GraduationRateController::class, 'checkYear'])
        ->name('check');

    Route::get('/{graduateYear}', [GraduationRateController::class, 'getGraduationRate'])
        ->name('get');

    Route::delete('/{graduateYear}', [GraduationRateController::class, 'deleteGraduationRate'])
        ->name('delete');
});

// ==================== SUPPLY SIDE ANALYSIS ROUTES ====================
Route::prefix('supply-side-analysis')->group(function () {

    // ── Existing routes (unchanged) ──
    Route::get('/options', [SupplySideAnalysisController::class, 'index']);
    Route::get('/show', [SupplySideAnalysisController::class, 'show']);
    Route::post('/save', [SupplySideAnalysisController::class, 'save']);
    Route::get('/reset', [SupplySideAnalysisController::class, 'reset']);
    Route::delete('/delete', [SupplySideAnalysisController::class, 'delete']);
    Route::get('/archives', [SupplySideAnalysisController::class, 'getArchivedSis']);
    Route::get('/years', [SupplySideAnalysisController::class, 'getYears']);

    // ── NEW: Admin submits a draft (saved as 'pending') ──
    Route::post('/submit', [SupplySideAnalysisController::class, 'adminSubmit']);

    // ── NEW: Load the pending draft (used by both admin & statistician editors) ──
    // ⚠️ Must be before any wildcard routes
    Route::get('/pending-show', [SupplySideAnalysisController::class, 'showPending']);
    Route::get('/pending-all', [SupplySideAnalysisController::class, 'allPending']);
    // ── NEW: Badge count for statistician sidebar polling ──
    Route::get('/pending-count', [SupplySideAnalysisController::class, 'pendingCount']);

    // ── NEW: Approved workflow ──
    Route::get('/approved-all', [SupplySideAnalysisController::class, 'allApproved']);
    Route::get('/approved-count', [SupplySideAnalysisController::class, 'approvedCount']);
});