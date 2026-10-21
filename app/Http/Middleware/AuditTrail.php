<?php

namespace App\Http\Middleware;

use App\Support\SecurityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records every data-changing request made by a logged-in user
 * (who changed what, when, from where, and whether it succeeded)
 * in the security log. Field values are not logged, only field names.
 */
class AuditTrail
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && ! $request->isMethodSafe()) {
            $name = $request->route()?->getName();

            SecurityLog::info('audit.change', [
                'role'   => $request->user()->role,
                // Unnamed routes get an auto-generated name, which says nothing; use the URI instead.
                'route'  => $name && ! str_contains($name, 'generated::') ? $name : $request->route()?->uri(),
                'params' => collect($request->route()?->parameters() ?? [])
                    ->map(fn ($value) => is_scalar($value) ? $value : (is_object($value) && method_exists($value, 'getKey') ? $value->getKey() : null))
                    ->all(),
                'fields' => array_values(array_diff(array_keys($request->except(['_token', '_method'])), ['password', 'password_confirmation', 'current_password'])),
                'status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}
