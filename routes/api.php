<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\LaborMarketController;

/*
|--------------------------------------------------------------------------
| Chart API Routes
|--------------------------------------------------------------------------
| These endpoints provide JSON data for Chart.js
| Base URL: /api/...
*/

// Get quarterly data for a specific year
// Endpoint: GET /api/quarterly/2025
// Used by: Labor Force Chart & Unemployment Chart
Route::get('/quarterly/{year}', [ChartController::class, 'getQuarterlyData']);

// Optional: Get quarterly data for a year range
// Endpoint: GET /api/quarterly-range?start=2024&end=2025
Route::get('/quarterly-range', [ChartController::class, 'getQuarterlyRange']);

// Optional: Get all available years for dropdowns
// Endpoint: GET /api/available-years
Route::get('/available-years', [ChartController::class, 'getAvailableYears']);

// Optional: Get latest quarter data (for KPI cards)
// Endpoint: GET /api/latest-quarter
Route::get('/latest-quarter', [ChartController::class, 'getLatestQuarter']);

Route::get('/kpi-cards', [KpiController::class, 'getKpiCards']);
Route::get('/kpi-cards/periods', [KpiController::class, 'getAvailablePeriods']);
//GET ALL AVAILABLE YEARS FROM THE DATABASE
Route::get('/api/available-years', [ChartController::class, 'getAvailableYears']);



//API FOR FORM
Route::post('/labor-market/store', [LaborMarketController::class, 'store']);