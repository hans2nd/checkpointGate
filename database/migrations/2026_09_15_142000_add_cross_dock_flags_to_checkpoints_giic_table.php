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
        Schema::table('checkpoints_giic', function (Blueprint $table) {
            if (!Schema::hasColumn('checkpoints_giic', 'type_of_load')) {
                $table->enum('type_of_load', ['Full', 'Mix', 'Cross Dock'])->nullable()->after('aktivitas');
            }
            if (!Schema::hasColumn('checkpoints_giic', 'product_category_id')) {
                $table->unsignedBigInteger('product_category_id')->nullable()->after('type_of_load');
            }
            if (!Schema::hasColumn('checkpoints_giic', 'shipping_type')) {
                $table->string('shipping_type')->nullable()->after('product_category_id');
            }
            if (!Schema::hasColumn('checkpoints_giic', 'is_cross_dock')) {
                $table->boolean('is_cross_dock')->default(false)->after('shipping_type');
            }
            if (!Schema::hasColumn('checkpoints_giic', 'is_generated_cross_dock')) {
                $table->boolean('is_generated_cross_dock')->default(false)->after('is_cross_dock');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints_giic', function (Blueprint $table) {
            if (Schema::hasColumn('checkpoints_giic', 'type_of_load')) {
                $table->dropColumn('type_of_load');
            }
            if (Schema::hasColumn('checkpoints_giic', 'product_category_id')) {
                $table->dropColumn('product_category_id');
            }
            if (Schema::hasColumn('checkpoints_giic', 'shipping_type')) {
                $table->dropColumn('shipping_type');
            }
            if (Schema::hasColumn('checkpoints_giic', 'is_cross_dock')) {
                $table->dropColumn('is_cross_dock');
            }
            if (Schema::hasColumn('checkpoints_giic', 'is_generated_cross_dock')) {
                $table->dropColumn('is_generated_cross_dock');
            }
        });
    }
};
