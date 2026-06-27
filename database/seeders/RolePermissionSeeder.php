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

        $supervisor = Role::firstOrCreate(['name' => 'supervisor'], [
            'display_name' => 'Supervisor',
            'description' => 'Akses operasional, master data, report, dan approve cancel',
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff_admin'], [
            'display_name' => 'Staff Admin',
            'description' => 'Akses tambah dan lihat data checkpoint, operasional, dan report',
        ]);

        $operator = Role::firstOrCreate(['name' => 'operator'], [
            'display_name' => 'Operator',
            'description' => 'Akses operasional checkpoint (assign gate, start, end loading)',
        ]);

        $security = Role::firstOrCreate(['name' => 'security'], [
            'display_name' => 'Security',
            'description' => 'Akses input data kendaraan awal dan lihat checkpoint',
        ]);

        // Define Permissions (Granular)
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'Lihat Dashboard', 'group' => 'Dashboard'],

            // Monitoring
            ['name' => 'monitoring.view', 'display_name' => 'Lihat Live Monitoring', 'group' => 'Monitoring'],

            // Checkpoint
            ['name' => 'checkpoint.view', 'display_name' => 'Lihat Data Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.create', 'display_name' => 'Tambah Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.edit', 'display_name' => 'Edit Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.delete', 'display_name' => 'Hapus Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.export', 'display_name' => 'Export Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.import', 'display_name' => 'Import Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.assign_gate', 'display_name' => 'Assign Gate Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.trigger_start', 'display_name' => 'Start Loading Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.trigger_end', 'display_name' => 'End Loading Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.trigger_terima', 'display_name' => 'Terima Dokumen Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.trigger_serah', 'display_name' => 'Serah Dokumen Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.request_cancel', 'display_name' => 'Request Cancel Checkpoint', 'group' => 'Checkpoint'],
            ['name' => 'checkpoint.approve_cancel', 'display_name' => 'Approve Cancel Checkpoint', 'group' => 'Checkpoint'],

            // Report
            ['name' => 'report.view', 'display_name' => 'Lihat Report', 'group' => 'Report'],
            ['name' => 'report.export', 'display_name' => 'Export Report', 'group' => 'Report'],

            // Vehicle (Master)
            ['name' => 'vehicle.view', 'display_name' => 'Lihat Kendaraan', 'group' => 'Master Vehicle'],
            ['name' => 'vehicle.create', 'display_name' => 'Tambah Kendaraan', 'group' => 'Master Vehicle'],
            ['name' => 'vehicle.edit', 'display_name' => 'Edit Kendaraan', 'group' => 'Master Vehicle'],
            ['name' => 'vehicle.delete', 'display_name' => 'Hapus Kendaraan', 'group' => 'Master Vehicle'],

            // Vehicle Type (Master)
            ['name' => 'vehicle_type.view', 'display_name' => 'Lihat Jenis Kendaraan', 'group' => 'Master Vehicle Type'],
            ['name' => 'vehicle_type.create', 'display_name' => 'Tambah Jenis Kendaraan', 'group' => 'Master Vehicle Type'],
            ['name' => 'vehicle_type.edit', 'display_name' => 'Edit Jenis Kendaraan', 'group' => 'Master Vehicle Type'],
            ['name' => 'vehicle_type.delete', 'display_name' => 'Hapus Jenis Kendaraan', 'group' => 'Master Vehicle Type'],

            // Employee (Master)
            ['name' => 'employee.view', 'display_name' => 'Lihat Employee', 'group' => 'Master Employee'],
            ['name' => 'employee.create', 'display_name' => 'Tambah Employee', 'group' => 'Master Employee'],
            ['name' => 'employee.edit', 'display_name' => 'Edit Employee', 'group' => 'Master Employee'],
            ['name' => 'employee.delete', 'display_name' => 'Hapus Employee', 'group' => 'Master Employee'],

            // User Management
            ['name' => 'user.view', 'display_name' => 'Lihat User', 'group' => 'User Management'],
            ['name' => 'user.create', 'display_name' => 'Tambah User', 'group' => 'User Management'],
            ['name' => 'user.edit', 'display_name' => 'Edit User', 'group' => 'User Management'],
            ['name' => 'user.delete', 'display_name' => 'Hapus User', 'group' => 'User Management'],

            // Role Management
            ['name' => 'role.view', 'display_name' => 'Lihat Role', 'group' => 'Role Management'],
            ['name' => 'role.create', 'display_name' => 'Tambah Role', 'group' => 'Role Management'],
            ['name' => 'role.edit', 'display_name' => 'Edit Role', 'group' => 'Role Management'],
            ['name' => 'role.delete', 'display_name' => 'Hapus Role', 'group' => 'Role Management'],

            // Permission Management
            ['name' => 'permission.view', 'display_name' => 'Lihat Permission', 'group' => 'Permission Management'],
            ['name' => 'permission.manage', 'display_name' => 'Kelola Permission', 'group' => 'Permission Management'],

            // Maintenance Mode
            ['name' => 'maintenance.view', 'display_name' => 'Lihat Maintenance Mode', 'group' => 'Maintenance Mode'],
            ['name' => 'maintenance.toggle', 'display_name' => 'Toggle Maintenance Mode', 'group' => 'Maintenance Mode'],

            // Profile
            ['name' => 'profile.view', 'display_name' => 'Lihat Profil', 'group' => 'Profile'],
            ['name' => 'profile.edit', 'display_name' => 'Edit Profil', 'group' => 'Profile'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], [
                'display_name' => $perm['display_name'],
                'group' => $perm['group'],
            ]);
        }

        // Assign permissions to Admin (All permissions)
        $allPermissions = Permission::all();
        $admin->permissions()->sync($allPermissions->pluck('id'));

        // Assign permissions to Supervisor
        $supervisorPermissions = Permission::whereNotIn('group', [
            'User Management',
            'Role Management',
            'Permission Management',
            'Maintenance Mode'
        ])->get();
        $supervisor->permissions()->sync($supervisorPermissions->pluck('id'));

        // Assign permissions to Staff Admin
        $staffPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'monitoring.view',
            'checkpoint.view',
            'checkpoint.create',
            'checkpoint.export',
            'checkpoint.trigger_terima',
            'checkpoint.trigger_serah',
            'checkpoint.request_cancel',
            'report.view',
            'report.export',
            'profile.view',
            'profile.edit',
        ])->get();
        $staff->permissions()->sync($staffPermissions->pluck('id'));

        // Assign permissions to Operator
        $operatorPermissions = Permission::whereIn('name', [
            'checkpoint.view',
            'checkpoint.assign_gate',
            'checkpoint.trigger_start',
            'checkpoint.trigger_end',
            'profile.view',
            'profile.edit',
        ])->get();
        $operator->permissions()->sync($operatorPermissions->pluck('id'));

        // Assign permissions to Security
        $securityPermissions = Permission::whereIn('name', [
            'checkpoint.view',
            'checkpoint.create',
            'profile.view',
            'profile.edit',
        ])->get();
        $security->permissions()->sync($securityPermissions->pluck('id'));

        // Update default admin user if exists
        $adminUser = User::where('email', 'admin@checkpoint.com')->first();
        if ($adminUser) {
            $adminUser->update([
                'role_id' => $admin->id,
            ]);
        }
    }
}
