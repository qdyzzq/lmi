<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Find the user first WITHOUT logging them in yet
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
            // Generate OTP
$otp = rand(100000, 999999);

// Store OTP and user info in session (user is NOT logged in yet)
session([
    'otp'              => $otp,
    'otp_pending'      => true,
    'user_id'          => $user->id,
    'otp_generated_at' => now(),
]);

// Send OTP via SMS (only in production)
if (app()->environment('production')) {
    $username    = config('sms.username');
    $password    = config('sms.password');
    $phoneNumber = $user->phone_number;
    $message     = "Your OTP for Labor Market Intelligence System is {$otp}. This OTP is valid for 10 minutes. Do not share it with anyone. If you did not request this, please contact support.";

    Http::withoutVerifying()->get('https://messagingsuite.smart.com.ph/cgphttp/servlet/sendmsg', [
        'username'    => $username,
        'password'    => $password,
        'destination' => $phoneNumber,
        'text'        => $message,
    ]);
} else {
    // Locally, just log the OTP — no SMS sent
    \Log::info("[DEV] OTP for {$user->phone_number}: {$otp}");
}
        return redirect()->route('otp');
    }
/*
        // Generate OTP
        $otp = rand(100000, 999999);

        // Store OTP and user info in session (user is NOT logged in yet)
        session([
            'otp'              => $otp,
            'otp_pending'      => true,
            'user_id'          => $user->id,
            'otp_generated_at' => now(),
        ]);

        // Send OTP via SMS
        $username    = config('sms.username');
        $password    = config('sms.password');
        $phoneNumber = $user->phone_number;
        $message     = "Your OTP for Labor Market Intelligence System is {$otp}. This OTP is valid for 10 minutes. Do not share it with anyone. If you did not request this, please contact support.";

        Http::withoutVerifying()->get('https://messagingsuite.smart.com.ph/cgphttp/servlet/sendmsg', [
            'username'    => $username,
            'password'    => $password,
            'destination' => $phoneNumber,
            'text'        => $message,
        ]);

        // Redirect to OTP page (AuthenticatedSessionController handles the rest)
        return redirect()->route('otp');
    }   */

    public function logout(Request $request)
    {
        // Clear stored session to allow fresh login
        Cache::forget('user_session_' . Auth::id());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}