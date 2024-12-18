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
            $table->id('soil_test_id'); // Primary key
            $table->unsignedBigInteger('farmer_id'); // Foreign key to farmers table
            $table->unsignedBigInteger('farm_id');   // Foreign key to farm table
            $table->string('ref_no');
            $table->string('submitted_by');
            $table->date('date_collected');
            $table->date('date_submitted');
            $table->date('date_analyzed');
            $table->string('lab_no');
            $table->string('field_no');
            $table->string('soil_type');
            $table->double('pH_level',10,2);
            $table->double('w_om',10,2);
            $table->double('p_ppm',10,2);
            $table->double('k_ppm',10,2);
            $table->double('wb_om',10,2);
            $table->double('wb_oc',10,2);
            $table->string('crop_variety');
            $table->string('nutrient_req_N');
            $table->string('nutrient_req_P2O3');
            $table->string('nutrient_req_K2O');
            $table->string('lime_req')->nullable();
            $table->string('pH_preference');
            $table->string('organic_matter');
            $table->timestamps();

             // Foreign key constraints
        $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
        $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');

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
