<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('no_surat_jalan', 100)->nullable()->after('note');
            $table->string('purchase_order', 100)->nullable()->after('no_surat_jalan');
        });
    }

    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn(['no_surat_jalan', 'purchase_order']);
        });
    }
};
