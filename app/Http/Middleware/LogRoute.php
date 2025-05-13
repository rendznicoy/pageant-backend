<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('Route matched', [
            'path' => $request->path(),
            'route' => $request->route()?->getName(),
            'controller' => $request->route()?->getAction('controller'),
            'parameters' => $request->route()?->parameters(),
        ]);
        return $next($request);
    }
}
