<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (Schema::hasColumn('pest_and_disease', 'image_url') &&
                !Schema::hasColumn('pest_and_disease', 'image_path')) {
                $table->renameColumn('image_url', 'image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (Schema::hasColumn('pest_and_disease', 'image_path') &&
                !Schema::hasColumn('pest_and_disease', 'image_url')) {
                $table->renameColumn('image_path', 'image_url');
            }
        });
    }
};
