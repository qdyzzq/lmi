<?php

namespace App\Support;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Turns a caught exception into a message that is safe to show to users.
 *
 * Internal details (SQL, file paths, stack info) are written to the log with a
 * short reference code; the user only sees the reference. With APP_DEBUG=true
 * (local development) the real message is still shown.
 */
class SafeError
{
    public static function message(Throwable $e): string
    {
        // These carry messages written for the user, so they are safe to show.
        if ($e instanceof ValidationException) {
            return $e->getMessage();
        }

        if ($e instanceof ModelNotFoundException) {
            return 'The requested record was not found.';
        }

        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            return $e->getMessage();
        }

        if (config('app.debug')) {
            return $e->getMessage();
        }

        $reference = strtoupper(Str::random(8));

        report(new \RuntimeException('[Ref ' . $reference . '] ' . $e->getMessage(), 0, $e));

        return 'An unexpected error occurred. Please try again or contact the administrator (Ref: ' . $reference . ').';
    }
}
