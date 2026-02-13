<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->string('type')->nullable()->after('pest');
        });

        // Populate type column based on PestAndDiseaseCategory
        $categories = DB::table('pest_and_disease_categories')->pluck('type', 'name');

        DB::table('pest_and_disease')->get()->each(function ($record) use ($categories) {
            $type = $categories[$record->pest] ?? 'pest';
            DB::table('pest_and_disease')
                ->where('case_id', $record->case_id)
                ->update(['type' => $type]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
