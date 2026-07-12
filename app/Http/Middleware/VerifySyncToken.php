<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates a Bearer token for VPS sync endpoints.
 *
 * Usage: middleware('sync_token')
 *
 * Accepts the token via:
 *   - Header: Authorization: Bearer {token}
 */
class VerifySyncToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('services.vps_sync.token');

        if (empty($configuredToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Sync token belum dikonfigurasi di server. Hubungi administrator.',
            ], 500);
        }

        $providedToken = $request->bearerToken();

        if (!$providedToken || !hash_equals($configuredToken, $providedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Sync token tidak valid atau tidak diberikan.',
            ], 401);
        }

        return $next($request);
    }
}
