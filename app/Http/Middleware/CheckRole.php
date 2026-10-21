<?php

namespace App\Http\Middleware;

use App\Support\SecurityLog;
use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401)
                : redirect('login');
        }

        // Check if user has any of the specified roles
        if (!in_array($request->user()->role, $roles)) {
            SecurityLog::warning('access.forbidden', ['role' => $request->user()->role, 'required' => $roles]);
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
