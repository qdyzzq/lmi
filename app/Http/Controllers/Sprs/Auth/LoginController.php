<?php

namespace App\Http\Controllers\Sprs\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /** How long a code stays usable. The SMS text says 10 minutes. */
    private const OTP_TTL_MINUTES = 10;

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tear down whatever came before.
        //
        // Without this, a browser that already held a verified session
        // stayed authenticated through the new login. Auth::check() was
        // true, the OTP flag from last time was still there, and the
        // person went straight to the dashboard — a fresh code was sent
        // and never asked for.
        //
        // On a shared office PC that means one person's OTP covers
        // everyone who uses that machine afterwards.
        if (Auth::check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $belongsToSprs = $user->role === 'super_admin' || ! is_null($user->office_id);

        if (! $belongsToSprs) {
            return back()->withErrors(['email' => 'This account does not have access to SPRS.']);
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'This account has been deactivated. Contact your Super Admin.']);
        }

        if (blank($user->phone_number)) {
            return back()->withErrors(['email' => 'No mobile number on this account. Contact your Super Admin.']);
        }

        // random_int, not rand. rand() is predictable from a few prior
        // outputs, and this is the second factor.
        $otp = random_int(100000, 999999);

        session([
            'sprs_otp'              => (string) $otp,
            'sprs_otp_pending'      => true,
            'sprs_otp_verified'     => false,
            'sprs_otp_generated_at' => now()->toDateTimeString(),
            'sprs_user_id'          => $user->id,
        ]);

        $this->sendOtpViaSms($user->phone_number, $otp);

        return redirect()->route('sprs.otp');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sprs.login')->with('success', 'Logged out successfully.');
    }

    private function sendOtpViaSms(string $phoneNumber, int $otp): void
    {
        $message = "Your OTP for the Strategic Planning & Reporting System (SPRS) is {$otp}. "
                 . 'Valid for ' . self::OTP_TTL_MINUTES . ' minutes. Do not share it with anyone.';

        $response = Http::withoutVerifying()
            ->timeout(15)
            ->get('https://messagingsuite.smart.com.ph/cgphttp/servlet/sendmsg', [
                'username'    => config('sms.username'),
                'password'    => config('sms.password'),
                'destination' => $phoneNumber,
                'text'        => $message,
            ]);

        // The code itself is deliberately absent from the log. Anyone with
        // read access to storage/logs would otherwise be able to sign in
        // as any user who logged in recently.
        Log::info('SPRS OTP SMS attempt', [
            'trigger'     => 'login',
            'destination' => substr($phoneNumber, -4),
            'status'      => $response->status(),
            'successful'  => $response->successful(),
        ]);
    }
}