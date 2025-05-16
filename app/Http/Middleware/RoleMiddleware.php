<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();
        if (!$user) {
            // Skip logging for api/v1/user to reduce noise
            if ($request->path() !== 'api/v1/user') {
                Log::error("No authenticated user in RoleMiddleware", [
                    'path' => $request->path(),
                    'roles' => $roles,
                    'auth_check' => auth()->check(),
                ]);
            }
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info("RoleMiddleware executed", [
            'path' => $request->path(),
            'roles' => $roles,
            'user_role' => $user->role,
            'user_id' => $user->user_id,
            'auth_check' => auth()->check(),
        ]);

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
