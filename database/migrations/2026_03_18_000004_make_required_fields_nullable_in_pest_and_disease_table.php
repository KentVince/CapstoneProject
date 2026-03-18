<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_and_disease', function (Blueprint $table) {
            $nullable = [
                'farmer_id'     => 'unsignedBigInteger',
                'farm_id'       => 'unsignedBigInteger',
                'date_detected' => 'date',
                'type'          => 'string',
                'pest'          => 'string',
                'severity'      => 'string',
                'latitude'      => 'string',
                'longitude'     => 'string',
                'options'       => 'text',
            ];

            foreach ($nullable as $col => $colType) {
                if (Schema::hasColumn('pest_and_disease', $col)) {
                    if ($colType === 'unsignedBigInteger') {
                        $table->unsignedBigInteger($col)->nullable()->default(null)->change();
                    } elseif ($colType === 'date') {
                        $table->date($col)->nullable()->default(null)->change();
                    } elseif ($colType === 'text') {
                        $table->text($col)->nullable()->default(null)->change();
                    } else {
                        $table->string($col)->nullable()->default(null)->change();
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty — reverting NOT NULL on production data is unsafe
    }
};
