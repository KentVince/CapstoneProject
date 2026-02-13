<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agricultural_professionals', function (Blueprint $table) {
            $table->id();
            $table->string('app_no')->unique();
            $table->string('agency');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('sex')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('age')->nullable();
            $table->string('municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('email_add')->nullable();
            $table->string('qr_code')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agricultural_professionals');
    }
};
