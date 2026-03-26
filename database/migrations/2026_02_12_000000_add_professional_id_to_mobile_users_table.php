<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->unsignedBigInteger('professional_id')->nullable()->after('farmer_id');

            $table->foreign('professional_id')
                  ->references('id')
                  ->on('agricultural_professionals')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn('professional_id');
        });
    }
};
