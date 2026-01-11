<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/export/csv', [DashboardController::class, 'exportCsv'])->name('export.csv');



Route::get('/Heigraduate', function () {
    return view('HeiGrad');
})->name('hei.graduate');

Route::get('/SkillGapDemand', function () {
    return view('SkillGapDemand');
})->name('Skill.Gap.Demand');

Route::get('/JobMarketOverview', function () {
    return view('JobMarketOverview');
})->name('Job.Market.Overview');

Route::get('/GovernmentData', function(){
    return view('GovernData');
})->name('Government.Data');

Route::get('/StakeHolderEngagement', function(){
    return view('StakeHolder');
})->name('Stake.Holder.Engagement');

Route::get('/Report', function(){
    return view('Reports');  
})->name('Report');

Route::get('/Settings',function(){
    return view('Setting');
})->name('Setting');

Route::get('/Logout',function(){
    return view('logout');
})->name('Logout');