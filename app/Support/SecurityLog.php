<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Writes security events to the dedicated "security" log channel
 * (storage/logs/security-YYYY-MM-DD.log, one JSON object per line).
 *
 * Every entry carries who, from where and when, so the log can be used
 * for incident investigation and is summarised by `php artisan security:report`.
 * Alert-level events are also emailed to SECURITY_ALERT_EMAIL when configured.
 *
 * Never pass passwords, OTP codes or tokens in $context.
 */
class SecurityLog
{
    public static function info(string $event, array $context = []): void
    {
        static::write('info', $event, $context);
    }

    public static function warning(string $event, array $context = []): void
    {
        static::write('warning', $event, $context);
    }

    public static function alert(string $event, array $context = []): void
    {
        static::write('alert', $event, $context);
        static::notify($event, $context);
    }

    protected static function write(string $level, string $event, array $context): void
    {
        try {
            $request = request();

            Log::channel('security')->{$level}($event, array_merge([
                'event'      => $event,
                'user_id'    => Auth::id(),
                'ip'         => $request?->ip(),
                'method'     => $request?->method(),
                'path'       => $request?->path(),
                'user_agent' => substr((string) $request?->userAgent(), 0, 255),
            ], $context));
        } catch (Throwable $e) {
            // Logging must never break the request it is describing.
        }
    }

    /**
     * Email an alert to the security contacts, at most once per event type
     * per throttle window so an ongoing attack does not flood the inbox.
     */
    protected static function notify(string $event, array $context): void
    {
        $recipients = config('security.alert_emails', []);

        if (empty($recipients)) {
            return;
        }

        try {
            $throttleKey = 'security-alert:' . $event;

            if (! Cache::add($throttleKey, true, now()->addMinutes(config('security.alert_throttle_minutes', 15)))) {
                return;
            }

            $lines = [
                'Security alert from ' . config('app.name') . ' (' . config('app.url') . ')',
                '',
                'Event: ' . $event,
                'Time:  ' . now()->toDateTimeString(),
                'IP:    ' . request()?->ip(),
            ];

            foreach ($context as $key => $value) {
                $lines[] = ucfirst((string) $key) . ': ' . (is_scalar($value) ? $value : json_encode($value));
            }

            $lines[] = '';
            $lines[] = 'Further alerts of this type are suppressed for '
                . config('security.alert_throttle_minutes', 15) . ' minutes. '
                . 'See storage/logs/security-*.log or run: php artisan security:report';

            Mail::raw(implode("\n", $lines), function ($message) use ($recipients, $event) {
                $message->to($recipients)->subject('[Security Alert] ' . $event);
            });
        } catch (Throwable $e) {
            Log::error('Security alert email failed', ['event' => $event, 'error' => $e->getMessage()]);
        }
    }
}
