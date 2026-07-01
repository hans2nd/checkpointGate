<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates a static API key for external integrations (e.g. PowerBI).
 *
 * Usage: middleware('api_key')
 *
 * Accepts the key via:
 *   - Header: X-API-Key: {key}
 *   - Query param: ?api_key={key}
 */
class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.powerbi.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API key belum dikonfigurasi di server. Hubungi administrator.',
            ], 500);
        }

        $providedKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$providedKey || !hash_equals($apiKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak valid atau tidak diberikan.',
            ], 401);
        }

        return $next($request);
    }
}
