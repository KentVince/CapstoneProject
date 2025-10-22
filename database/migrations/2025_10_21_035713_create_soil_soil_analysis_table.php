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
        Schema::create('soil_analysis', function (Blueprint $table) {
            $table->id(); // Primary Key

            // 🔹 Farmer & Farm Linkage
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->string('farm_name')->nullable();

            // 🔹 General Farm/Soil Info
            $table->string('soil_type')->nullable();
            $table->string('crop_variety')->default('Coffee');
            $table->date('date_collected')->nullable();
            $table->string('location')->nullable();

            // 🔹 Laboratory Information
            $table->string('ref_no')->nullable();
            $table->string('submitted_by')->nullable();
            $table->date('date_submitted')->nullable();
            $table->date('date_analyzed')->nullable();
            $table->string('lab_no')->nullable();
            $table->string('field_no')->nullable();

            // 🔹 Soil Test Results
            $table->decimal('ph_level', 5, 2)->nullable();
            $table->decimal('nitrogen', 8, 2)->nullable();     // %
            $table->decimal('phosphorus', 8, 2)->nullable();   // ppm
            $table->decimal('potassium', 8, 2)->nullable();    // ppm
            $table->decimal('organic_matter', 8, 2)->nullable(); // %

            // 🔹 Recommendation / Analysis Summary
            $table->text('recommendation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_analysis'); // ✅ fix: correct table name
    }
};
