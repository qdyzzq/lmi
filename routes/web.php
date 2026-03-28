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
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\ProgramAdminController;
use App\Http\Controllers\PesoDirectoryController;



// ==================== PUBLIC ROUTES (No login required) ====================
Route::get('/programs-stories', [ProgramsController::class, 'index'])
    ->name('programStories');

Route::get('/peso-directory', [PesoDirectoryController::class, 'index'])->name('peso.directory');

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

    // Supply Side Analysis Editor (statistician publishes directly)
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
    Route::get('/JobMarketDemandsForm', function () {
        return view('admin.jobMarketDemandsForm');
    })->name('job.Market.Demands.Form');
    
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
    
    Route::post('/licensure-rates', [LicensureRateController::class, 'store'])
        ->name('licensure-rates.store');
    
    Route::put('/licensure-rates/{id}', [LicensureRateController::class, 'update'])
        ->name('licensure-rates.update');
    
    Route::delete('/licensure-rates/{id}', [LicensureRateController::class, 'destroy'])
        ->name('licensure-rates.destroy');
    
    Route::delete('/licensure-rates/delete-year/{year}', [LicensureRateController::class, 'deleteYear'])
        ->name('licensure-rates.delete-year');

    // ==================== DISCIPLINE ENROLLMENT Routes ====================
    Route::get('/discipline-enrollment/form', [DisciplineEnrollmentController::class, 'showForm'])
        ->name('discipline-enrollment.form');
    
    Route::post('/discipline-enrollment', [DisciplineEnrollmentController::class, 'store'])
        ->name('discipline-enrollment.store');
    
    Route::put('/discipline-enrollment/{id}', [DisciplineEnrollmentController::class, 'update'])
        ->name('discipline-enrollment.update');
    
    Route::delete('/discipline-enrollment/{id}', [DisciplineEnrollmentController::class, 'destroy'])
        ->name('discipline-enrollment.destroy');
    
    Route::delete('/discipline-enrollment/delete/{year}', [DisciplineEnrollmentController::class, 'deleteYear'])
        ->name('discipline-enrollment.delete');

    // ==================== DISCIPLINE GRADUATE Routes ====================
    Route::get('/discipline-graduate/form', [DisciplineGraduateController::class, 'showForm'])
        ->name('discipline-graduate.form');
    
    Route::post('/discipline-graduate', [DisciplineGraduateController::class, 'store'])
        ->name('discipline-graduate.store');
    
    Route::put('/discipline-graduate/{id}', [DisciplineGraduateController::class, 'update'])
        ->name('discipline-graduate.update');
    
    Route::delete('/discipline-graduate/{id}', [DisciplineGraduateController::class, 'destroy'])
        ->name('discipline-graduate.destroy');
    
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

    // ==================== SUPPLY SIDE ANALYSIS Routes ====================
    Route::get('/supply-side-editor', [SupplySideAnalysisController::class, 'adminEditor'])
        ->name('supply-side-editor');

    // ==================== ANALYSIS TEMPLATE Routes ====================
    Route::get('/template-editor', [AnalysisTemplateController::class, 'adminEditor'])
        ->name('template-editor');

    // ==================== PROGRAMS & STORIES ADMIN ====================
    Route::get('/programs-stories', [ProgramAdminController::class, 'index'])
        ->name('program-stories-editor');

    Route::get('/programs-stories/preview', [ProgramAdminController::class, 'preview'])
        ->name('program-stories-preview');
    Route::get('/stories/filter',  [ProgramAdminController::class, 'filterStories'])->name('stories.filter');
    Route::get('/stories/years',   [ProgramAdminController::class, 'storyYears'])  ->name('stories.years');
    Route::get('/stories/export',  [ProgramAdminController::class, 'exportStories'])->name('stories.export');

    // ==================== PROGRAM ADMIN CRUD ====================
    Route::post('/programs', [ProgramAdminController::class, 'storeProgram'])->name('programs.store');
    Route::put('/programs/{program}', [ProgramAdminController::class, 'updateProgram'])->name('programs.update');
    Route::delete('/programs/{program}', [ProgramAdminController::class, 'destroyProgram'])->name('programs.destroy');
    Route::put('/programs/{program}/description', [ProgramAdminController::class, 'updateDescription'])->name('programs.description');
    Route::delete('/programs/{program}/description', [ProgramAdminController::class, 'destroyDescription'])->name('programs.description.destroy');
    Route::patch('/programs/{program}/toggle-publish', [ProgramAdminController::class, 'togglePublish'])->name('programs.toggle-publish');
    Route::get('/programs/{program}/fragment', [ProgramAdminController::class, 'fragment']);
    Route::patch('/programs/{program}/republish', [ProgramAdminController::class, 'republish']);
    Route::post('/qualifications', [ProgramAdminController::class, 'storeQualification'])->name('qualifications.store');
    Route::put('/qualifications/{qualification}', [ProgramAdminController::class, 'updateQualification'])->name('qualifications.update');
    Route::delete('/qualifications/{qualification}', [ProgramAdminController::class, 'destroyQualification'])->name('qualifications.destroy');
    Route::delete('/qualifications/type/{type}/program/{program}', [ProgramAdminController::class, 'destroyQualificationsByType'])
        ->name('qualifications.destroy-by-type');
    Route::get('/admin/stories/export', [ProgramAdminController::class, 'exportStories']);

    Route::post('/steps', [ProgramAdminController::class, 'storeStep'])->name('steps.store');
    Route::put('/steps/{step}', [ProgramAdminController::class, 'updateStep'])->name('steps.update');
    Route::delete('/steps/{step}', [ProgramAdminController::class, 'destroyStep'])->name('steps.destroy');

    Route::post('/stories', [ProgramAdminController::class, 'storeStory'])->name('stories.store');
    Route::put('/stories/{story}', [ProgramAdminController::class, 'updateStory'])->name('stories.update');
    Route::delete('/stories/{story}', [ProgramAdminController::class, 'destroyStory'])->name('stories.destroy');

    Route::post('/testimonials', [ProgramAdminController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::put('/testimonials/{testimonial}', [ProgramAdminController::class, 'updateTestimonial'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [ProgramAdminController::class, 'destroyTestimonial'])->name('testimonials.destroy');

    Route::post('/carousel', [ProgramAdminController::class, 'storeSlide'])->name('carousel.store');
    Route::put('/carousel/{slide}', [ProgramAdminController::class, 'updateSlide'])->name('carousel.update');
    Route::delete('/carousel/{slide}', [ProgramAdminController::class, 'destroySlide'])->name('carousel.destroy');

   // ==================== FIELD OFFICES (PESO/JPO Directory) ====================
    Route::get('/peso-directory',            [PesoDirectoryController::class, 'adminIndex'])       ->name('peso-directory.index');
    Route::post('/field-offices',            [PesoDirectoryController::class, 'storeFieldOffice']) ->name('field-offices.store');
    Route::put('/field-offices/{office}',    [PesoDirectoryController::class, 'updateFieldOffice'])->name('field-offices.update');
    Route::delete('/field-offices/{office}', [PesoDirectoryController::class, 'destroyFieldOffice'])->name('field-offices.destroy');
    Route::post('/field-offices/touch',      [PesoDirectoryController::class, 'touchDirectory']);
    Route::post('/field-offices/publish',    [PesoDirectoryController::class, 'publishDirectory']);
    Route::get('/office-types',              [PesoDirectoryController::class, 'getOfficeTypes']);
    Route::post('/office-types',             [PesoDirectoryController::class, 'storeOfficeType']);
    Route::put('/office-types/{name}',       [PesoDirectoryController::class, 'updateOfficeType']);
    Route::delete('/office-types/{name}',    [PesoDirectoryController::class, 'destroyOfficeType']);
 
    // ==================== POSITION TITLES ====================
    Route::get('/position-titles',           [PesoDirectoryController::class, 'getPositionTitles']);
    Route::post('/position-titles',          [PesoDirectoryController::class, 'storePositionTitle']);
    Route::put('/position-titles/{name}',    [PesoDirectoryController::class, 'updatePositionTitle']);
    Route::delete('/position-titles/{name}', [PesoDirectoryController::class, 'destroyPositionTitle']);
 
    // ✅ PESO Info Settings (description, objective, how to avail, lists)
    Route::get('/peso-info',       [PesoDirectoryController::class, 'getPesoInfo']);
    Route::put('/peso-info/{key}', [PesoDirectoryController::class, 'updatePesoInfo']);

    //=========================CTA SECTION=====================
    Route::get('/cta-section',     [ProgramAdminController::class, 'getCtaSection'])    ->name('cta-section.show');
    Route::put('/cta-section',     [ProgramAdminController::class, 'updateCtaSection']);
    Route::post('/cta-section/publish', [ProgramAdminController::class, 'publishCtaSection']) ->name('cta-section.publish');
});