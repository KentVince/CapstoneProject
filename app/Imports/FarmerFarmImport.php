<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FarmerFarmImport implements WithMultipleSheets
{
    public int $importedCount = 0;
    public int $skippedCount  = 0;
    public array $errors = [];

    private FarmersSheetImport $farmersSheet;
    private FarmsSheetImport   $farmsSheet;

    public function __construct()
    {
        $this->farmersSheet = new FarmersSheetImport();
        $this->farmsSheet   = new FarmsSheetImport();

        // Give farms sheet access to the farmer ID map built during farmers sheet processing.
        // Farmers (sheet 0) processes before farms (sheet 1), so the map is ready in time.
        $this->farmsSheet->farmersSheet = $this->farmersSheet;
    }

    public function sheets(): array
    {
        return [
            0 => $this->farmersSheet, // Excel sheet 0 = farmers
            1 => $this->farmsSheet,   // Excel sheet 1 = farms
        ];
    }

    /**
     * Aggregate counts from both sheet importers after Excel::import() completes.
     */
    public function collectResults(): void
    {
        $this->importedCount = $this->farmersSheet->importedCount + $this->farmsSheet->importedCount;
        $this->skippedCount  = $this->farmersSheet->skippedCount  + $this->farmsSheet->skippedCount;
        $this->errors        = array_merge($this->farmersSheet->errors, $this->farmsSheet->errors);
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
