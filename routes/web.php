<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LaborMarketController;
use App\Http\Controllers\LmiSubmissionController;
use App\Http\Controllers\AnalysisTemplateController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\LicensureRateController;
use App\Http\Controllers\DisciplineEnrollmentController;
use App\Http\Controllers\DisciplineGraduateController;
use App\Http\Controllers\SupplySideAnalysisController;

// ==================== PUBLIC ROUTES (No login required) ====================
Route::get('/ProgramStories', function () {
    return view('ProgramStories');
})->name('program.stories');
Route::post('/lmi/submit', [LmiSubmissionController::class, 'store'])->name('lmi.submit');
Route::get('/login', function () {
    return view('auth.Login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');


// PUBLIC DASHBOARD - Anyone can view
Route::get('/', [DashboardController::class, 'index'])->name('home');

// Public view pages (anyone can access)
Route::get('/JobMarketDemands', [DashboardController::class, 'jobMarket'])->name('Job.Market.Demands');

Route::get('/JobMarketOverview', function () {
    return view('JobMarketOverview');
})->name('Job.Market.Overview');

Route::get('/SupplySide', function(){
    return view('SupplySide');
})->name('Supply.Side');

Route::get('/Heigraduate', function () {
    return view('HeiGrad');
})->name('hei.graduate');

Route::get('/SkillGapDemand', function () {
    return view('SkillGapDemand');
})->name('Skill.Gap.Demand');

Route::get('/GovernmentData', function(){
    return view('GovernData');
})->name('Government.Data');

Route::get('/StakeHolder', function(){
    return view('StakeHolder');
})->name('Stake.Holder');

Route::get('/Report', function(){
    return view('Reports');  
})->name('Report');

Route::get('/Settings', function(){
    return view('Setting');
})->name('Setting');

//==================== PROTECTED ROUTES (Login required for updates) ====================
Route::middleware(['auth', 'role:statistician'])->prefix('statistician')->name('statistician.')->group(function () {
    
    // Statistician Review Page (Login required)
    Route::get('/review', [LaborMarketController::class, 'index'])
        ->name('review');

    // Statistician checks for duplicates before posting to final database
    Route::post('/labor-market/check-post', [LaborMarketController::class, 'checkPost'])
        ->name('labor-market.check.post');
    
    // Statistician posts verified data to final database
    Route::post('/labor-market/post', [LaborMarketController::class, 'post'])
        ->name('labor-market.post');

    // Statistician job title verification
    Route::get('/job-titles/pending', [JobTitleController::class, 'pendingSubmissions'])
        ->name('job-titles.pending');

    // Live polling — returns pending job title count
    Route::get('/job-titles/pending-count', [JobTitleController::class, 'pendingCount'])
        ->name('job-titles.pending-count');
    
    Route::post('/job-titles/{year}/approve', [JobTitleController::class, 'approve'])
        ->name('job-titles.approve');
    
    Route::post('/job-titles/{year}/reject', [JobTitleController::class, 'reject'])
        ->name('job-titles.reject');

    // Live polling — returns pending labor market data count
    Route::get('/labor-market/pending-count', [LaborMarketController::class, 'counts'])
        ->name('labor-market.pending-count');

    // Analysis Templates Editor
    Route::get('/templates', [AnalysisTemplateController::class, 'editor'])
        ->name('templates');

    Route::get('/supply-side-editor', [SupplySideAnalysisController::class, 'editor'])
    ->name('supply-side-editor');
});

// Routes accessible by both Admin and Statistician
Route::middleware(['auth', 'role:admin,statistician'])->group(function () {
    
    // Admin checks for duplicates before submitting to pending (Dashboard)
    Route::post('/labor-market/check-duplicate', [LaborMarketController::class, 'check'])
        ->name('labor.market.check');

    // Admin submits data to pending queue (Dashboard)
    Route::post('/labor-market/submit-pending', [LaborMarketController::class, 'submitPending'])
        ->name('labor.market.submit.pending');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Admin routes (protected by auth middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Labor Market Routes
    Route::post('/labor-market/check-before-post', [LaborMarketController::class, 'checkBeforePost'])
        ->name('labor.market.check.post');
    
    Route::post('/labor-market/post-verified', [LaborMarketController::class, 'postVerifiedData'])
        ->name('labor.market.post');

    // LMI Submissions Routes
    Route::get('/lmi-submissions', [LmiSubmissionController::class, 'adminIndex'])
        ->name('lmi-submissions.index');
    
    // JOB TITLE FORM Routes
    Route::get('/job-titles/form', [JobTitleController::class, 'showForm'])
        ->name('job-titles.form');
    
    Route::post('/job-titles/store', [JobTitleController::class, 'store'])
        ->name('job-titles.store');
    
    // LICENSURE RATES Routes
    Route::get('/licensure-rates/form', [LicensureRateController::class, 'showForm'])
        ->name('licensure-rates.form');

    Route::get('/licensure-rates/check-year/{year}', [LicensureRateController::class, 'checkYear'])
        ->name('licensure-rates.check-year');
    
    // Store licensure rate data (POST from form)
    Route::post('/licensure-rates', [LicensureRateController::class, 'store'])
        ->name('licensure-rates.store');
    
    // Update a single entry
    Route::put('/licensure-rates/{id}', [LicensureRateController::class, 'update'])
        ->name('licensure-rates.update');
    
    // Delete a single entry
    Route::delete('/licensure-rates/{id}', [LicensureRateController::class, 'destroy'])
        ->name('licensure-rates.destroy');
    
    // Delete all data for a specific year
    Route::delete('/licensure-rates/delete-year/{year}', [LicensureRateController::class, 'deleteYear'])
        ->name('licensure-rates.delete-year');

    // ==================== DISCIPLINE ENROLLMENT Routes ====================
    
    // Show the enrollment form
    Route::get('/discipline-enrollment/form', [DisciplineEnrollmentController::class, 'showForm'])
        ->name('discipline-enrollment.form');
    
    
    // Store enrollment data (POST from form)
    Route::post('/discipline-enrollment', [DisciplineEnrollmentController::class, 'store'])
        ->name('discipline-enrollment.store');
    
    // Update a single record
    Route::put('/discipline-enrollment/{id}', [DisciplineEnrollmentController::class, 'update'])
        ->name('discipline-enrollment.update');
    
    // Delete a single record
    Route::delete('/discipline-enrollment/{id}', [DisciplineEnrollmentController::class, 'destroy'])
        ->name('discipline-enrollment.destroy');
    
    // Delete all data for a specific year

        Route::delete('/discipline-enrollment/delete/{year}', [DisciplineEnrollmentController::class, 'deleteYear'])
        ->name('discipline-enrollment.delete');

        // DISCIPLINE GRADUATE Routes
    
    // Show the graduate form/dashboard
    Route::get('/discipline-graduate/form', [DisciplineGraduateController::class, 'showForm'])
        ->name('discipline-graduate.form');
    
    
    
    // Store graduate data (POST from form)
    Route::post('/discipline-graduate', [DisciplineGraduateController::class, 'store'])
        ->name('discipline-graduate.store');
    
    // Update a single record
    Route::put('/discipline-graduate/{id}', [DisciplineGraduateController::class, 'update'])
        ->name('discipline-graduate.update');
    
    // Delete a single record
    Route::delete('/discipline-graduate/{id}', [DisciplineGraduateController::class, 'destroy'])
        ->name('discipline-graduate.destroy');
    
    // Delete all data for a specific year
    Route::delete('/discipline-graduate/delete/{year}', [DisciplineGraduateController::class, 'deleteYear'])
        ->name('discipline-graduate.delete');



    // Live polling — returns submission counts for real-time badge updates
    // ⚠️ MUST be before /{id} wildcard routes
    Route::get('/lmi-submissions/counts', [LmiSubmissionController::class, 'counts'])
        ->name('lmi-submissions.counts');

    // LMI Submissions Detail Routes
    Route::get('/lmi-submissions/{id}', [LmiSubmissionController::class, 'adminShow'])
        ->name('lmi-submissions.show');
    
    Route::put('/lmi-submissions/{id}', [LmiSubmissionController::class, 'update'])
        ->name('lmi-submissions.update');

    Route::put('/lmi-submissions/{id}/update-diagnosis', [LmiSubmissionController::class, 'updateDiagnosis'])
        ->name('lmi-submissions.update-diagnosis');
    
    Route::put('/lmi-submissions/{id}/update-engagement', [LmiSubmissionController::class, 'updateEngagement'])
        ->name('lmi-submissions.update-engagement');
    
    Route::put('/lmi-submissions/{id}/update-roles', [LmiSubmissionController::class, 'updateRoles'])
        ->name('lmi-submissions.update-roles');
    
    Route::post('/lmi-submissions/{id}/approve', [LmiSubmissionController::class, 'approve'])
        ->name('lmi-submissions.approve');
    
    Route::post('/lmi-submissions/{id}/reject', [LmiSubmissionController::class, 'reject'])
        ->name('lmi-submissions.reject');
        
        Route::post('/lmi-submissions/{id}/restore-pending', [LmiSubmissionController::class, 'restorePending'])
    ->name('lmi-submissions.restore-pending');
});