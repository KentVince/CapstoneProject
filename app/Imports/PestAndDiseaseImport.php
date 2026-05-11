<?php

namespace App\Imports;

use App\Models\PestAndDisease;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PestAndDiseaseImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public int   $importedCount = 0;
    public int   $skippedCount  = 0;
    public array $errors        = [];

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        set_time_limit(300);

        $batch     = [];
        $now       = now()->toDateTimeString();
        $fillable  = (new PestAndDisease)->getFillable();
        $fillableMap = array_flip($fillable);

        // Pre-load valid FK target IDs so we can null out stale references
        // from spreadsheets prepared on a different environment, instead of
        // failing the whole batch on a foreign key constraint violation.
        $validUserIds   = DB::table('users')->pluck('id')->flip();
        $validExpertIds = DB::table('agricultural_professionals')->pluck('id')->flip();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $pest         = $this->nullIfEmpty($row['pest']         ?? null);
                $dateDetected = $this->parseDate($row['date_detected']  ?? null);

                if (empty($pest) || empty($dateDetected)) {
                    $this->skippedCount++;
                    $this->errors[] = "Row {$rowNumber}: missing pest or date_detected — skipped.";
                    continue;
                }

                $expertId    = $this->parseInt($row['expert_id']    ?? null);
                $validatedBy = $this->parseInt($row['validated_by'] ?? null);

                if ($expertId !== null && ! $validExpertIds->has($expertId)) {
                    $this->errors[] = "Row {$rowNumber}: expert_id {$expertId} not found — set to null.";
                    $expertId = null;
                }
                if ($validatedBy !== null && ! $validUserIds->has($validatedBy)) {
                    $this->errors[] = "Row {$rowNumber}: validated_by {$validatedBy} not found — set to null.";
                    $validatedBy = null;
                }

                $record = [
                    'app_no'              => $this->nullIfEmpty($row['app_no']              ?? null),
                    'expert_id'           => $expertId,
                    'farmer_id'           => $this->parseInt($row['farmer_id']              ?? null),
                    'farm_id'             => $this->parseInt($row['farm_id']                ?? null),
                    'date_detected'       => $dateDetected,
                    'type'                => $this->nullIfEmpty($row['type']                ?? null),
                    'pest'                => $pest,
                    'confidence'          => $this->parseDecimal($row['confidence']         ?? null),
                    'severity'            => $this->nullIfEmpty($row['severity']            ?? null),
                    'pest_incidence'      => $this->parseDecimal($row['pest_incidence']     ?? null),
                    'incidence_rating'    => $this->nullIfEmpty($row['incidence_rating']    ?? null),
                    'pest_severity_pct'   => $this->parseDecimal($row['pest_severity_pct']  ?? null),
                    'sum_ratings'         => $this->parseInt($row['sum_ratings']            ?? null),
                    'n_infested'          => $this->parseInt($row['n_infested']             ?? null),
                    'n_total'             => $this->parseInt($row['n_total']                ?? null),
                    'total_trees_planted' => $this->parseInt($row['total_trees_planted']    ?? null),
                    'scan_results'        => $this->nullIfEmpty($row['scan_results']        ?? null),
                    'image_path'          => $this->nullIfEmpty($row['image_path']          ?? null),
                    'validation_status'   => $this->resolveValidationStatus($row['validation_status'] ?? null),
                    'expert_comments'     => $this->nullIfEmpty($row['expert_comments']     ?? null),
                    'ai_recommendation'   => $this->nullIfEmpty($row['ai_recommendation']   ?? null),
                    'validated_by'        => $validatedBy,
                    'validated_at'        => $this->parseDateTime($row['validated_at']      ?? null),
                    'admin_viewed_at'     => $this->parseDateTime($row['admin_viewed_at']   ?? null),
                    'area'                => $this->nullIfEmpty($row['area']                ?? null),
                    'latitude'            => $this->parseDecimal($row['latitude']           ?? null),
                    'longitude'           => $this->parseDecimal($row['longitude']          ?? null),
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];

                // Only include columns that exist on the model
                $batch[] = array_intersect_key($record, $fillableMap + ['created_at' => 1, 'updated_at' => 1]);

            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Row {$rowNumber}: {$e->getMessage()}";
                Log::warning("PestAndDisease import error at row {$rowNumber}: {$e->getMessage()}");
            }
        }

        // Insert collected rows in sub-chunks of 100. If a chunk fails
        // (e.g. an unrelated constraint), fall back to per-row inserts so
        // one bad row doesn't take down the entire batch.
        foreach (array_chunk($batch, 100) as $chunk) {
            try {
                DB::table('pest_and_disease')->insert($chunk);
                $this->importedCount += count($chunk);
            } catch (\Exception $e) {
                Log::warning("PestAndDisease batch insert failed, retrying per-row: {$e->getMessage()}");
                foreach ($chunk as $row) {
                    try {
                        DB::table('pest_and_disease')->insert($row);
                        $this->importedCount++;
                    } catch (\Exception $rowEx) {
                        $this->skippedCount++;
                        $this->errors[] = "Row insert failed: {$rowEx->getMessage()}";
                        Log::error("PestAndDisease row insert error: {$rowEx->getMessage()}");
                    }
                }
            }
        }
    }

    private function resolveValidationStatus($value): string
    {
        $val = strtolower(trim((string) ($value ?? '')));
        $map = [
            'validated' => 'approved',
            'approved'  => 'approved',
            'rejected'  => 'rejected',
            'pending'   => 'pending',
        ];
        return $map[$val] ?? 'pending';
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

    private function parseInt($value): ?int
    {
        if ($value === null || $value === '' || strtolower((string) $value) === 'null') {
            return null;
        }
        $clean = preg_replace('/[^0-9\-]/', '', (string) $value);
        return is_numeric($clean) ? (int) $clean : null;
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function parseDateTime($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d H:i:s');
            }
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return null;
        }
    }
}
