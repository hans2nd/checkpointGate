<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'administrator' => ['email' => 'admin@checkpoint.com', 'name' => 'Admin GIIC'],
            'supervisor' => ['email' => 'supervisor@checkpoint.com', 'name' => 'Supervisor GIIC'],
            'staff_admin' => ['email' => 'staffadmin@checkpoint.com', 'name' => 'Staff Admin GIIC'],
            'operator' => ['email' => 'operator@checkpoint.com', 'name' => 'Operator Gate'],
            'security' => ['email' => 'security@checkpoint.com', 'name' => 'Security Guard'],
        ];

        foreach ($roles as $roleName => $userData) {
            $role = \App\Models\Role::where('name', $roleName)->first();
            if ($role) {
                \App\Models\User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => bcrypt('password'),
                        'role_id' => $role->id,
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
