<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (Schema::hasColumn('pest_and_disease', 'qr_code')) {
                $table->string('qr_code')->nullable()->default(null)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (Schema::hasColumn('pest_and_disease', 'qr_code')) {
                $table->string('qr_code')->nullable(false)->change();
            }
        });
    }
};
