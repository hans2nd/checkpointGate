<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@checkpoint.com',
            'password' => bcrypt('password'),
        ]);

        // $this->call(CheckpointSeeder::class);
        // $this->call(GateSeeder::class);
        // $this->call(VehicleSeeder::class);
        $this->call(DemoUserSeeder::class);
        $this->call(RolePermissionSeeder::class);
    }
}
