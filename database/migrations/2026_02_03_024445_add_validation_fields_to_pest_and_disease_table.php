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
            $table->enum('validation_status', ['pending', 'approved', 'disapproved'])->default('pending')->after('area');
            $table->text('expert_comments')->nullable()->after('validation_status');
            $table->unsignedBigInteger('validated_by')->nullable()->after('expert_comments');
            $table->timestamp('validated_at')->nullable()->after('validated_by');

            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validation_status', 'expert_comments', 'validated_by', 'validated_at']);
        });
    }
};
