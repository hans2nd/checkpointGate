<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\LiveMonitoringController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/artisan-run', function () {
    return view('artisan-run');
});

Route::post('/artisan-run', function (\Illuminate\Http\Request $request) {
    $command = $request->input('command');
    $output = '';

    try {
        switch ($command) {
            case 'migrate':
                Artisan::call('migrate', ['--force' => true]);
                $output = Artisan::output();
                break;
            case 'migrate:fresh':
                Artisan::call('migrate:fresh', ['--force' => true]);
                $output = Artisan::output();
                break;
            case 'db:seed':
                Artisan::call('db:seed', ['--force' => true]);
                $output = Artisan::output();
                break;
            case 'storage:link':
                Artisan::call('storage:link');
                $output = Artisan::output();
                break;
            case 'cache:clear':
                Artisan::call('cache:clear');
                $output = Artisan::output();
                break;
            case 'optimize:clear':
                Artisan::call('optimize:clear');
                $output = Artisan::output();
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Unknown command: ' . $command]);
        }
        return response()->json(['success' => true, 'output' => $output]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Public Maintenance Page
Route::get('/maintenance', [\App\Http\Controllers\MaintenanceController::class, 'page'])->name('maintenance.page');

// Language switcher (accessible to all: guest & auth)
Route::post('/locale/switch', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'id');
    if (in_array($locale, config('app.available_locales', ['id', 'en']))) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale.switch');

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->hasPermission('dashboard.view')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasPermission('checkpoint.view')) {
            return redirect()->route('checkpoints.index');
        } elseif ($user->hasPermission('monitoring.view')) {
            return redirect()->route('livemonitoring');
        }
        return redirect()->route('profile.index');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/web-data/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');
    Route::get('/live-monitoring', [LiveMonitoringController::class, 'index'])->name('livemonitoring')->middleware('permission:monitoring.view');
    Route::get('/web-data/live-monitoring', [LiveMonitoringController::class, 'data'])->name('livemonitoring.data');
    Route::get('/web-data/pending-gates', [CheckpointController::class, 'pendingConfirmGates'])->name('pending.gates');
    // Bulk delete routes (protected by delete permissions)
    Route::post('checkpoints/bulk-delete', [CheckpointController::class, 'bulkDelete'])
        ->name('checkpoints.bulk-delete')->middleware('permission:checkpoint.delete');
    Route::post('gates/bulk-delete', [GateController::class, 'bulkDelete'])
        ->name('gates.bulk-delete')->middleware('permission:checkpoint.delete');
    Route::post('vehicles/bulk-delete', [VehicleController::class, 'bulkDelete'])
        ->name('vehicles.bulk-delete')->middleware('permission:vehicle.delete');

    // Export routes (protected by view permissions)
    Route::get('checkpoints/export', [CheckpointController::class, 'export'])->name('checkpoints.export')->middleware('permission:checkpoint.export');
    Route::get('gates/export', [GateController::class, 'export'])->name('gates.export')->middleware('permission:checkpoint.view');
    Route::get('vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export')->middleware('permission:vehicle.view');

    // Import routes (protected by create permissions)
    Route::post('checkpoints/import', [CheckpointController::class, 'import'])->name('checkpoints.import')->middleware('permission:checkpoint.import');
    Route::post('checkpoints/extract-surat-jalan', [CheckpointController::class, 'extractSuratJalan'])->name('checkpoints.extract-sj');
    Route::post('gates/import', [GateController::class, 'import'])->name('gates.import')->middleware('permission:checkpoint.create');
    Route::post('vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import')->middleware('permission:vehicle.create');

    // Template download routes
    Route::get('checkpoints/template', [CheckpointController::class, 'downloadTemplate'])->name('checkpoints.template');
    Route::get('gates/template', [GateController::class, 'downloadTemplate'])->name('gates.template');
    Route::get('vehicles/template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.template');

    // Resources
    // NOTE: To securely protect resource destroy methods, we shouldn't just rely on hiding buttons in blade.
    Route::delete('checkpoints/{checkpoint}', [CheckpointController::class, 'destroy'])->name('checkpoints.destroy')->middleware('permission:checkpoint.delete');
    Route::delete('gates/{gate}', [GateController::class, 'destroy'])->name('gates.destroy')->middleware('permission:checkpoint.delete');
    Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy')->middleware('permission:vehicle.delete');

    Route::resource('checkpoints', CheckpointController::class)->except(['destroy']);
    Route::post('checkpoints/{checkpoint}/trigger-penerimaan', [CheckpointController::class, 'triggerPenerimaan'])->name('checkpoints.trigger-penerimaan')->middleware('permission:checkpoint.trigger_terima');
    Route::post('checkpoints/{checkpoint}/assign-gate', [CheckpointController::class, 'assignGate'])->name('checkpoints.assign-gate')->middleware('permission:checkpoint.assign_gate');
    Route::post('checkpoints/{checkpoint}/confirm-gate', [CheckpointController::class, 'triggerConfirmGate'])->name('checkpoints.confirm-gate')->middleware('permission:checkpoint.trigger_terima');
    Route::post('checkpoints/{checkpoint}/trigger-penyerahan', [CheckpointController::class, 'triggerPenyerahan'])->name('checkpoints.trigger-penyerahan')->middleware('permission:checkpoint.trigger_serah');
    Route::post('checkpoints/{checkpoint}/trigger-start', [CheckpointController::class, 'triggerStart'])->name('checkpoints.trigger-start')->middleware('permission:checkpoint.trigger_start');
    Route::post('checkpoints/{checkpoint}/trigger-end', [CheckpointController::class, 'triggerEnd'])->name('checkpoints.trigger-end')->middleware('permission:checkpoint.trigger_end');
    Route::post('checkpoints/{checkpoint}/cancel', [CheckpointController::class, 'cancel'])->name('checkpoints.cancel')->middleware('permission:checkpoint.request_cancel');
    Route::post('checkpoints/{checkpoint}/approve-cancel', [CheckpointController::class, 'approveCancel'])->name('checkpoints.approve-cancel')->middleware('permission:checkpoint.approve_cancel');
    Route::post('checkpoints/{checkpoint}/reject-cancel', [CheckpointController::class, 'rejectCancel'])->name('checkpoints.reject-cancel')->middleware('permission:checkpoint.approve_cancel');

    Route::resource('gates', GateController::class)->except(['show', 'destroy']);
    Route::resource('vehicles', VehicleController::class)->except(['show', 'destroy']);
    Route::get('/web-data/vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
    Route::get('/web-data/vehicles/lookup', [VehicleController::class, 'getByNoPolisi'])->name('vehicles.lookup');
    Route::get('/web-data/gates/available', [CheckpointController::class, 'getAvailableGates'])->name('gates.available');

    // Vehicle Types
    Route::resource('vehicle-types', VehicleTypeController::class)->except(['show']);
    Route::get('/web-data/vehicle-types', [VehicleTypeController::class, 'apiList'])->name('vehicle-types.api');

    // Product Categories
    Route::get('product-categories/template', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'template'])->name('product-categories.template');
    Route::get('product-categories/export', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'export'])->name('product-categories.export');
    Route::post('product-categories/import', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'import'])->name('product-categories.import');
    Route::resource('product-categories', \App\Http\Controllers\Admin\ProductCategoryController::class)->except(['show']);

    // Report
    Route::get('report', [ReportController::class, 'index'])->name('report.index')->middleware('permission:report.view');
    Route::get('report/export', [ReportController::class, 'export'])->name('report.export')->middleware('permission:report.export');

    // User Management
    Route::group(['middleware' => 'permission:user.view'], function () {
        Route::post('users/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('users.bulk-delete')->middleware('permission:user.delete');
        Route::get('users/export', [UserManagementController::class, 'export'])->name('users.export');
        Route::post('users/import', [UserManagementController::class, 'import'])->name('users.import')->middleware('permission:user.create');
        Route::get('users/template', [UserManagementController::class, 'downloadTemplate'])->name('users.template');
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:user.edit');
        Route::resource('users', UserManagementController::class)->except(['show']);
    });

    // Employee Management
    Route::group(['middleware' => 'permission:employee.view'], function () {
        Route::post('employees/bulk-delete', [\App\Http\Controllers\EmployeeController::class, 'bulkDelete'])->name('employees.bulk-delete')->middleware('permission:employee.delete');
        Route::get('employees/export', [\App\Http\Controllers\EmployeeController::class, 'export'])->name('employees.export');
        Route::post('employees/import', [\App\Http\Controllers\EmployeeController::class, 'import'])->name('employees.import')->middleware('permission:employee.create');
        Route::get('employees/template', [\App\Http\Controllers\EmployeeController::class, 'downloadTemplate'])->name('employees.template');
        Route::post('employees/{employee}/toggle-status', [\App\Http\Controllers\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status')->middleware('permission:employee.edit');
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class)->except(['show']);
    });

    // Role Management
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['show'])->middleware('permission:role.view');

    // Permission Management
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->except(['show'])->middleware('permission:permission.view');

    // Maintenance Settings
    Route::get('settings/maintenance', [\App\Http\Controllers\MaintenanceController::class, 'index'])->name('maintenance.index')->middleware('permission:maintenance.view');
    Route::post('settings/maintenance/toggle', [\App\Http\Controllers\MaintenanceController::class, 'toggle'])->name('maintenance.toggle')->middleware('permission:maintenance.toggle');

    // Audit Log
    Route::group(['middleware' => 'permission:audit_log.view'], function () {
        Route::get('audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/export', [\App\Http\Controllers\AuditLogController::class, 'export'])->name('audit-logs.export')->middleware('permission:audit_log.export');
        Route::post('audit-logs/purge', [\App\Http\Controllers\AuditLogController::class, 'purge'])->name('audit-logs.purge')->middleware('permission:audit_log.purge');
    });

});
