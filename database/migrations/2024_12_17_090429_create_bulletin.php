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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id('bulletin_id'); // Primary key
            $table->string('created_by');
            $table->date('date_posted');
            $table->string('category');
            $table->string('title');
            $table->text('content'); // Use 'text' for longer content
            $table->boolean('notification_sent')->default(false); // Boolean for notification status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
