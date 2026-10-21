<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lets a logged-in admin/statistician change their own password.
 */
class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password'         => ['required', 'string', 'max:255', 'confirmed', 'different:current_password', Password::defaults()],
            ], [
                'current_password.current_password' => 'Your current password is incorrect.',
                'password.different'                => 'The new password must be different from your current password.',
            ]);
        } catch (ValidationException $e) {
            if (isset($e->errors()['current_password'])) {
                SecurityLog::warning('auth.password_change_failed', ['reason' => 'wrong_current_password']);
            }
            throw $e;
        }

        $user = $request->user();

        $user->forceFill([
            'password'       => $request->password, // hashed by the model cast
            'remember_token' => Str::random(60),
        ])->save();

        // New session ID; keep this session as the user's only valid one.
        $request->session()->regenerate();
        Cache::put('user_session_' . $user->id, $request->session()->getId(), now()->addMinutes(config('session.lifetime', 120)));

        SecurityLog::info('auth.password_changed');

        return back()->with('success', 'Your password has been changed.');
    }
}
