<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Insert permissions
        $permissions = [
            ['name' => 'audit_log.view',   'display_name' => 'Lihat Audit Log',   'group' => 'audit_log', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'audit_log.export',  'display_name' => 'Export Audit Log',  'group' => 'audit_log', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'audit_log.purge',   'display_name' => 'Hapus Audit Log',   'group' => 'audit_log', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($permissions as $perm) {
            // Skip if already exists
            if (DB::table('permissions')->where('name', $perm['name'])->exists()) {
                continue;
            }
            DB::table('permissions')->insert($perm);
        }

        // Assign to administrator / admin roles
        $adminRoles = DB::table('roles')
            ->whereIn('name', ['administrator', 'admin', 'superadmin', 'super_admin'])
            ->pluck('id');

        $permissionIds = DB::table('permissions')
            ->whereIn('name', ['audit_log.view', 'audit_log.export', 'audit_log.purge'])
            ->pluck('id');

        foreach ($adminRoles as $roleId) {
            foreach ($permissionIds as $permId) {
                // Skip if already assigned
                $exists = DB::table('role_permission')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permission')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', ['audit_log.view', 'audit_log.export', 'audit_log.purge'])
            ->pluck('id');

        DB::table('role_permission')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', ['audit_log.view', 'audit_log.export', 'audit_log.purge'])->delete();
    }
};
