<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Removes HTML tags from every string input of the request.
 *
 * Used on public, plain-text forms (e.g. the LMI survey) whose values are later
 * shown inside the admin pages, so a visitor cannot plant script/HTML there.
 * Rich-text editor fields must not go through this middleware.
 */
class StripHtmlInput
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->merge($this->clean($request->except(['_token', '_method'])));

        return $next($request);
    }

    protected function clean(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->clean($value);
            } elseif (is_string($value) && preg_match('/<[a-zA-Z!\/?]/', $value)) {
                // Only touch values that contain markup, so text like "< 3 months" is kept.
                $input[$key] = trim(strip_tags($value));
            }
        }

        return $input;
    }
}
