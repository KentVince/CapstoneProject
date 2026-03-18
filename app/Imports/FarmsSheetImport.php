<?php

namespace App\Imports;

use App\Models\Barangay;
use App\Models\Farm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FarmsSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $skippedCount  = 0;
    public array $errors = [];

    /**
     * Reference to the farmers sheet importer.
     * Farmers sheet (index 0) is processed before farms (index 1),
     * so rowIndexToFarmerId is already populated when collection() runs here.
     */
    public ?FarmersSheetImport $farmersSheet = null;

    /**
     * Pre-loaded barangay lookup: "muni_filter|barangay_name_lowercase" => code
     */
    private array $barangayMap = [];

    public function collection(Collection $rows)
    {
        $rowMap = $this->farmersSheet ? $this->farmersSheet->rowIndexToFarmerId : [];

        // Build barangay lookup map once to avoid N+1 queries
        $this->barangayMap = Barangay::all(['code', 'barangay', 'muni_filter'])
            ->mapWithKeys(fn ($b) => [
                $b->muni_filter . '|' . strtolower(trim($b->barangay)) => $b->code
            ])
            ->toArray();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                // farmer_id in Excel is 1-based sequential → 0-based index in farmers sheet
                $farmerExcelId = (int) ($row['farmer_id'] ?? ($index + 1));
                $farmerIndex   = $farmerExcelId - 1;
                $farmerId      = $rowMap[$farmerIndex] ?? null;

                if (!$farmerId) {
                    $this->skippedCount++;
                    $this->errors[] = "Farms row {$rowNumber}: No matching farmer (index {$farmerIndex}). Skipped.";
                    continue;
                }

                if (empty($row['farm_name']) && empty($row['crop_name'])) {
                    $this->skippedCount++;
                    continue;
                }

                $munCode = trim($row['farmer_address_mun'] ?? '');
                $bgyName = trim($row['farmer_address_bgy'] ?? '');

                Farm::create([
                    'farmer_id'          => $farmerId,
                    'farm_name'          => $row['farm_name']    ?? null,
                    'farmer_address_bgy' => $this->lookupBarangayCode($bgyName, $munCode),
                    'farmer_address_mun' => $munCode ?: null,
                    'farmer_address_prv' => trim($row['farmer_address_prv'] ?? 'Davao de Oro'),
                    'latitude'           => $row['latitude']     ?? null,
                    'longtitude'         => $row['longtitude']   ?? null,
                    'crop_name'          => $row['crop_name']    ?? 'Coffee',
                    'crop_variety'       => $row['crop_variety'] ?? null,
                    'crop_area'          => is_numeric($row['crop_area'] ?? null) ? $row['crop_area'] : null,
                    'soil_type'          => $this->nullIfEmpty($row['soil_type']     ?? null),
                    'verified_area'      => is_numeric($row['verified_area'] ?? null) ? $row['verified_area'] : null,
                    'farmworker'         => ucfirst(strtolower(trim($row['farmworker'] ?? 'No'))),
                    'status'             => 'pending',
                ]);

                $this->importedCount++;

            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Farms row {$rowNumber}: {$e->getMessage()}";
                Log::warning("Farms import error at row {$rowNumber}: {$e->getMessage()}");
            }
        }
    }

    private function lookupBarangayCode(?string $bgyName, ?string $munCode): ?string
    {
        if (empty($bgyName) || empty($munCode)) {
            return null;
        }

        $key = $munCode . '|' . strtolower(trim($bgyName));

        return $this->barangayMap[$key] ?? null;
    }

    private function nullIfEmpty($value): ?string
    {
        if ($value === null || $value === '' || strtolower((string) $value) === 'null') {
            return null;
        }
        return $value;
    }
}
