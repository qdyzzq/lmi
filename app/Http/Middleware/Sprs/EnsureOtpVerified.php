<?php

// app/Http/Middleware/EnsureOtpVerified.php
namespace App\Http\Middleware\Sprs;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOtpVerified
{
    /**
     * Block anything past login until the OTP has been entered.
     *
     * The session key is `sprs_otp_pending`. It previously read
     * `otp_pending`, which LoginController never sets — so the condition
     * was always false and this middleware passed every request through.
     *
     * Fails closed: a logged-in user with no explicit "verified" marker is
     * sent to the OTP screen rather than let through. A missing flag is
     * more likely to mean something went wrong than that the person is
     * genuinely verified.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);   // the auth middleware handles this
        }

        if (session('sprs_otp_pending') === true) {
            return redirect()->route('sprs.otp');
        }

        if (session('sprs_otp_verified') !== true) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('sprs.login')
                ->withErrors(['email' => 'Please sign in again.']);
        }

        return $next($request);
    }
}