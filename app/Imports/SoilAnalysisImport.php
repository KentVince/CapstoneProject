<?php

namespace App\Imports;

use App\Models\Farm;
use App\Models\SoilAnalysis;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoilAnalysisImport implements ToCollection, WithHeadingRow
{
    public int   $importedCount = 0;
    public int   $skippedCount  = 0;
    public array $errors        = [];

    /** Farm name → [id, farmer_id] cache to avoid N+1 */
    private array $farmCache = [];

    public function collection(Collection $rows): void
    {
        // Pre-load farms keyed by lower-trimmed name
        Farm::select('id', 'farmer_id', 'farm_name')->each(function ($farm) {
            $this->farmCache[strtolower(trim($farm->farm_name))] = [
                'id'        => $farm->id,
                'farmer_id' => $farm->farmer_id,
            ];
        });

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $farmName = trim($row['farm_name'] ?? '');

                if (empty($farmName)) {
                    $this->skippedCount++;
                    $this->errors[] = "Row {$rowNumber}: farm_name is empty — skipped.";
                    continue;
                }

                // Resolve farm_id and farmer_id by farm name
                $farmEntry = $this->farmCache[strtolower($farmName)] ?? null;
                $farmId    = $farmEntry['id']        ?? null;
                $farmerId  = $farmEntry['farmer_id'] ?? null;

                // Skip duplicate sample_id
                $sampleId = $this->nullIfEmpty($row['sample_id'] ?? null);
                if ($sampleId && SoilAnalysis::where('sample_id', $sampleId)->exists()) {
                    $this->skippedCount++;
                    $this->errors[] = "Row {$rowNumber}: sample_id '{$sampleId}' already exists — skipped.";
                    continue;
                }

                SoilAnalysis::create([
                    'sample_id'       => $sampleId,
                    'farm_id'         => $farmId,
                    'farmer_id'       => $farmerId,
                    'farm_name'       => $farmName,
                    'soil_type'       => $this->nullIfEmpty($row['soil_type']      ?? null),
                    'topography'      => $this->nullIfEmpty($row['topography']      ?? null),
                    'analysis_type'   => $this->nullIfEmpty($row['analysis_type']   ?? null) ?? 'with_lab',
                    'crop_variety'    => $this->nullIfEmpty($row['crop_variety']    ?? null),
                    'date_collected'  => $this->parseDate($row['date_collected']   ?? null),
                    'location'        => $this->nullIfEmpty($row['location']        ?? null),
                    'ref_no'          => $this->nullIfEmpty($row['ref_no']          ?? null),
                    'submitted_by'    => $this->nullIfEmpty($row['submitted_by']    ?? null),
                    'date_submitted'  => $this->parseDate($row['date_submitted']   ?? null),
                    'date_analyzed'   => $this->parseDate($row['date_analyzed']    ?? null),
                    'lab_no'          => $this->nullIfEmpty($row['lab_no']          ?? null),
                    'field_no'        => $this->nullIfEmpty($row['field_no']        ?? null),
                    'ph_level'        => $this->parseDecimal($row['ph_level']       ?? null),
                    'nitrogen'        => $this->parseDecimal($row['nitrogen']        ?? null),
                    'phosphorus'      => $this->parseDecimal($row['phosphorus']      ?? null),
                    'potassium'       => $this->parseDecimal($row['potassium']       ?? null),
                    'organic_matter'    => $this->parseDecimal($row['organic_matter']    ?? null),
                    'validation_status' => $this->resolveValidationStatus($row['validation_status'] ?? null),
                    'validated_by'      => $this->nullIfEmpty($row['validated_by']      ?? null),
                ]);

                $this->importedCount++;

            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Row {$rowNumber}: {$e->getMessage()}";
                Log::warning("SoilAnalysis import error at row {$rowNumber}: {$e->getMessage()}");
            }
        }
    }

    private function resolveValidationStatus($value): string
    {
        $allowed = ['pending', 'approved', 'rejected'];
        $val = strtolower(trim((string) ($value ?? '')));
        return in_array($val, $allowed, true) ? $val : 'pending';
    }

    private function nullIfEmpty($value): ?string
    {
        if ($value === null || $value === '' || strtolower((string) $value) === 'null') {
            return null;
        }
        return trim((string) $value);
    }

    private function parseDecimal($value): ?float
    {
        if ($value === null || $value === '' || strtolower((string) $value) === 'null') {
            return null;
        }
        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return is_numeric($clean) ? (float) $clean : null;
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            // Handle numeric Excel serial dates
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
