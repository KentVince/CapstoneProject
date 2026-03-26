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
        Schema::create('pest_and_disease', function (Blueprint $table) {
            $table->id('case_id'); // Primary key
            $table->unsignedBigInteger('farmer_id'); // Foreign key to farmers table
            $table->unsignedBigInteger('farm_id');   // Foreign key to farm table
            $table->date('date_detected');
            $table->string('type'); // Type of pest or disease
            $table->string('name'); // Name of pest or disease
            $table->string('severity');
            $table->string('image_url')->nullable(); // Allow null for optional image
            $table->string('diagnosis_result');
            $table->string('recommended_treatment');
            $table->string('treatment_status');
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
            // $table->foreign('farm_id')->references('id')->on('farm')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pest_and_disease');
    }
};
