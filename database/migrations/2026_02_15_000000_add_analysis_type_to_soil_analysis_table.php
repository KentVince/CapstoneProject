<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->string('analysis_type')->nullable()->after('soil_type');
        });
    }

    public function down(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->dropColumn('analysis_type');
        });
    }
};
