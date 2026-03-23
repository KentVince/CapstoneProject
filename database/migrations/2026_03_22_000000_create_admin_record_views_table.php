<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_record_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('record_type'); // 'pest_disease' or 'soil_analysis'
            $table->unsignedBigInteger('record_id');
            $table->timestamp('viewed_at');
            $table->unique(['user_id', 'record_type', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_record_views');
    }
};
