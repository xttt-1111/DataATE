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
        Schema::create('car', function (Blueprint $table) {
            $table->string('plate_no')->primary();
            $table->string('model');
            $table->decimal('price_hour', 8, 2); // max 999,999.99 per hour
            $table->boolean('availability_status')->default(true);
            $table->integer('fuel_level')->default(100); // 0-100
            $table->decimal('car_mileage', 10, 2)->default(0); // total mileage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car');
    }
};
