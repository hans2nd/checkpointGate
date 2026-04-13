<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Full access to all features including user management',
        ]);

        $operator = Role::firstOrCreate(['name' => 'operator'], [
            'display_name' => 'Operator',
            'description' => 'Can manage checkpoints, vehicles, and operational features',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer'], [
            'display_name' => 'Viewer',
            'description' => 'Read-only access to dashboard, monitoring, and data',
        ]);

        // Create Permissions
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'Lihat Dashboard', 'group' => 'Dashboard'],

            // Checkpoint
            ['name' => 'checkpoint.view', 'display_name' => 'Lihat Data Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.create', 'display_name' => 'Tambah Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.edit', 'display_name' => 'Edit Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.delete', 'display_name' => 'Hapus Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.trigger', 'display_name' => 'Trigger Aksi Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.export', 'display_name' => 'Export Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.import', 'display_name' => 'Import Checkpoint', 'group' => 'Checkpoint'],

            // Vehicle
            ['name' => 'vehicle.view', 'display_name' => 'Lihat Kendaraan', 'group' => 'Kendaraan'],
            ['name' => 'vehicle.manage', 'display_name' => 'Kelola Kendaraan', 'group' => 'Kendaraan'],

            // Monitoring
            ['name' => 'monitoring.view', 'display_name' => 'Lihat Live Monitoring', 'group' => 'Monitoring'],

            // User Management
            ['name' => 'user.view', 'display_name' => 'Lihat User', 'group' => 'User Management'],
            ['name' => 'user.manage', 'display_name' => 'Kelola User', 'group' => 'User Management'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], [
                'display_name' => $perm['display_name'],
                'group' => $perm['group'],
            ]);
        }

        // Assign permissions to Admin (all permissions)
        $allPermissions = Permission::all();
        $admin->permissions()->sync($allPermissions->pluck('id'));

        // Assign permissions to Operator
        $operatorPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'checkpoint.view', 'checkpoint.create', 'checkpoint.edit', 'checkpoint.delete',
            'checkpoint.trigger', 'checkpoint.export', 'checkpoint.import',
            'vehicle.view', 'vehicle.manage',
            'monitoring.view',
        ])->get();
        $operator->permissions()->sync($operatorPermissions->pluck('id'));

        // Assign permissions to Viewer
        $viewerPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'checkpoint.view', 'checkpoint.export',
            'vehicle.view',
            'monitoring.view',
        ])->get();
        $viewer->permissions()->sync($viewerPermissions->pluck('id'));

        // Assign roles to existing users
        // First user gets admin, rest get operator
        $users = User::whereNull('role_id')->get();
        foreach ($users as $index => $user) {
            $user->update([
                'role_id' => $index === 0 ? $admin->id : $operator->id,
            ]);
        }
    }
}
