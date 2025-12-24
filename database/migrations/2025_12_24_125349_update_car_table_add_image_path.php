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
        Schema::table('car', function (Blueprint $table) {
            // 在 car_mileage 后面加一个 image_path 字段
            $table->string('image_path')
                  ->nullable()
                  ->after('car_mileage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car', function (Blueprint $table) {
            // 回滚时，删掉 image_path 字段
            $table->dropColumn('image_path');
        });
    }
};