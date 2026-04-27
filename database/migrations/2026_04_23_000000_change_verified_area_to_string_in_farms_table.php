<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL raw ALTER because doctrine/dbal is not installed in this project.
        DB::statement('ALTER TABLE farms MODIFY verified_area VARCHAR(255) NULL');
    }

    public function down(): void
    {
        // Non-numeric values will be lost on rollback (set to NULL implicitly by MySQL).
        DB::statement('ALTER TABLE farms MODIFY verified_area DECIMAL(10,2) NULL');
    }
};
