<?php

// app/Http/Middleware/EnsureOtpVerified.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * Handle the request and check if the user needs to complete OTP verification.
     *
     * This middleware performs the following actions:
     * 1. Checks if the user is logged in using `Auth::check()`.
     * 2. Checks if OTP verification is still pending by verifying the session flag `otp_pending`.
     * 3. If OTP is pending, redirects the user to the OTP verification page.
     * 4. If OTP is not pending, allows the request to proceed to the next middleware or controller.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @param \Closure $next The next middleware to call.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response The response after handling the request.
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is logged in and if OTP verification is still pending
        if (Auth::check() && session('otp_pending') === true) {
            // If OTP is pending, redirect the user to the OTP verification page
            return redirect()->route('otp');
        }

        // If OTP is not pending, proceed with the next request or middleware
        return $next($request);
    }

}
