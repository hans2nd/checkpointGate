<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('no_polisi', 20)->unique();
            $table->string('driver', 100);
            $table->string('vendor', 100);
            $table->enum('tipe', ['INTERNAL', 'EKSTERNAL'])->default('EKSTERNAL');
            $table->string('jenis_kendaraan', 50);
            $table->timestamps();

            $table->index('no_polisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
