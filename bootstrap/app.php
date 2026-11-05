<?php

use App\Support\SecurityLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        'strip.html'        => \App\Http\Middleware\StripHtmlInput::class,
        'audit'             => \App\Http\Middleware\AuditTrail::class,
        ]);

    // HTTPS redirect + security headers on every response (web, api and sprs).
    // Appended so it runs after TrustProxies has resolved the real scheme.
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

    // Rate limit for all /api routes (limiter defined in AppServiceProvider).
    $middleware->throttleApi('api');
    // Trusted reverse proxies are applied in AppServiceProvider (config('security.trusted_proxies')).
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Record blocked request-forgery attempts (expired or missing CSRF token).
        // Laravel turns TokenMismatchException into an HTTP 419 before rendering.
        $exceptions->render(function (HttpException $e) {
            if ($e->getStatusCode() === 419 && $e->getPrevious() instanceof TokenMismatchException) {
                SecurityLog::warning('request.csrf_mismatch');
            }

            return null; // keep Laravel's normal response
        });
    })->create();
