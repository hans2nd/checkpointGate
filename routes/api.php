<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\SyncApiController;

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
    Route::get('/checkpoints/recent-assignments', [ApiController::class, 'recentAssignments']);
    Route::get('/checkpoints', [ApiController::class, 'checkpoints']);
    Route::get('/checkpoints/{id}', [ApiController::class, 'checkpointShow']);
    Route::post('/checkpoints', [ApiController::class, 'checkpointStore']);

    // --- Triggers & Actions ---
    Route::post('/checkpoints/{id}/assign-gate', [ApiController::class, 'assignGate']);
    Route::post('/checkpoints/{id}/confirm-gate', [ApiController::class, 'triggerConfirmGate']);
    Route::post('/checkpoints/{id}/trigger-penerimaan', [ApiController::class, 'triggerPenerimaan']);
    Route::post('/checkpoints/{id}/trigger-start', [ApiController::class, 'triggerStart']);
    Route::post('/checkpoints/{id}/trigger-end', [ApiController::class, 'triggerEnd']);
    Route::post('/checkpoints/{id}/trigger-penyerahan', [ApiController::class, 'triggerPenyerahan']);
    Route::post('/checkpoints/{id}/trigger-completed', [ApiController::class, 'triggerCompleted']);

    // --- Gates ---
    Route::get('/gates/available', [ApiController::class, 'availableGates']);

    // --- Vehicles ---
    Route::get('/vehicles/search', [ApiController::class, 'vehicleSearch']);
});

// ==========================================
// EXTERNAL INTEGRATIONS (API Key Auth)
// ==========================================
Route::middleware('api_key')->group(function () {
    // --- Report (PowerBI) ---
    Route::get('/report', [ReportApiController::class, 'index']);
    Route::get('/report/summary', [ReportApiController::class, 'summary']);
});

// ==========================================
// VPS SYNC (Sync Token Auth)
// ==========================================
Route::middleware('sync_token')->prefix('sync')->group(function () {
    Route::post('/checkpoints', [SyncApiController::class, 'syncCheckpoints']);
    Route::post('/users', [SyncApiController::class, 'syncUsers']);
    Route::post('/employees', [SyncApiController::class, 'syncEmployees']);
});
