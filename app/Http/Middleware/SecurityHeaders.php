<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTPS enforcement plus standard browser security headers on every response.
 *
 * - Redirects plain-http requests to https when security.force_https is on
 *   (local hosts such as localhost/127.0.0.1 are never redirected).
 * - Sends HSTS over https so browsers refuse to downgrade afterwards.
 */
class SecurityHeaders
{
    protected const LOCAL_HOSTS = ['localhost', '127.0.0.1', '::1'];

    public function handle(Request $request, Closure $next): Response
    {
        $enforceHttps = config('security.force_https') && ! in_array($request->getHost(), self::LOCAL_HOSTS, true);

        // Behind a TLS-terminating proxy (production has nginx in front) the app may see
        // plain http even when TRUSTED_PROXIES is not set. Honouring the proxy's
        // X-Forwarded-Proto here prevents a redirect loop; a client faking this header
        // only skips its own redirect, and HSTS is ignored by browsers over plain http.
        $isHttps = $request->isSecure() || strtolower((string) $request->headers->get('X-Forwarded-Proto')) === 'https';

        if ($enforceHttps && ! $isHttps) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->remove('X-Powered-By');

        if ($isHttps) {
            $headers->set('Strict-Transport-Security', 'max-age=' . config('security.hsts_max_age', 31536000) . '; includeSubDomains');
        }

        return $response;
    }
}
