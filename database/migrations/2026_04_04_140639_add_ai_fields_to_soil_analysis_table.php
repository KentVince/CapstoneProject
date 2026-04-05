<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->text('ai_diagnosis')->nullable()->after('recommendation');
            $table->text('ai_farmer_summary')->nullable()->after('ai_diagnosis');
            $table->json('ai_key_concerns')->nullable()->after('ai_farmer_summary');
            $table->json('ai_priority_actions')->nullable()->after('ai_key_concerns');
            $table->json('ai_soil_remarks')->nullable()->after('ai_priority_actions');
            $table->json('ai_organic_alternatives')->nullable()->after('ai_soil_remarks');
            $table->json('ai_practices')->nullable()->after('ai_organic_alternatives');
            $table->json('ai_monitoring_plan')->nullable()->after('ai_practices');
            $table->text('ai_expected_outcomes')->nullable()->after('ai_monitoring_plan');
            $table->json('ai_reminders')->nullable()->after('ai_expected_outcomes');
        });
    }

    public function down(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->dropColumn([
                'ai_diagnosis', 'ai_farmer_summary', 'ai_key_concerns',
                'ai_priority_actions', 'ai_soil_remarks', 'ai_organic_alternatives',
                'ai_practices', 'ai_monitoring_plan', 'ai_expected_outcomes', 'ai_reminders',
            ]);
        });
    }
};
