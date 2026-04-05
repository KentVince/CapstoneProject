<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add a temporary JSON column alongside the existing string column
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->json('lab_file_new')->nullable()->after('lab_file');
        });

        // 2. Migrate existing single-string values into JSON array
        DB::table('soil_analysis')
            ->whereNotNull('lab_file')
            ->where('lab_file', '!=', '')
            ->get(['id', 'lab_file'])
            ->each(function ($row) {
                $decoded = json_decode($row->lab_file, true);
                $array = is_array($decoded) ? $decoded : [$row->lab_file];
                DB::table('soil_analysis')
                    ->where('id', $row->id)
                    ->update(['lab_file_new' => json_encode($array)]);
            });

        // 3. Drop old column and rename new one
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->dropColumn('lab_file');
        });

        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->renameColumn('lab_file_new', 'lab_file');
        });
    }

    public function down(): void
    {
        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->string('lab_file_old')->nullable()->after('lab_file');
        });

        DB::table('soil_analysis')
            ->whereNotNull('lab_file')
            ->get(['id', 'lab_file'])
            ->each(function ($row) {
                $array = json_decode($row->lab_file, true);
                $single = is_array($array) ? ($array[0] ?? null) : $row->lab_file;
                DB::table('soil_analysis')
                    ->where('id', $row->id)
                    ->update(['lab_file_old' => $single]);
            });

        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->dropColumn('lab_file');
        });

        Schema::table('soil_analysis', function (Blueprint $table) {
            $table->renameColumn('lab_file_old', 'lab_file');
        });
    }
};
