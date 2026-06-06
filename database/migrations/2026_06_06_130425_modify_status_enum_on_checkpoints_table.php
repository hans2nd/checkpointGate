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
        // Ganti status START menjadi READY (atau gunakan yang baru).
        // Kita masukkan semua: PARKING, DOC IN, WAITING, READY, ON LOADING, FINISH, CANCEL, COMPLETED, START
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE checkpoints MODIFY COLUMN status ENUM('START', 'PARKING', 'DOC IN', 'WAITING', 'READY', 'ON LOADING', 'FINISH', 'CANCEL', 'COMPLETED') DEFAULT 'PARKING'");
        
        // Opsional: Migrasi data lama dari START ke PARKING
        \Illuminate\Support\Facades\DB::statement("UPDATE checkpoints SET status = 'PARKING' WHERE status = 'START'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum sebelumnya
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE checkpoints MODIFY COLUMN status ENUM('START', 'FINISH', 'ON LOADING', 'CANCEL', 'COMPLETED') DEFAULT 'START'");
    }
};
