<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            if (!Schema::hasColumn('pest_and_disease', 'pest_incidence')) {
                $table->float('pest_incidence')->nullable()->after('severity');
            }
            if (!Schema::hasColumn('pest_and_disease', 'incidence_rating')) {
                $table->string('incidence_rating')->nullable()->after('pest_incidence');
            }
            if (!Schema::hasColumn('pest_and_disease', 'pest_severity_pct')) {
                $table->float('pest_severity_pct')->nullable()->after('incidence_rating');
            }
            if (!Schema::hasColumn('pest_and_disease', 'sum_ratings')) {
                $table->integer('sum_ratings')->nullable()->after('pest_severity_pct');
            }
            if (!Schema::hasColumn('pest_and_disease', 'n_infested')) {
                $table->integer('n_infested')->nullable()->after('sum_ratings');
            }
            if (!Schema::hasColumn('pest_and_disease', 'n_total')) {
                $table->integer('n_total')->nullable()->after('n_infested');
            }
            if (!Schema::hasColumn('pest_and_disease', 'total_trees_planted')) {
                $table->integer('total_trees_planted')->nullable()->after('n_total');
            }
            if (!Schema::hasColumn('pest_and_disease', 'scan_results')) {
                $table->text('scan_results')->nullable()->after('total_trees_planted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $table->dropColumn([
                'pest_incidence', 'incidence_rating', 'pest_severity_pct',
                'sum_ratings', 'n_infested', 'n_total', 'total_trees_planted', 'scan_results',
            ]);
        });
    }
};
