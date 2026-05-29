<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika mode maintenance aktif
        if (Cache::get('app_maintenance', false)) {
            // Determine user based on request type
            $user = null;
            if ($request->expectsJson() || $request->is('api/*')) {
                $user = Auth::guard('sanctum')->user();
            } else {
                $user = Auth::guard('web')->user();
            }
            
            // Jika user sedang login dan BUKAN admin, logout & redirect/return error
            if ($user && !$user->isAdmin()) {
                // Handle API request
                if ($request->expectsJson() || $request->is('api/*')) {
                    if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                        $user->currentAccessToken()->delete();
                    }
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated.'
                    ], 401);
                }

                // Handle Web request
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('maintenance.page');
            }
        }

        return $next($request);
    }
}
