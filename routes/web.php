<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LiveMonitoringController;
use App\Http\Controllers\UserManagementController;

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
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/api/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');
    Route::get('/live-monitoring', [LiveMonitoringController::class, 'index'])->name('livemonitoring');
    Route::get('/api/live-monitoring', [LiveMonitoringController::class, 'data'])->name('livemonitoring.data');
    Route::get('/live-monitoring-2', [\App\Http\Controllers\LiveMonitoring2Controller::class, 'index'])->name('livemonitoring2')->middleware('permission:livemonitoring2.view');
    Route::get('/api/live-monitoring-2', [\App\Http\Controllers\LiveMonitoring2Controller::class, 'data'])->name('livemonitoring2.data')->middleware('permission:livemonitoring2.view');
    // Bulk delete routes (protected by delete permissions)
    Route::post('checkpoints/bulk-delete', [CheckpointController::class, 'bulkDelete'])
         ->name('checkpoints.bulk-delete')->middleware('permission:checkpoint.delete');
    Route::post('gates/bulk-delete', [GateController::class, 'bulkDelete'])
         ->name('gates.bulk-delete')->middleware('permission:checkpoint.delete');
    Route::post('vehicles/bulk-delete', [VehicleController::class, 'bulkDelete'])
         ->name('vehicles.bulk-delete')->middleware('permission:vehicle.manage');

    // Export routes (protected by view permissions)
    Route::get('checkpoints/export', [CheckpointController::class, 'export'])->name('checkpoints.export')->middleware('permission:checkpoint.export');
    Route::get('gates/export', [GateController::class, 'export'])->name('gates.export')->middleware('permission:checkpoint.view');
    Route::get('vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export')->middleware('permission:vehicle.view');

    // Import routes (protected by create permissions)
    Route::post('checkpoints/import', [CheckpointController::class, 'import'])->name('checkpoints.import')->middleware('permission:checkpoint.import');
    Route::post('gates/import', [GateController::class, 'import'])->name('gates.import')->middleware('permission:checkpoint.create');
    Route::post('vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import')->middleware('permission:vehicle.manage');

    // Template download routes
    Route::get('checkpoints/template', [CheckpointController::class, 'downloadTemplate'])->name('checkpoints.template');
    Route::get('gates/template', [GateController::class, 'downloadTemplate'])->name('gates.template');
    Route::get('vehicles/template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.template');

    // Resources
    // NOTE: To securely protect resource destroy methods, we shouldn't just rely on hiding buttons in blade.
    Route::delete('checkpoints/{checkpoint}', [CheckpointController::class, 'destroy'])->name('checkpoints.destroy')->middleware('permission:checkpoint.delete');
    Route::delete('gates/{gate}', [GateController::class, 'destroy'])->name('gates.destroy')->middleware('permission:checkpoint.delete');
    Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy')->middleware('permission:vehicle.manage');

    Route::resource('checkpoints', CheckpointController::class)->except(['destroy']);
    Route::post('checkpoints/{checkpoint}/trigger-penerimaan', [CheckpointController::class, 'triggerPenerimaan'])->name('checkpoints.trigger-penerimaan')->middleware('permission:checkpoint.trigger');
    Route::post('checkpoints/{checkpoint}/trigger-penyerahan', [CheckpointController::class, 'triggerPenyerahan'])->name('checkpoints.trigger-penyerahan')->middleware('permission:checkpoint.trigger');
    Route::post('checkpoints/{checkpoint}/trigger-start', [CheckpointController::class, 'triggerStart'])->name('checkpoints.trigger-start')->middleware('permission:checkpoint.trigger');
    Route::post('checkpoints/{checkpoint}/trigger-end', [CheckpointController::class, 'triggerEnd'])->name('checkpoints.trigger-end')->middleware('permission:checkpoint.trigger');

    Route::resource('gates', GateController::class)->except(['show', 'destroy']);
    Route::resource('vehicles', VehicleController::class)->except(['show', 'destroy']);
    Route::get('/api/vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
    Route::get('/api/vehicles/lookup', [VehicleController::class, 'getByNoPolisi'])->name('vehicles.lookup');
    Route::get('/api/gates/available', [CheckpointController::class, 'getAvailableGates'])->name('gates.available');

    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('users/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::get('users/export', [UserManagementController::class, 'export'])->name('users.export');
        Route::post('users/import', [UserManagementController::class, 'import'])->name('users.import');
        Route::get('users/template', [UserManagementController::class, 'downloadTemplate'])->name('users.template');
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserManagementController::class)->except(['show']);

        // Role Management
        Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['show']);

        // Permission Management
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->except(['show']);
    });
});
