<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            // ✅ Rename 'name' → 'pest'
            if (Schema::hasColumn('pest_and_disease', 'name')) {
                $table->renameColumn('name', 'pest');
            }

            // ✅ Rename 'longtitude' → 'longitude'
            if (Schema::hasColumn('pest_and_disease', 'longtitude')) {
                $table->renameColumn('longtitude', 'longitude');
            }

            // ✅ Add new columns (if not exist)
            if (!Schema::hasColumn('pest_and_disease', 'area')) {
                $table->string('area')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('pest_and_disease', 'confidence')) {
                $table->float('confidence')->nullable()->after('name');
            }

            // ✅ Ensure 'date_detected' exists
            if (!Schema::hasColumn('pest_and_disease', 'date_detected')) {
                $table->dateTime('date_detected')->nullable()->after('farm_id');
            }

            // ✅ Drop columns that are no longer needed
            if (Schema::hasColumn('pest_and_disease', 'timestamp')) {
                $table->dropColumn('timestamp');
            }
            if (Schema::hasColumn('pest_and_disease', 'type')) {
                // keep if needed — else uncomment to drop
                // $table->dropColumn('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            // Rollback changes
            if (Schema::hasColumn('pest_and_disease', 'pest')) {
                $table->renameColumn('pest', 'name');
            }
            if (Schema::hasColumn('pest_and_disease', 'longitude')) {
                $table->renameColumn('longitude', 'longtitude');
            }
            if (Schema::hasColumn('pest_and_disease', 'area')) {
                $table->dropColumn('area');
            }
            if (Schema::hasColumn('pest_and_disease', 'confidence')) {
                $table->dropColumn('confidence');
            }
        });
    }
};
