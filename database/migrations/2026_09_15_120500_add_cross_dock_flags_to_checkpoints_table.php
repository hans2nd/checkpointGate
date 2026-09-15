<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->boolean('is_cross_dock')->default(false)->after('type_of_load');
            $table->boolean('is_generated_cross_dock')->default(false)->after('is_cross_dock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn(['is_cross_dock', 'is_generated_cross_dock']);
        });
    }
};
