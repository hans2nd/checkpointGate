<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed initial data from existing Vehicle::JENIS_KENDARAAN
        $types = ['L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG', 'FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT'];
        foreach ($types as $type) {
            \DB::table('vehicle_types')->insert([
                'name' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
