<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SecurityLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * "Forgot password" flow: email a signed, single-use reset link (valid 60 minutes),
 * then let the user choose a new password that meets the password policy.
 */
class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email|max:255']);

        $email  = Str::lower(trim($request->email));
        $status = Password::sendResetLink(['email' => $email]);

        SecurityLog::info('auth.password_reset_requested', ['email' => $email, 'status' => $status]);

        // Same answer whether or not the account exists, so emails cannot be enumerated.
        return back()->with('success', 'If that email address is registered, a password reset link has been sent to it. The link is valid for 60 minutes.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email|max:255',
            'password' => ['required', 'string', 'max:255', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => $password, // hashed by the model cast
                    'remember_token' => Str::random(60),
                ])->save();

                // End any active session of this user.
                Cache::forget('user_session_' . $user->id);

                // A successful reset also lifts a login lockout.
                RateLimiter::clear('login-lockout:' . sha1(Str::lower($user->email)));

                event(new PasswordReset($user));

                SecurityLog::info('auth.password_reset_completed', ['target_user_id' => $user->id]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            SecurityLog::warning('auth.password_reset_failed', ['email' => Str::lower((string) $request->email), 'status' => $status]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset link is invalid or has expired. Please request a new one.']);
        }

        return redirect()->route('login')->with('success', 'Your password has been reset. You can now log in.');
    }
}
