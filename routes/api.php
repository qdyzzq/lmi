<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmploymentStatsController;
use App\Http\Controllers\LaborMarketController;

Route::get('/labor-vs-employment', [EmploymentStatsController::class, 'laborVsEmployment']);
Route::get('/unemployment-volume', [EmploymentStatsController::class, 'unemploymentVolume']);
Route::get('/quarterly/{year}', [EmploymentStatsController::class, 'quarterlyByYear']);
Route::post('/labor-market/store', [LaborMarketController::class, 'store'])->name('labor.market.store');