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
            $table->text('farmer_action')->nullable()->after('validated_at');
            $table->timestamp('farmer_action_date')->nullable()->after('farmer_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropColumn(['farmer_action', 'farmer_action_date']);
        });
    }
};
