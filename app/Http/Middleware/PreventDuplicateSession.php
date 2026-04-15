<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PreventDuplicateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId           = Auth::id();
            $currentSessionId = session()->getId();
            $storedSessionId  = Cache::get('user_session_' . $userId);

            // If another session exists and it's not this one, log out
            if ($storedSessionId && $storedSessionId !== $currentSessionId) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your account was logged in from another device. Please log in again.',
                ]);
            }
        }

        return $next($request);
    }
}