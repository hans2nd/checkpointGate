<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LiveMonitoringController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');
    Route::get('/live-monitoring', [LiveMonitoringController::class, 'index'])->name('livemonitoring');
    Route::get('/api/live-monitoring', [LiveMonitoringController::class, 'data'])->name('livemonitoring.data');
    // Bulk delete routes
    Route::post('checkpoints/bulk-delete', [CheckpointController::class, 'bulkDelete'])->name('checkpoints.bulk-delete');
    Route::post('gates/bulk-delete', [GateController::class, 'bulkDelete'])->name('gates.bulk-delete');
    Route::post('vehicles/bulk-delete', [VehicleController::class, 'bulkDelete'])->name('vehicles.bulk-delete');

    // Export routes
    Route::get('checkpoints/export', [CheckpointController::class, 'export'])->name('checkpoints.export');
    Route::get('gates/export', [GateController::class, 'export'])->name('gates.export');
    Route::get('vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');

    // Import routes
    Route::post('checkpoints/import', [CheckpointController::class, 'import'])->name('checkpoints.import');
    Route::post('gates/import', [GateController::class, 'import'])->name('gates.import');
    Route::post('vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');

    // Template download routes
    Route::get('checkpoints/template', [CheckpointController::class, 'downloadTemplate'])->name('checkpoints.template');
    Route::get('gates/template', [GateController::class, 'downloadTemplate'])->name('gates.template');
    Route::get('vehicles/template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.template');

    Route::resource('checkpoints', CheckpointController::class);
    Route::post('checkpoints/{checkpoint}/trigger-penerimaan', [CheckpointController::class, 'triggerPenerimaan'])->name('checkpoints.trigger-penerimaan');
    Route::post('checkpoints/{checkpoint}/trigger-penyerahan', [CheckpointController::class, 'triggerPenyerahan'])->name('checkpoints.trigger-penyerahan');
    Route::post('checkpoints/{checkpoint}/trigger-start', [CheckpointController::class, 'triggerStart'])->name('checkpoints.trigger-start');
    Route::post('checkpoints/{checkpoint}/trigger-end', [CheckpointController::class, 'triggerEnd'])->name('checkpoints.trigger-end');
    Route::resource('gates', GateController::class)->except(['show']);
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::get('/api/vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
    Route::get('/api/vehicles/lookup', [VehicleController::class, 'getByNoPolisi'])->name('vehicles.lookup');
});
