<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->string('lab_file')->nullable()->after('organic_matter');
        });
    }

    public function down(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->dropColumn('lab_file');
        });
    }
};
