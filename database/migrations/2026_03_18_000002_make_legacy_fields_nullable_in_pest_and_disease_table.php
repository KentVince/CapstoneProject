<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            foreach (['diagnosis_result', 'recommended_treatment', 'treatment_status'] as $col) {
                if (Schema::hasColumn('pest_and_disease', $col)) {
                    $table->string($col)->nullable()->default(null)->change();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            foreach (['diagnosis_result', 'recommended_treatment', 'treatment_status'] as $col) {
                if (Schema::hasColumn('pest_and_disease', $col)) {
                    $table->string($col)->nullable(false)->change();
                }
            }
        });
    }
};
