<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use App\Support\SecurityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * One-time password (second factor) handling for the login flow.
 *
 * The code is generated with a CSPRNG, only its hash is kept in the session,
 * it expires after config('security.otp.expires_minutes') and the login is
 * cancelled after config('security.otp.max_attempts') wrong entries.
 */
class OtpService
{
    public const RESULT_OK       = 'ok';
    public const RESULT_INVALID  = 'invalid';
    public const RESULT_EXPIRED  = 'expired';
    public const RESULT_LOCKED   = 'locked';
    public const RESULT_NO_LOGIN = 'no_login';

    /**
     * Start (or restart) the OTP step for a user whose password was verified,
     * and deliver the code. Returns false if delivery failed.
     */
    public function issue(User $user, string $via = 'sms', bool $isResend = false): bool
    {
        $otp = random_int(100000, 999999);

        // A resend keeps the wrong-attempt count, so resending cannot be used to get more guesses.
        $attempts = $isResend && session('user_id') === $user->id ? (int) session('otp_attempts', 0) : 0;

        session([
            'otp_hash'         => Hash::make((string) $otp),
            'otp_pending'      => true,
            'user_id'          => $user->id,
            'otp_generated_at' => now()->getTimestamp(),
            'otp_attempts'     => $attempts,
            'otp_last_sent_at' => now()->getTimestamp(),
        ]);

        // Remove the plain-text code key used by the previous implementation.
        session()->forget('otp');

        $sent = $via === 'email'
            ? $this->sendViaEmail($user, $otp)
            : $this->sendViaSms($user, $otp);

        if ($sent) {
            SecurityLog::info('auth.otp_sent', ['target_user_id' => $user->id, 'channel' => $via]);
        } else {
            // Nothing was delivered, so let the user switch channel right away.
            session(['otp_last_sent_at' => 0]);
        }

        return $sent;
    }

    /** Seconds the user must still wait before another resend is allowed. */
    public function resendWaitSeconds(): int
    {
        $last = (int) session('otp_last_sent_at', 0);
        $wait = $last + config('security.otp.resend_cooldown', 60) - now()->getTimestamp();

        return max(0, $wait);
    }

    public function verify(string $code): string
    {
        $userId      = session('user_id');
        $hash        = session('otp_hash');
        $generatedAt = session('otp_generated_at');

        if (session('otp_pending') !== true || ! $userId || ! $hash || ! $generatedAt) {
            return self::RESULT_NO_LOGIN;
        }

        // Stored as a Unix timestamp so the comparison does not depend on Carbon's signed diff behaviour.
        $ageSeconds = now()->getTimestamp() - (int) $generatedAt;

        if ($ageSeconds > config('security.otp.expires_minutes', 10) * 60) {
            SecurityLog::warning('auth.otp_expired', ['target_user_id' => $userId]);
            return self::RESULT_EXPIRED;
        }

        if (Hash::check($code, $hash)) {
            return self::RESULT_OK;
        }

        $attempts = (int) session('otp_attempts', 0) + 1;
        session(['otp_attempts' => $attempts]);

        if ($attempts >= config('security.otp.max_attempts', 5)) {
            SecurityLog::alert('auth.otp_locked_out', ['target_user_id' => $userId, 'attempts' => $attempts]);
            $this->clear();
            return self::RESULT_LOCKED;
        }

        SecurityLog::warning('auth.otp_failed', ['target_user_id' => $userId, 'attempts' => $attempts]);

        return self::RESULT_INVALID;
    }

    public function attemptsLeft(): int
    {
        return max(0, config('security.otp.max_attempts', 5) - (int) session('otp_attempts', 0));
    }

    public function clear(): void
    {
        session()->forget(['otp', 'otp_hash', 'otp_pending', 'user_id', 'otp_generated_at', 'otp_attempts', 'otp_last_sent_at']);
    }

    protected function sendViaEmail(User $user, int $otp): bool
    {
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return true;
        } catch (Throwable $e) {
            SecurityLog::warning('auth.otp_send_failed', ['target_user_id' => $user->id, 'channel' => 'email', 'error' => $e->getMessage()]);
            return false;
        }
    }

    protected function sendViaSms(User $user, int $otp): bool
    {
        if (empty($user->phone_number)) {
            SecurityLog::warning('auth.otp_send_failed', ['target_user_id' => $user->id, 'channel' => 'sms', 'error' => 'no_phone_number']);
            return false;
        }

        $message = "Your OTP for Labor Market Information System is {$otp}. "
                 . 'This OTP is valid for ' . config('security.otp.expires_minutes', 10) . ' minutes. '
                 . 'Do not share it with anyone. '
                 . 'If you did not request this, please contact support.';

        try {
            // TLS certificates are verified (previously disabled with withoutVerifying()).
            // The gateway only accepts its parameters on a GET query string.
            $response = Http::withOptions(['verify' => config('sms.verify')])
                ->timeout(15)
                ->get(config('sms.url'), [
                    'username'    => config('sms.username'),
                    'password'    => config('sms.password'),
                    'destination' => $user->phone_number,
                    'text'        => $message,
                ]);

            if ($response->failed()) {
                SecurityLog::warning('auth.otp_send_failed', ['target_user_id' => $user->id, 'channel' => 'sms', 'status' => $response->status()]);
                return false;
            }

            return true;
        } catch (Throwable $e) {
            // Exception text can contain the request URL (with the SMS password), so only the class is logged.
            SecurityLog::warning('auth.otp_send_failed', ['target_user_id' => $user->id, 'channel' => 'sms', 'error' => get_class($e)]);
            return false;
        }
    }
}
