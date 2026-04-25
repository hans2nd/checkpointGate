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
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('cancel_status')->nullable()->after('canceled_by'); // 'pending', 'approved', 'rejected'
            $table->text('cancel_reason')->nullable()->after('cancel_status');
            $table->foreignId('cancel_requested_by')->nullable()->constrained('users')->nullOnDelete()->after('cancel_reason');
            $table->foreignId('cancel_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('cancel_requested_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropForeign(['cancel_requested_by']);
            $table->dropForeign(['cancel_approved_by']);
            $table->dropColumn(['cancel_status', 'cancel_reason', 'cancel_requested_by', 'cancel_approved_by']);
        });
    }
};
