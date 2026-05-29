<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Checkpoint GIIC Mobile App
|--------------------------------------------------------------------------
|
| Base URL: /api
| Auth: Laravel Sanctum (Bearer Token)
|
| Usage from Flutter:
|   1. POST /api/login → get token
|   2. Set header: Authorization: Bearer {token}
|   3. Access all protected endpoints
|
*/

// ==========================================
// PUBLIC (No Auth Required)
// ==========================================
Route::post('/login', [ApiController::class, 'login']);

// ==========================================
// PROTECTED (Requires Bearer Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/me', [ApiController::class, 'me']);

    // --- Dashboard ---
    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    Route::get('/chart-data', [ApiController::class, 'chartData']);

    // --- Live Monitoring ---
    Route::get('/live-monitoring', [ApiController::class, 'liveMonitoring']);

    // --- Checkpoints ---
    Route::get('/checkpoints', [ApiController::class, 'checkpoints']);
    Route::get('/checkpoints/{id}', [ApiController::class, 'checkpointShow']);
    Route::post('/checkpoints', [ApiController::class, 'checkpointStore']);

    // --- Triggers & Actions ---
    Route::post('/checkpoints/{id}/assign-gate', [ApiController::class, 'assignGate']);
    Route::post('/checkpoints/{id}/trigger-penerimaan', [ApiController::class, 'triggerPenerimaan']);
    Route::post('/checkpoints/{id}/trigger-start', [ApiController::class, 'triggerStart']);
    Route::post('/checkpoints/{id}/trigger-end', [ApiController::class, 'triggerEnd']);
    Route::post('/checkpoints/{id}/trigger-penyerahan', [ApiController::class, 'triggerPenyerahan']);

    // --- Gates ---
    Route::get('/gates/available', [ApiController::class, 'availableGates']);

    // --- Vehicles ---
    Route::get('/vehicles/search', [ApiController::class, 'vehicleSearch']);
});
