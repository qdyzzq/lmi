<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $sessionOtp     = session('otp');
        $userId         = session('user_id');
        $otpGeneratedAt = session('otp_generated_at');

        // Check if OTP has expired (10 minutes)
        if (!$otpGeneratedAt || now()->diffInMinutes($otpGeneratedAt) > 10) {
            return back()->withErrors(['otp' => 'The OTP has expired. Please request a new one.']);
        }

        // Check if OTP matches
        if ($request->otp != $sessionOtp) {
            return back()->withErrors(['otp' => 'The OTP is incorrect.']);
        }

        // Clear OTP session data
        session()->forget(['otp', 'otp_pending']);

        // Log the user in
        $user = User::find($userId);
        Auth::login($user);

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Store session ID to prevent duplicate logins
        Cache::put('user_session_' . $user->id, session()->getId(), now()->addHours(2));

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
     * Resend the OTP to the user's registered phone number.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Unable to resend OTP. Please log in again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'User not found. Please log in again.']);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Update session
        session([
            'otp'              => $otp,
            'otp_pending'      => true,
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

        return back()->with('success', 'A new OTP has been sent to your registered phone number.');
    }
}