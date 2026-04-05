<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->unsignedBigInteger('expert_id')->nullable()->after('app_no');

            $table->foreign('expert_id')
                  ->references('id')
                  ->on('agricultural_professionals')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropForeign(['expert_id']);
            $table->dropColumn('expert_id');
        });
    }
};
