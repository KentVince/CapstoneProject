<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id')->nullable(); // FK to farmers table
            $table->string('type')->default('farmer'); // farmer | not_farmer
            $table->string('app_no')->nullable(); // from farmers table
            $table->string('username')->unique();
            $table->string('password');
            $table->string('full_name')->nullable();
            $table->string('barangay')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('farm_location')->nullable();
            $table->string('farm_size')->nullable();
            $table->timestamps();

            // Foreign Key to farmers table
            $table->foreign('farmer_id')
                  ->references('id')
                  ->on('farmers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_users');
    }
};
