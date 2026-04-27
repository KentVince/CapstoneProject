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

            // Log the actual heading keys on first row so admins can diagnose header mismatches
            if ($index === 0) {
                Log::info('Farms import detected headings', [
                    'keys' => array_keys(is_array($row) ? $row : $row->toArray()),
                ]);
            }

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
                    'verified_area'      => $this->nullIfEmpty($this->pickValue($row, [
                        'verified_area', 'verified_area_ha', 'verified_area_hectare', 'verified_area_hectares',
                        'verified_hectare', 'verified_hectares', 'verifiedarea',
                    ])),
                    'farmworker'         => ucfirst(strtolower(trim($row['farmworker'] ?? 'No'))),
                    'status'             => $this->nullIfEmpty($this->pickValue($row, [
                        'status', 'farm_status',
                    ])) ?? 'pending',
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

    private function parseDecimal($value): ?float
    {
        if ($value === null || $value === '' || strtolower((string) $value) === 'null') {
            return null;
        }
        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return is_numeric($clean) ? (float) $clean : null;
    }

    /**
     * Try multiple possible header keys (after WithHeadingRow normalization)
     * and return the first non-empty value. Handles cases where the Excel
     * column heading includes units or extra words (e.g. "Verified Area (ha)"
     * normalizes to "verified_area_ha").
     */
    private function pickValue($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) {
                return $row[$key];
            }
        }
        return null;
    }
}
