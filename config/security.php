<?php

/*
|--------------------------------------------------------------------------
| Security Settings
|--------------------------------------------------------------------------
| Central place for the authentication, rate-limit, transport and alerting
| settings used across the app. Every value can be overridden from .env.
*/

return [

    // ==================== HTTPS / TRANSPORT ====================
    // Defaults to on in production. Requests to localhost / 127.0.0.1 are
    // never redirected so a local XAMPP copy keeps working over plain http.
    // An empty FORCE_HTTPS= line counts as "not set" (use the default).
    'force_https' => in_array(env('FORCE_HTTPS'), [null, ''], true)
        ? env('APP_ENV') === 'production'
        : filter_var(env('FORCE_HTTPS'), FILTER_VALIDATE_BOOLEAN),

    // Strict-Transport-Security max-age in seconds (1 year). Sent only over HTTPS.
    'hsts_max_age' => (int) env('HSTS_MAX_AGE', 31536000),

    // Only needed when the app sits behind a reverse proxy / load balancer that
    // terminates TLS. Comma-separated proxy IPs, or * for any. Empty = trust none.
    'trusted_proxies' => env('TRUSTED_PROXIES'),

    // ==================== OTP (2FA) ====================
    'otp' => [
        'expires_minutes'   => (int) env('OTP_EXPIRES_MINUTES', 10),
        'max_attempts'      => (int) env('OTP_MAX_ATTEMPTS', 5),   // wrong codes before the login is cancelled
        'resend_cooldown'   => (int) env('OTP_RESEND_COOLDOWN', 60), // seconds between resends
    ],

    // ==================== LOGIN LOCKOUT ====================
    'lockout' => [
        'max_attempts'  => (int) env('LOGIN_MAX_ATTEMPTS', 5),      // failed passwords per account
        'decay_minutes' => (int) env('LOGIN_LOCKOUT_MINUTES', 15),  // how long the account stays locked
    ],

    // ==================== RATE LIMITS (requests per minute) ====================
    'rate_limits' => [
        'login'         => (int) env('RATE_LIMIT_LOGIN', 10),        // per IP
        'otp_verify'    => (int) env('RATE_LIMIT_OTP_VERIFY', 10),   // per IP
        'otp_resend'    => (int) env('RATE_LIMIT_OTP_RESEND', 3),    // per IP
        'password_mail' => (int) env('RATE_LIMIT_PASSWORD_MAIL', 3), // per IP
        'public_submit' => (int) env('RATE_LIMIT_PUBLIC_SUBMIT', 5), // per IP
        'api'           => (int) env('RATE_LIMIT_API', 240),         // per IP (public dashboard makes many chart calls)
        'staff_write'   => (int) env('RATE_LIMIT_STAFF_WRITE', 120), // per user
    ],

    // ==================== PASSWORD POLICY ====================
    'password' => [
        'min_length'    => (int) env('PASSWORD_MIN_LENGTH', 12),
        // Checks new passwords against the Have I Been Pwned breach list
        // (k-anonymity: only the first 5 chars of the SHA-1 hash leave the server).
        'uncompromised' => (bool) env('PASSWORD_CHECK_BREACHED', true),
    ],

    // ==================== MONITORING / ALERTS ====================
    // Comma-separated list. When set, high-severity security events
    // (account lockouts, OTP brute force) are emailed here.
    'alert_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('SECURITY_ALERT_EMAIL', ''))))),

    // Minimum minutes between two alert emails of the same type.
    'alert_throttle_minutes' => (int) env('SECURITY_ALERT_THROTTLE', 15),
];
