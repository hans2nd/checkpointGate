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
            $table->enum('type_of_load', ['Full', 'Mix', 'Cross Dock'])->nullable()->after('aktivitas');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('set null')->after('type_of_load');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropForeign(['product_category_id']);
            $table->dropColumn('product_category_id');
            $table->dropColumn('type_of_load');
        });
    }
};
