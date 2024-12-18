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
            $table->case_id();
            $table->unsignedBigInteger('farmer_id'); // Add foreign key column
            $table->unsignedBigInteger('farm_id'); // Add foreign key column
            $table->date('date_detected');
            $table->string('type');
            $table->string('pest_or_disease');
            $table->string('severity');
            $table->string('image_url')->nullable();
            $table->string('diagnosis_result');
            $table->string('recommended_treatment');
            $table->string('treatment_status');
            $table->timestamps();
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
