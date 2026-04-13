<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:admin,operator')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !$request->user()->role) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        if (!in_array($request->user()->role->name, $roles)) {
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }

        return $next($request);
    }
}
