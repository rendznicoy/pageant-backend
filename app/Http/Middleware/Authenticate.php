<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            Log::warning("Unauthenticated API request", [
                'path' => $request->path(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'user_id' => auth()->id(),
            ]);
        }
        return null;
    }
}
