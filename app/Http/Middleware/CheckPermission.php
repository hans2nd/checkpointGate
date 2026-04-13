<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage: middleware('permission:user.manage')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user() || !$request->user()->hasPermission($permission)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Tidak ada otoritas untuk aksi ini.'], 403);
            }
            
            $fallback = url()->previous() !== url()->current() ? url()->previous() : route('dashboard');
            return redirect()->to($fallback)->with('error', 'Tidak ada otoritas untuk aksi/fitur tersebut.');
        }

        return $next($request);
    }
}
