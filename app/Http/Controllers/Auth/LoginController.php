<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Support\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request, OtpService $otp)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        $email      = Str::lower(trim($request->email));
        $lockoutKey = 'login-lockout:' . sha1($email);
        $maxTries   = config('security.lockout.max_attempts', 5);

        // Account lockout: too many wrong passwords for this email, from any IP.
        if (RateLimiter::tooManyAttempts($lockoutKey, $maxTries)) {
            $minutes = (int) ceil(RateLimiter::availableIn($lockoutKey) / 60);

            SecurityLog::warning('auth.login_blocked_locked_account', ['email' => $email]);

            return back()->withErrors([
                'email' => "Too many failed login attempts. This account is locked for {$minutes} minute(s).",
            ])->onlyInput('email');
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($lockoutKey, config('security.lockout.decay_minutes', 15) * 60);

            SecurityLog::warning('auth.login_failed', [
                'email'    => $email,
                'reason'   => $user ? 'bad_password' : 'unknown_user',
                'attempts' => RateLimiter::attempts($lockoutKey),
            ]);

            if (RateLimiter::tooManyAttempts($lockoutKey, $maxTries)) {
                SecurityLog::alert('auth.account_locked', ['email' => $email, 'attempts' => RateLimiter::attempts($lockoutKey)]);
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($lockoutKey);

        // New session ID before any login state is stored (prevents session fixation).
        $request->session()->regenerate();

        $sent = $otp->issue($user, 'sms');

        $redirect = redirect()->route('otp');

        return $sent
            ? $redirect
            : $redirect->with('error', 'We could not send the OTP by SMS. Please use "Receive OTP via Email instead".');
    }

    public function logout(Request $request)
    {
        SecurityLog::info('auth.logout');

        Cache::forget('user_session_' . Auth::id());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
