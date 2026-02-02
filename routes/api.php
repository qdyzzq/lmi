<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\LaborMarketController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\JobTitleController;

/*
|--------------------------------------------------------------------------
| Chart API Routes
|--------------------------------------------------------------------------
| These endpoints provide JSON data for Chart.js
| Base URL: /api/... (Laravel adds this automatically)
*/

// ✅ CHART ROUTES - No /api prefix needed!
Route::get('/quarterly/{year}', [ChartController::class, 'getQuarterlyData']);
Route::get('/quarterly-range', [ChartController::class, 'getQuarterlyRange']);
Route::get('/available-years', [ChartController::class, 'getAvailableYears']);
Route::get('/latest-quarter', [ChartController::class, 'getLatestQuarter']);

// ✅ KPI ROUTES
Route::get('/kpi-cards', [KpiController::class, 'getKpiCards']);
Route::get('/kpi-cards/periods', [KpiController::class, 'getAvailablePeriods']);

// ✅ FORM ROUTE
Route::post('/labor-market/store', [LaborMarketController::class, 'store']);
Route::get('/autocomplete-data', [AutocompleteController::class, 'getAutocompleteData']);
Route::get('/autocomplete/search', [AutocompleteController::class, 'search']);
Route::get('/job-titles/{year}', [JobTitleController::class, 'getByYear']);