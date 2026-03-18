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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('app_no')->nullable();
            $table->string('rsbsa_no')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('user_type')->default('farmer');
            $table->string('agency')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('ext_name')->nullable();
            $table->string('farmer_address_prk')->nullable();
            $table->string('farmer_address_bgy')->nullable();
            $table->string('farmer_address_mun')->nullable();
            $table->string('farmer_address_prv')->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->string('contact_num')->nullable();
            $table->string('email_add')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
