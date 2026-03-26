<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (!Schema::hasColumn('pest_and_disease', 'farmer_id')) {
                $table->unsignedBigInteger('farmer_id')->nullable()->after('expert_id');
            }

            if (!Schema::hasColumn('pest_and_disease', 'farm_id')) {
                $table->unsignedBigInteger('farm_id')->nullable()->after('farmer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (Schema::hasColumn('pest_and_disease', 'farm_id')) {
                $table->dropColumn('farm_id');
            }

            if (Schema::hasColumn('pest_and_disease', 'farmer_id')) {
                $table->dropColumn('farmer_id');
            }
        });
    }
};
