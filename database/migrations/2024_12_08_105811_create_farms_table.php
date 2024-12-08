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
            $table->unsignedBigInteger('farmer_id'); // Add foreign key column
            $table->string('lot_hectare');
            $table->string('sitio');
            $table->string('barangay');
            $table->string('municipality');
            $table->string('province');
            $table->string('north');
            $table->string('south');
            $table->string('east');
            $table->string('west');
            $table->string('variety');
            $table->string('planning_method')->nullable(); ;
            $table->string('date_of_sowing')->nullable(); ;
            $table->string('date_of_planning');
            $table->string('date_of_harvest')->nullable(); ;
            $table->string('population_density')->nullable(); ;
            $table->string('age_group');
            $table->string('no_of_hills');
            $table->string('land_category')->nullable(); ;
            $table->string('soil_type')->nullable(); ;
            $table->string('topography')->nullable(); ;
            $table->string('source_of_irrigation')->nullable(); ;
            $table->string('tenurial_status')->nullable(); ;
            $table->timestamps();


         // Define the foreign key constraint
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
