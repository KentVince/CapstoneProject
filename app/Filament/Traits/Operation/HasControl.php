<?php

namespace App\Traits\Operation;

use App\Models\Farmer;
use Illuminate\Support\Str;

trait HasControl
{
    /**
     * ✅ Generates a unique control/application number.
     * Example: COF-00001, COF-00002, ...
     */
 public function generateControlNumber(string $prefix): string
    {
        $last = Farmer::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;

        return sprintf('%s-%05d', $prefix, $nextId);
    }

    /**
     * Optional variant that includes the current year
     * Example: COF-2025-00001
     */
    public function generateControlNumberWithYear(string $prefix): string
    {
        $year = now()->format('Y');
        $latest = Farmer::whereYear('created_at', $year)->latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $nextId);
    }
}
