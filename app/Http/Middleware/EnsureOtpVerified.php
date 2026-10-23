<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * LMI's OTP gate.
 *
 * Reads LMI's own session keys — `otp_pending`, set by
 * App\Http\Controllers\Auth\LoginController.
 *
 * RECONSTRUCTED. The original was overwritten when SPRS reused this
 * class. Check it against git history if you have it; the behaviour here
 * is the straightforward reading of what LoginController writes.
 */
class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Mid-login: credentials accepted, code not yet entered.
        if ($request->session()->get('otp_pending')) {
            return redirect()->route('otp');
        }

        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}