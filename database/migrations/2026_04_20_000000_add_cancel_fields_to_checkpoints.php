<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE checkpoints MODIFY COLUMN status ENUM('START', 'FINISH', 'ON LOADING', 'CANCEL') DEFAULT 'START'");

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->text('cancel_note')->nullable()->after('note');
            $table->dateTime('canceled_at')->nullable()->after('cancel_note');
            $table->unsignedBigInteger('canceled_by')->nullable()->after('canceled_at');

            $table->foreign('canceled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropForeign(['canceled_by']);
            $table->dropColumn(['cancel_note', 'canceled_at', 'canceled_by']);
        });

        DB::statement("ALTER TABLE checkpoints MODIFY COLUMN status ENUM('START', 'FINISH', 'ON LOADING') DEFAULT 'START'");
    }
};
