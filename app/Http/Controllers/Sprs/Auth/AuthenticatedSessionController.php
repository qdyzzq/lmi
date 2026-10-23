<?php

namespace App\Http\Controllers\Sprs\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /** How long a code stays usable. */
    private const OTP_TTL_MINUTES = 10;

    /**
     * Minimum gap between resends. Short enough not to annoy anyone on a
     * slow network, long enough that a stuck button doesn't send hundreds
     * of texts.
     */
    private const RESEND_COOLDOWN_SECONDS = 10;

    private function dashboardRouteFor(User $user): string
    {
        if ($user->role === 'super_admin') {
            return 'sprs.superadmin.dashboard';
        }

        return 'sprs.dashboard';
    }

    public function otp(): View|RedirectResponse
    {
        if (Auth::check() && session('sprs_otp_verified') === true) {
            return redirect()->route($this->dashboardRouteFor(Auth::user()));
        }

        if (session('sprs_otp_pending') !== true) {
            return redirect()->route('sprs.login');
        }

        return view('sprs.auth.otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        if (session('sprs_otp_pending') !== true) {
            return redirect()->route('sprs.login')
                ->withErrors(['email' => 'Please sign in again.']);
        }

        $generatedAt = session('sprs_otp_generated_at');

        // Carbon 3 returns a *signed* difference, so the old
        // `now()->diffInMinutes($generatedAt) > 10` produced a negative
        // number for a past time and never fired — expired codes were
        // being accepted. Comparing against a deadline avoids the whole
        // question of which direction the subtraction runs.
        $expired = ! $generatedAt
            || Carbon::parse($generatedAt)->addMinutes(self::OTP_TTL_MINUTES)->isPast();

        if ($expired) {
            return back()->withErrors(['otp' => 'That code has expired. Request a new one.']);
        }

        // Unlimited attempts, by request. The ten-minute expiry is the
        // only thing bounding a guesser now — a few thousand tries per
        // code window against a million possibilities. Worth an attempt
        // cap before this holds real data.
        //
        // hash_equals compares in constant time. A plain === leaks a
        // little about how much of the code was right through timing.
        if (! hash_equals((string) session('sprs_otp'), (string) $request->otp)) {
            return back()->withErrors(['otp' => 'That code is incorrect.']);
        }

        $user = User::find(session('sprs_user_id'));

        if (! $user || ! $user->is_active) {
            $this->abandon($request);

            return redirect()->route('sprs.login')
                ->withErrors(['email' => 'This account is no longer active.']);
        }

        session()->forget([
            'sprs_otp',
            'sprs_otp_pending',
            'sprs_otp_generated_at',
            'sprs_otp_last_sent_at',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // The marker EnsureOtpVerified looks for. Set after regenerate()
        // so it survives the new session id.
        session(['sprs_otp_verified' => true]);

        // Longer than the session on purpose. If this entry expires while
        // the session is still alive, the duplicate-login check silently
        // stops working, and nothing surfaces that it has.
        Cache::put(
            'sprs_user_session_' . $user->id,
            session()->getId(),
            now()->addMinutes((int) config('session.lifetime', 60) + 30)
        );

        return redirect()->route($this->dashboardRouteFor($user));
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        if (session('sprs_otp_pending') !== true) {
            return redirect()->route('sprs.login')
                ->withErrors(['otp' => 'Please log in again.']);
        }

        $user = User::find(session('sprs_user_id'));

        if (! $user) {
            return redirect()->route('sprs.login')
                ->withErrors(['otp' => 'Unable to resend the code. Please log in again.']);
        }

        // Each resend costs an SMS. Without a gap, holding the button
        // sends hundreds.
        $lastSent = session('sprs_otp_last_sent_at');

        if ($lastSent && Carbon::parse($lastSent)->addSeconds(self::RESEND_COOLDOWN_SECONDS)->isFuture()) {
            $wait = (int) ceil(now()->diffInSeconds(
                Carbon::parse($lastSent)->addSeconds(self::RESEND_COOLDOWN_SECONDS),
                absolute: true
            ));

            return back()->withErrors(['otp' => "Please wait {$wait} seconds before requesting another code."]);
        }

        $otp = random_int(100000, 999999);

        session([
            'sprs_otp'              => (string) $otp,
            'sprs_otp_pending'      => true,
            'sprs_otp_generated_at' => now()->toDateTimeString(),
            'sprs_otp_last_sent_at' => now()->toDateTimeString(),
        ]);

        $via = $request->input('via', 'sms');

        if ($via === 'email') {
            Mail::to($user->email)->send(new OtpMail($otp));

            return back()->with('success', 'A new code has been sent to your email address.');
        }

        if (blank($user->phone_number)) {
            return back()->withErrors(['otp' => 'No mobile number on this account. Try email instead.']);
        }

        $this->sendSms($user->phone_number, $otp);

        return back()->with('success', 'A new code has been sent to your registered phone number.');
    }

    /** Drop everything and force a fresh login. */
    private function abandon(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function sendSms(string $phoneNumber, int $otp): void
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

        // Neither the code nor the full number goes in the log. Anyone
        // with read access to storage/logs could otherwise sign in as any
        // user who logged in recently.
        Log::info('SPRS OTP SMS attempt', [
            'trigger'     => 'resend',
            'destination' => '••••' . substr($phoneNumber, -4),
            'status'      => $response->status(),
            'successful'  => $response->successful(),
        ]);
    }
}