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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE checkpoints MODIFY COLUMN status ENUM('START', 'PARKING', 'DOC IN', 'ASSIGN GATE', 'WAITING', 'READY', 'ON LOADING', 'FINISH', 'CANCEL', 'COMPLETED') DEFAULT 'PARKING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE checkpoints MODIFY COLUMN status ENUM('START', 'PARKING', 'DOC IN', 'WAITING', 'READY', 'ON LOADING', 'FINISH', 'CANCEL', 'COMPLETED') DEFAULT 'PARKING'");
    }
};
