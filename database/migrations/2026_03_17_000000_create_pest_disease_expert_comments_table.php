<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pest_disease_expert_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pest_and_disease_id');
            $table->foreign('pest_and_disease_id')
                  ->references('case_id')
                  ->on('pest_and_disease')
                  ->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pest_disease_expert_comments');
    }
};
