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
            $table->string('app_no');
            $table->string('crop');
            $table->string('funding_source');
            $table->date('date_of_application');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('purok');
            $table->string('barangay');
            $table->string('municipality');
            $table->string('province');
            $table->string('phone_no');
            $table->string('sex');
            $table->date('birthdate');
            $table->integer('age');
            $table->string('civil_status');
            $table->string('pwd');
            $table->string('ip');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('spouse')->nullable();
            $table->string('primary_beneficiaries');
            $table->integer('primary_beneficiaries_age');
            $table->string('primary_beneficiaries_relationship');
            $table->string('secondary_beneficiaries');
            $table->integer('secondary_beneficiaries_age');
            $table->string('secondary_beneficiaries_relationship');
            $table->string('assignee')->nullable();
            $table->string('reason_assignment')->nullable();
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
