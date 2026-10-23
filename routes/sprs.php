<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sprs\Auth\LoginController;

use App\Http\Controllers\Sprs\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Sprs\DashboardController;
use App\Http\Controllers\Sprs\UserController;
use App\Http\Controllers\Sprs\ProfileController;

use App\Http\Controllers\Sprs\AccomplishmentController;
use App\Http\Controllers\Sprs\ReviewController;
use App\Http\Controllers\Sprs\CompilationController;

/*
|--------------------------------------------------------------------------
| SPRS
|--------------------------------------------------------------------------
|
| Loaded from bootstrap/app.php, inside the `web` group.
|
| Note the OTP gate is `sprs.otp.verified`, not `otp.verified`. LMI and
| SPRS share this install, this users table and this session cookie, but
| not their session keys — LMI writes `otp_*`, SPRS writes `sprs_otp_*`.
| One gate served both for a while, and because the SPRS one fails
| closed, signing into LMI logged the person out and sent them here. Two
| apps, two gates.
|
*/

Route::prefix('sprs')->name('sprs.')->group(function () {

    // ==================== LOGIN / OTP ====================
    Route::get('/login', function () {
        if (auth()->check() && auth()->user()->role === 'super_admin') {
            return redirect()->route('sprs.superadmin.dashboard');
        }
        return view('sprs.auth.login');
    })->name('login')->middleware('no.back');

    Route::post('/login', [LoginController::class, 'login'])->name('login.store');

   

    Route::get('/otp', [AuthenticatedSessionController::class, 'otp'])->name('otp');
    Route::post('/otp', [AuthenticatedSessionController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp/resend', [AuthenticatedSessionController::class, 'resendOtp'])->name('otp.resend');

    // ==================== SUPERADMIN ====================
    Route::middleware(['auth', 'sprs.otp.verified', 'no.duplicate', 'no.back', 'role:super_admin'])
        ->prefix('superadmin')
        ->name('superadmin.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');

            Route::get('/create_account', [UserController::class, 'create'])->name('create_account');
            Route::post('/create_account', [UserController::class, 'store'])->name('create_account.store');
        });

    // ==================== EVERY OTHER ROLE ====================
    Route::middleware(['auth', 'sprs.otp.verified', 'no.duplicate', 'no.back'])
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // ---- The office's own figures ----
            Route::get('/accomplishment', [AccomplishmentController::class, 'index'])
                ->name('accomplishment');

            Route::post('/accomplishment/{year}/{month}/sections/{section}',
                [AccomplishmentController::class, 'saveSection'])
                ->whereNumber(['year', 'month'])
                ->name('accomplishment.section.save');

            Route::post('/accomplishment/{year}/{month}/activities',
                [AccomplishmentController::class, 'saveActivities'])
                ->whereNumber(['year', 'month'])
                ->name('accomplishment.activities.save');

            Route::post('/accomplishment/{year}/{month}/submit',
                [AccomplishmentController::class, 'submit'])
                ->whereNumber(['year', 'month'])
                ->name('accomplishment.submit');

            // ---- Own report plus everything gathered from below ----
            Route::get('/compilation', [CompilationController::class, 'index'])
                ->name('compilation');

            // One report's figures for one section, fetched when a card
            // is expanded rather than rendered upfront.
            Route::get('/submissions/{submission}/section/{section}',
                [CompilationController::class, 'section'])
                ->name('compilation.section');

            // ---- Moving a batch up, or back down ----
            //
            // No {submission} on any of these: the chain moves in batches
            // at every tier, so the period is the unit, not the report.
            Route::post('/submissions/forward', [ReviewController::class, 'forward'])
                ->name('review.forward');

            Route::post('/submissions/approve', [ReviewController::class, 'approve'])
                ->name('review.approve');

            Route::post('/submissions/reject', [ReviewController::class, 'reject'])
                ->name('review.reject');
        });

    // ==================== PROFILE ====================
    Route::middleware(['auth', 'sprs.otp.verified'])
        ->group(function () {
            Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
                ->name('profile.avatar.update');
        });

    // ==================== LOGOUT ====================
    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

});