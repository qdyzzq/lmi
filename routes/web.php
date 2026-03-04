<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LaborMarketController;
use App\Http\Controllers\LmiSubmissionController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\ProgramAdminController;




// ==================== PUBLIC ROUTES (No login required) ====================
Route::get('/programs-stories', [ProgramsController::class, 'index'])
    ->name('programs.stories');


Route::get('/program-admin', [ProgramsController::class, 'admin'])->name('program.admin');

Route::get('/programs-and-stories', function () {
    return view('programs-stories-static');
})->name('programs.stories.static');
    
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
    
    Route::post('/job-titles/{year}/approve', [JobTitleController::class, 'approve'])
        ->name('job-titles.approve');
    
    Route::post('/job-titles/{year}/reject', [JobTitleController::class, 'reject'])
        ->name('job-titles.reject');
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
    
    // LMI Submissions Routes (FIXED - removed duplicate /admin/ prefix)
    Route::get('/lmi-submissions', [LmiSubmissionController::class, 'adminIndex'])
        ->name('lmi-submissions.index');
    // JOBTITLEFORM Routes
    Route::get('/job-titles/form', [JobTitleController::class, 'showForm'])
        ->name('job-titles.form');
    
    Route::post('/job-titles/store', [JobTitleController::class, 'store'])
        ->name('job-titles.store');
    
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

       
});

 // Program Admin CRUD
Route::post('/programs', [ProgramAdminController::class, 'storeProgram'])->name('programs.store');
Route::put('/programs/{program}', [ProgramAdminController::class, 'updateProgram'])->name('programs.update');
Route::delete('/programs/{program}', [ProgramAdminController::class, 'destroyProgram'])->name('programs.destroy');

Route::put('/programs/{program}/description', [ProgramAdminController::class, 'updateDescription'])->name('programs.description');
Route::delete('/programs/{program}/description', [ProgramAdminController::class, 'destroyDescription']);

Route::post('/qualifications', [ProgramAdminController::class, 'storeQualification'])->name('qualifications.store');
Route::put('/qualifications/{qualification}', [ProgramAdminController::class, 'updateQualification'])->name('qualifications.update');
Route::delete('/qualifications/{qualification}', [ProgramAdminController::class, 'destroyQualification'])->name('qualifications.destroy');

Route::post('/steps', [ProgramAdminController::class, 'storeStep'])->name('steps.store');
Route::put('/steps/{step}', [ProgramAdminController::class, 'updateStep'])->name('steps.update');
Route::delete('/steps/{step}', [ProgramAdminController::class, 'destroyStep'])->name('steps.destroy');

Route::post('/stories', [ProgramAdminController::class, 'storeStory'])->name('stories.store');
Route::put('/stories/{story}', [ProgramAdminController::class, 'updateStory'])->name('stories.update');
Route::delete('/stories/{story}', [ProgramAdminController::class, 'destroyStory'])->name('stories.destroy');

Route::post('/testimonials', [ProgramAdminController::class, 'storeTestimonial'])->name('testimonials.store');
Route::put('/testimonials/{testimonial}', [ProgramAdminController::class, 'updateTestimonial'])->name('testimonials.update');
Route::delete('/testimonials/{testimonial}', [ProgramAdminController::class, 'destroy']);
Route::post('/carousel', [ProgramAdminController::class, 'storeSlide'])->name('carousel.store');
Route::put('/carousel/{slide}', [ProgramAdminController::class, 'updateSlide'])->name('carousel.update');
Route::delete('/carousel/{slide}', [ProgramAdminController::class, 'destroySlide'])->name('carousel.destroy');

Route::patch('/admin/programs/{program}/toggle-publish',
    [ProgramAdminController::class, 'togglePublish']
);

Route::patch('/programs/{program}/toggle-publish', [ProgramAdminController::class, 'togglePublish']);