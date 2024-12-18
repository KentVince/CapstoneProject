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
            $table->soil_test_id();
            $table->unsignedBigInteger('farmer_id'); // Add foreign key column
            $table->unsignedBigInteger('farm_id'); // Add foreign key column
            $table->date('date_tested');
            $table->string('pH_level');
            $table->string('nutrient_content');
            $table->string('organic_matter');
            $table->string('soil_type');
            $table->string('recommendations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_analysis');
    }
};
