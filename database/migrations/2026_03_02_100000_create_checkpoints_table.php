<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_polisi', 20);
            $table->string('vendor', 100);
            $table->string('driver', 100);
            $table->enum('tipe', ['INTERNAL', 'EKSTERNAL'])->default('EKSTERNAL');
            $table->string('jenis_kendaraan', 50)->nullable();
            $table->dateTime('waktu_penerimaan_dokumen')->nullable();
            $table->dateTime('waktu_penyerahan_dokumen')->nullable();
            $table->enum('jenis_barang', ['FROZEN', 'DRY', 'CHILLED'])->default('FROZEN');
            $table->enum('aktivitas', ['INBOUND', 'OUTBOUND'])->default('INBOUND');
            $table->integer('gate')->nullable();
            $table->enum('status', ['START', 'FINISH'])->default('START');
            $table->dateTime('waktu_start')->nullable();
            $table->dateTime('waktu_end')->nullable();
            $table->string('durasi', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
