<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmploymentStatsController;

Route::get('/labor-vs-employment', [EmploymentStatsController::class, 'laborVsEmployment']);
Route::get('/unemployment-volume', [EmploymentStatsController::class, 'unemploymentVolume']);