<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->text('ai_description')->nullable()->after('ai_recommendation');
            $table->json('ai_symptoms')->nullable()->after('ai_description');
            $table->json('ai_causes')->nullable()->after('ai_symptoms');
            $table->text('ai_impact')->nullable()->after('ai_causes');
            $table->json('ai_action_plan')->nullable()->after('ai_impact');
            $table->json('ai_immediate_response')->nullable()->after('ai_action_plan');
            $table->json('ai_long_term_strategy')->nullable()->after('ai_immediate_response');
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropColumn([
                'ai_description',
                'ai_symptoms',
                'ai_causes',
                'ai_impact',
                'ai_action_plan',
                'ai_immediate_response',
                'ai_long_term_strategy',
            ]);
        });
    }
};
