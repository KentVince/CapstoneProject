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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->string('farm_name')->nullable();
            $table->string('farmer_address_bgy')->nullable();
            $table->string('farmer_address_mun')->nullable();
            $table->string('farmer_address_prv')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longtitude')->nullable();
            $table->string('crop_name')->nullable();
            $table->string('crop_variety')->nullable();
            $table->decimal('crop_area', 10, 2)->nullable();
            $table->string('soil_type')->nullable();
            $table->decimal('verified_area', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
