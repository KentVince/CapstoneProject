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
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->text('farmer_reply')->nullable();
            $table->timestamp('farmer_reply_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->dropColumn(['farmer_reply', 'farmer_reply_date']);
        });
    }
};
