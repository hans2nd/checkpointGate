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
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $operatorRole = \App\Models\Role::where('name', 'operator')->first();
        $viewerRole = \App\Models\Role::where('name', 'viewer')->first();

        if ($adminRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'admin@gmail.com'],
                ['name' => 'Admin GIIC', 'password' => bcrypt('password'), 'role_id' => $adminRole->id, 'is_active' => true]
            );
        }

        if ($operatorRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'operator@gmail.com'],
                ['name' => 'Operator Gate', 'password' => bcrypt('password'), 'role_id' => $operatorRole->id, 'is_active' => true]
            );
        }

        if ($viewerRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'viewer@gmail.com'],
                ['name' => 'Viewer Monitor', 'password' => bcrypt('password'), 'role_id' => $viewerRole->id, 'is_active' => true]
            );
        }
    }
}
