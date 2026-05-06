<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agricultural_professionals', function (Blueprint $table) {
            // Add a JSON column to store selected roles
            $table->json('roles')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('agricultural_professionals', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
