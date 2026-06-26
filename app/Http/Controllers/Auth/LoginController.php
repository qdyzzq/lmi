<?php

namespace App\Http\Controllers\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $otp = rand(100000, 999999);

        session([
            'otp'              => $otp,
            'otp_pending'      => true,
            'user_id'          => $user->id,
            'otp_generated_at' => now(),
        ]);

        $this->sendOtpViaSms($user->phone_number, $otp);

        return redirect()->route('otp');
    }

    /**
     * Resend OTP — via SMS (default) or email (on request).
     */
    public function resendOtp(Request $request)
    {
        if (!session('otp_pending')) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $otp = rand(100000, 999999);

        session([
            'otp'              => $otp,
            'otp_generated_at' => now(),
        ]);

        $via = $request->input('via', 'sms');

        if ($via === 'email') {
            Mail::to($user->email)->send(new OtpMail($otp));
            return back()->with('success', 'A new OTP has been sent to your email address.');
        }

        // Default: SMS
        $this->sendOtpViaSms($user->phone_number, $otp);

        return back()->with('success', 'A new OTP has been sent to your phone number.');
    }

    public function logout(Request $request)
    {
        Cache::forget('user_session_' . Auth::id());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function sendOtpViaSms(string $phoneNumber, int $otp): void
{
    $message = "Your OTP for Labor Market Information System is {$otp}. "
             . "This OTP is valid for 10 minutes. "
             . "Do not share it with anyone. "
             . "If you did not request this, please contact support.";

    $response = Http::withoutVerifying()->get('https://messagingsuite.smart.com.ph/cgphttp/servlet/sendmsg', [
        'username'    => config('sms.username'),
        'password'    => config('sms.password'),
        'destination' => $phoneNumber,
        'text'        => $message,
    ]);

}
}
