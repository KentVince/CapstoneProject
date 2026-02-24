<?php

namespace App\Imports;

use App\Models\Farm;
use App\Models\Farmer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class FarmsSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    /**
     * Reference to the farmers sheet importer to access row-index mapping.
     */
    public ?FarmersSheetImport $farmersSheet = null;

    public function collection(Collection $rows)
    {
        // Get the mapping from row index to farmer ID
        $rowMap = $this->farmersSheet ? $this->farmersSheet->rowIndexToFarmerId : [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                // Find the farmer by matching row index (row 1 in farms = row 1 in farmers)
                $farmerId = $rowMap[$index] ?? null;
                $farmer = $farmerId ? Farmer::find($farmerId) : null;

                if (!$farmer) {
                    $this->skippedCount++;
                    $this->errors[] = "Farms sheet row {$rowNumber}: No matching farmer found. Skipped.";
                    continue;
                }

                if (empty($row['farm_name']) && empty($row['lot_hectare']) && empty($row['variety'])) {
                    $this->skippedCount++;
                    $this->errors[] = "Farms sheet row {$rowNumber}: No farm data found. Skipped.";
                    continue;
                }

                Farm::create([
                    'farmer_id'            => $farmer->id,
                    'name'                 => $row['farm_name'] ?? '',
                    'lot_hectare'          => $row['lot_hectare'] ?? '',
                    'sitio'                => $row['farm_sitio'] ?? '',
                    'barangay'             => $row['farm_barangay'] ?? ($farmer->barangay ?? ''),
                    'municipality'         => $row['farm_municipality'] ?? ($farmer->municipality ?? ''),
                    'province'             => $row['farm_province'] ?? ($farmer->province ?? 'Davao de Oro'),
                    'latitude'             => $row['latitude'] ?? '',
                    'longitude'            => $row['longitude'] ?? '',
                    'north'                => $row['north'] ?? '',
                    'south'                => $row['south'] ?? '',
                    'east'                 => $row['east'] ?? '',
                    'west'                 => $row['west'] ?? '',
                    'variety'              => $row['variety'] ?? '',
                    'planning_method'      => $row['planning_method'] ?? null,
                    'date_of_sowing'       => $row['date_of_sowing'] ?? null,
                    'date_of_planning'     => $row['date_of_planning'] ?? '',
                    'date_of_harvest'      => $row['date_of_harvest'] ?? null,
                    'population_density'   => $row['population_density'] ?? null,
                    'age_group'            => $row['age_group'] ?? '',
                    'no_of_hills'          => $row['no_of_hills'] ?? '',
                    'land_category'        => $row['land_category'] ?? null,
                    'soil_type'            => $row['soil_type'] ?? null,
                    'topography'           => $row['topography'] ?? null,
                    'source_of_irrigation' => $row['source_of_irrigation'] ?? null,
                    'tenurial_status'      => $row['tenurial_status'] ?? null,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Farms sheet row {$rowNumber}: {$e->getMessage()}";
                Log::warning("Farms sheet import error at row {$rowNumber}: {$e->getMessage()}");
            }
        }
    }
}
