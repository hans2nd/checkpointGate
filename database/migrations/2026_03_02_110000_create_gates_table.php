<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_gate');
            $table->enum('jenis_barang', ['FROZEN', 'DRY', 'CHILLED']);
            $table->string('jenis_kendaraan', 50);
            $table->enum('aktivitas', ['INBOUND', 'OUTBOUND']);
            $table->timestamps();

            $table->index(['jenis_barang', 'jenis_kendaraan', 'aktivitas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gates');
    }
};
