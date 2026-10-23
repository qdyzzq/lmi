<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/sprs.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'              => \App\Http\Middleware\CheckRole::class,

        // LMI and SPRS share this install, this users table and this
        // session cookie — but not their session keys. One OTP gate
        // served both for a while, and since the SPRS one fails closed,
        // signing into LMI logged the person out and sent them to the
        // SPRS login. Two apps, two gates.
        'otp.verified'      => \App\Http\Middleware\EnsureOtpVerified::class,
        'sprs.otp.verified' => \App\Http\Middleware\Sprs\EnsureOtpVerified::class,

        'no.duplicate'      => \App\Http\Middleware\PreventDuplicateSession::class,
        'no.back'           => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();