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
        $admin = Role::firstOrCreate(['name' => 'administrator'], [
            'display_name' => 'Administrator',
            'description' => 'Akses penuh ke semua fitur',
        ]);

        $supervisor = Role::firstOrCreate(['name' => 'supervisor_admin'], [
            'display_name' => 'Supervisor Admin',
            'description' => 'Akses operasional dan approve cancel',
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff_admin'], [
            'display_name' => 'Staff Admin',
            'description' => 'Akses tambah dan lihat data checkpoint, tanpa hapus dan approve',
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
            ['name' => 'checkpoint.trigger_terima', 'display_name' => 'Trigger Terima', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.trigger_serah', 'display_name' => 'Trigger Serah', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.request_cancel', 'display_name' => 'Request Cancel', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.approve_cancel', 'display_name' => 'Approve Cancel', 'group' => 'Checkpoint'],
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

            // Report
            ['name' => 'report.view', 'display_name' => 'Lihat Report', 'group' => 'Report'],
            ['name' => 'report.export', 'display_name' => 'Export Report', 'group' => 'Report'],
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

        // Assign permissions to Supervisor Admin
        $supervisorPermissions = Permission::whereNotIn('group', [
            'User Management',
            'Role Management',
            'Permission Management'      // Just in case it's named something else
        ])->whereNotIn('name', [
            'role.manage', 'permission.manage', 'user.view', 'user.manage'
        ])->get();
        $supervisor->permissions()->sync($supervisorPermissions->pluck('id'));

        // Assign permissions to Staff Admin
        $staffPermissions = Permission::whereIn('name', [
            'checkpoint.create',
            'checkpoint.export',
            'checkpoint.view',
            'checkpoint.trigger_terima',
            'checkpoint.trigger_serah',
            'checkpoint.request_cancel',
            'report.view',
            'report.export',
        ])->get();
        $staff->permissions()->sync($staffPermissions->pluck('id'));

        // Assign roles to existing user admin
        $adminUser = User::where('email', 'admin@checkpoint.com')->first();
        if ($adminUser) {
            $adminUser->update([
                'role_id' => $admin->id,
            ]);
        }

        // Delete other sample users
        User::where('email', '!=', 'admin@checkpoint.com')->delete();
    }
}
