<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('login');
        }

        // Check if user has any of the specified roles.
        // access_role (not role) so this works for both office-hierarchy
        // accounts (JPO, Peso_City, Peso_Municipality, Peso_Province, FO, RO
        // — where the raw `role` column is NULL) and non-office accounts
        // (super_admin, admin, statistician — where `role` is set directly).
        if (!in_array($request->user()->access_role, $roles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}