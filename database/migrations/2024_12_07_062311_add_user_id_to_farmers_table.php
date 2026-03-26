<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Neutralized: user_id is no longer part of the farmers table schema.
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
