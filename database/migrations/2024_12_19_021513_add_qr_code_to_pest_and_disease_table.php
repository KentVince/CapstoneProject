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
        Schema::table('pest_and_disease', function (Blueprint $table) {
            //
            // $table->string('qr_code')->nullable()->after('date_detected'); // Adjust the position as needed
            $table->string('qr_code');
            $table->text('options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropColumn('qr_code');
            $table->dropColumn('options');
        });
    }
};
