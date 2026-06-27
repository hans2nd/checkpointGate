<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $this->syncPermissions();
        $query = Permission::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }

        if ($group = $request->input('group')) {
            $query->where('group', $group);
        }

        $permissions = $query->orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();

        return view('permissions.index', compact('permissions', 'groups'));
    }

    public function create()
    {
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();
        return view('permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'resource' => 'required|string|max:50|regex:/^[a-z_]+$/',
            'resource_name' => 'required|string|max:100',
            'group' => 'required|string|max:50',
            'actions' => 'required|array',
        ]);

        $actionDisplayNames = [
            'view' => 'Lihat',
            'index' => 'View',
            'create' => 'Tambah',
            'edit' => 'Edit',
            'delete' => 'Hapus',
            'export' => 'Export',
            'import' => 'Import',
            'approve' => 'Approve',
            'trigger' => 'Trigger',
            'trigger_start' => 'Trigger Start',
            'trigger_end' => 'Trigger End',
            'trigger_terima' => 'Trigger Terima',
            'assign_gate' => 'Assign Gate',
        ];

        $createdCount = 0;
        foreach ($request->actions as $action) {
            $permissionName = $request->resource . '.' . $action;
            $actionName = $actionDisplayNames[$action] ?? ucfirst($action);
            $displayName = $actionName . ' ' . $request->resource_name;

            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => $displayName,
                    'group' => $request->group
                ]
            );
            
            if ($permission->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        return redirect()->route('permissions.index')
                            ->with('success', "Berhasil men-generate {$createdCount} permission baru untuk modul '{$request->resource_name}'.");
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();
        return view('permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|regex:/^[a-z_.]+$/|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:150',
            'group' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')
                         ->with('success', "Permission '{$validated['display_name']}' berhasil diperbarui.");
    }

    public function destroy(Permission $permission)
    {
        // Detach from all roles first
        $permission->roles()->detach();
        $permission->delete();

        return redirect()->route('permissions.index')
                         ->with('success', "Permission berhasil dihapus.");
    }

    /**
     * Memindai route sistem untuk mencari middleware 'permission:...'
     * dan otomatis menyimpannya ke database jika belum ada.
     */
    private function syncPermissions()
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        foreach ($routes as $route) {
            $middlewares = $route->middleware();
            if (!is_array($middlewares)) continue;
            
            foreach ($middlewares as $mw) {
                if (str_starts_with($mw, 'permission:')) {
                    $permissionName = substr($mw, 11);
                    $group = explode('.', $permissionName)[0];
                    $displayName = ucwords(str_replace(['.', '_', '-'], ' ', $permissionName));
                    
                    \App\Models\Permission::firstOrCreate(
                        ['name' => $permissionName],
                        [
                            'display_name' => $displayName,
                            'group' => $group
                        ]
                    );
                }
            }
        }
    }
}
