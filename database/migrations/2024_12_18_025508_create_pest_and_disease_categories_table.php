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
        Schema::create('pest_and_disease_categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('type'); // E.g., Pest, Disease
            $table->string('name'); // Name of the pest or disease
            $table->text('description')->nullable(); // Optional detailed information
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pest_and_disease_categories');
    }
};
