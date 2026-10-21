<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Support\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the OTP verification view.
     * Redirects to login if no OTP is pending.
     */
    public function otp(): View|RedirectResponse
    {
        // If already logged in, redirect to their dashboard
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.job.Market.Demands.Form');
            }
            return redirect()->route('statistician.review');
        }

        if (session('otp_pending') !== true) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /**
     * Verify the OTP entered by the user.
     * Logs the user in and redirects based on their role.
     */
    public function verifyOtp(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $result = $otp->verify((string) $request->otp);

        if ($result === OtpService::RESULT_NO_LOGIN) {
            return redirect()->route('login')->withErrors(['email' => 'Your login session has expired. Please log in again.']);
        }

        if ($result === OtpService::RESULT_EXPIRED) {
            return back()->withErrors(['otp' => 'The OTP has expired. Please request a new one.']);
        }

        if ($result === OtpService::RESULT_LOCKED) {
            return redirect()->route('login')->withErrors([
                'email' => 'Too many incorrect OTP attempts. Please log in again.',
            ]);
        }

        if ($result === OtpService::RESULT_INVALID) {
            $left = $otp->attemptsLeft();
            return back()->withErrors(['otp' => "The OTP is incorrect. {$left} attempt(s) left."]);
        }

        $user = User::find(session('user_id'));

        // Clear OTP session data
        $otp->clear();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Your login session has expired. Please log in again.']);
        }

        // Log the user in
        Auth::login($user);

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Store session ID to prevent duplicate logins
        Cache::put('user_session_' . $user->id, session()->getId(), now()->addMinutes(config('session.lifetime', 120)));

        SecurityLog::info('auth.login_success', ['role' => $user->role]);

        // Redirect based on role
        if ($user->role === 'statistician') {
            return redirect()->route('statistician.review');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.job.Market.Demands.Form');
        }

        // Fallback (should not reach here)
        return redirect('/');
    }

    /**
     * Resend the OTP — via SMS (default) or email (on request).
     */
    public function resendOtp(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate([
            'via' => 'nullable|in:sms,email',
        ]);

        $userId = session('user_id');

        if (session('otp_pending') !== true || !$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Unable to resend OTP. Please log in again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found. Please log in again.']);
        }

        $wait = $otp->resendWaitSeconds();
        if ($wait > 0) {
            return back()->with('error', "Please wait {$wait} second(s) before requesting a new OTP.");
        }

        $via = $request->input('via', 'sms');

        if (!$otp->issue($user, $via, true)) {
            return back()->with('error', $via === 'email'
                ? 'We could not send the OTP by email. Please try SMS or contact the administrator.'
                : 'We could not send the OTP by SMS. Please try email or contact the administrator.');
        }

        return back()->with('success', $via === 'email'
            ? 'A new OTP has been sent to your email address.'
            : 'A new OTP has been sent to your registered phone number.');
    }
}
