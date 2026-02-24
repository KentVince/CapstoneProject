<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FarmerFarmImport implements WithMultipleSheets
{
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    private FarmersSheetImport $farmersSheet;
    private FarmsSheetImport $farmsSheet;

    public function __construct()
    {
        $this->farmersSheet = new FarmersSheetImport();
        $this->farmsSheet = new FarmsSheetImport();
        // The farms sheet holds a reference to the farmers sheet
        // so it can access the app_no-to-farmer mapping after sheet 0 is processed
        $this->farmsSheet->farmersSheet = $this->farmersSheet;
    }

    public function sheets(): array
    {
        return [
            0 => $this->farmersSheet,
            1 => $this->farmsSheet,
        ];
    }

    /**
     * Aggregate counts from both sheet importers.
     * Call this after Excel::import() completes.
     */
    public function collectResults(): void
    {
        $this->importedCount = $this->farmersSheet->importedCount + $this->farmsSheet->importedCount;
        $this->skippedCount = $this->farmersSheet->skippedCount + $this->farmsSheet->skippedCount;
        $this->errors = array_merge($this->farmersSheet->errors, $this->farmsSheet->errors);
    }

    public function getFarmersImported(): int
    {
        return $this->farmersSheet->importedCount;
    }

    public function getFarmsImported(): int
    {
        return $this->farmsSheet->importedCount;
    }
}
