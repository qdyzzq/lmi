<?php

namespace App\Providers;

use App\Support\SecurityLog;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureHttps();
        $this->configurePasswordPolicy();
        $this->configureRateLimiting();

        // @safehtml($html) — prints rich-text HTML with scripts/handlers removed.
        Blade::directive('safehtml', function ($expression) {
            return "<?php echo \App\Support\HtmlSanitizer::clean({$expression}); ?>";
        });
    }

    /**
     * Generate https:// URLs (assets, redirects, reset links) when HTTPS is enforced.
     */
    protected function configureHttps(): void
    {
        // Behind a TLS-terminating proxy, trust its X-Forwarded-* headers so the
        // original https scheme and client IP are seen (needed for HSTS and rate limits).
        if ($proxies = config('security.trusted_proxies')) {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        if (config('security.force_https') && ! $this->app->runningInConsole()) {
            $host = request()->getHost();

            if (! in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
                URL::forceScheme('https');

                // Session cookie is only ever sent over HTTPS.
                config(['session.secure' => true]);
            }
        }
    }

    /**
     * Password policy used everywhere a password is set (reset, change).
     */
    protected function configurePasswordPolicy(): void
    {
        Password::defaults(function () {
            $rule = Password::min(config('security.password.min_length', 12))
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();

            return config('security.password.uncompromised') && ! $this->app->runningUnitTests()
                ? $rule->uncompromised()
                : $rule;
        });
    }

    protected function configureRateLimiting(): void
    {
        $perMinute = fn (string $key) => (int) config('security.rate_limits.' . $key);

        RateLimiter::for('login', fn (Request $request) => $this->limit('login', $perMinute('login'), $request->ip()));
        RateLimiter::for('otp-verify', fn (Request $request) => $this->limit('otp-verify', $perMinute('otp_verify'), $request->ip()));
        RateLimiter::for('otp-resend', fn (Request $request) => $this->limit('otp-resend', $perMinute('otp_resend'), $request->ip()));
        RateLimiter::for('password-mail', fn (Request $request) => $this->limit('password-mail', $perMinute('password_mail'), $request->ip()));
        RateLimiter::for('public-submit', fn (Request $request) => $this->limit('public-submit', $perMinute('public_submit'), $request->ip()));
        RateLimiter::for('api', fn (Request $request) => $this->limit('api', $perMinute('api'), $request->user()?->id ?: $request->ip()));
        RateLimiter::for('staff-write', function (Request $request) use ($perMinute) {
            // Only data-changing requests count; page views and polling are not limited here.
            return $request->isMethodSafe()
                ? Limit::none()
                : $this->limit('staff-write', $perMinute('staff_write'), $request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Per-minute limit that logs when it is hit and answers in the format the caller expects.
     */
    protected function limit(string $name, int $perMinute, string|int|null $key): Limit
    {
        return Limit::perMinute($perMinute)
            ->by($name . '|' . $key)
            ->response(function (Request $request, array $headers) use ($name) {
                SecurityLog::warning('request.rate_limited', ['limiter' => $name]);

                $message = 'Too many requests. Please wait a minute and try again.';

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['success' => false, 'message' => $message], 429, $headers);
                }

                return back()->with('error', $message)->withErrors(['email' => $message])->withHeaders($headers);
            });
    }
}
