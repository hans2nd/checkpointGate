<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('created_at', 'asc')->get();
        
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
                         ->with('success', "Role '{$role->display_name}' berhasil ditambahkan.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|regex:/^[a-z_]+$/|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
                         ->with('success', "Role '{$role->display_name}' berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        // Prevent deleting if users still assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                             ->with('error', "Role '{$role->display_name}' tidak bisa dihapus karena masih ada user yang menggunakan.");
        }

        $role->delete();

        return redirect()->route('roles.index')
                         ->with('success', "Role '{$role->display_name}' berhasil dihapus.");
    }
}
