<?php

namespace App\Imports;

use App\Models\Barangay;
use App\Models\Farmer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FarmersSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $skippedCount  = 0;
    public array $errors = [];

    /**
     * Maps Excel row index (0-based) to the created Farmer's DB id.
     */
    public array $rowIndexToFarmerId = [];

    /**
     * Pre-loaded barangay lookup: "muni_filter|barangay_name_lowercase" => code
     * Avoids N+1 queries for 1,345+ rows.
     */
    private array $barangayMap = [];

    public function collection(Collection $rows)
    {
        // Build barangay lookup map once: "muni_filter|name_lowercase" => code
        $this->barangayMap = Barangay::all(['code', 'barangay', 'muni_filter'])
            ->mapWithKeys(fn ($b) => [
                $b->muni_filter . '|' . strtolower(trim($b->barangay)) => $b->code
            ])
            ->toArray();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $lastName  = trim($row['last_name']  ?? '');
                $firstName = trim($row['first_name'] ?? '');

                if (empty($lastName) || empty($firstName)) {
                    $this->skippedCount++;
                    $this->errors[] = "Farmers row {$rowNumber}: Missing last_name or first_name. Skipped.";
                    continue;
                }

                $appNo = trim($row['app_no'] ?? '');
                if (empty($appNo)) {
                    $appNo = $this->generateControlNumber('COF');
                }

                // If already in DB, map the existing ID and skip
                if ($existing = Farmer::where('app_no', $appNo)->first()) {
                    $this->rowIndexToFarmerId[$index] = $existing->id;
                    $this->skippedCount++;
                    $this->errors[] = "Farmers row {$rowNumber}: {$appNo} already exists. Skipped.";
                    continue;
                }

                $munCode = trim($row['farmer_address_mun'] ?? '');
                $bgyName = trim($row['farmer_address_bgy'] ?? '');

                $farmer = Farmer::create([
                    'app_no'             => $appNo,
                    'rsbsa_no'           => $row['rsbsa_no'] ?? null,
                    'user_type'          => strtolower(trim($row['user_type'] ?? 'farmer')),
                    'agency'             => $row['agency'] ?? null,
                    'last_name'          => $lastName,
                    'first_name'         => $firstName,
                    'middle_name'        => $row['middle_name'] ?? null,
                    'ext_name'           => $row['ext_name'] ?? null,
                    'farmer_address_bgy' => $this->lookupBarangayCode($bgyName, $munCode),
                    'farmer_address_mun' => $munCode ?: null,
                    'farmer_address_prv' => trim($row['farmer_address_prv'] ?? 'Davao de Oro'),
                    'birthday'           => $this->parseDate($row['birthday'] ?? null),
                    'gender'             => ucfirst(strtolower(trim($row['gender'] ?? ''))),
                    'contact_num'        => $this->formatContactNum($row['contact_num'] ?? null),
                    'email_add'          => $row['email_add'] ?? null,
                ]);

                $this->rowIndexToFarmerId[$index] = $farmer->id;
                $this->importedCount++;

            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Farmers row {$rowNumber}: {$e->getMessage()}";
                Log::warning("Farmers import error at row {$rowNumber}: {$e->getMessage()}");
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

    private function formatContactNum($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $num = (string)(is_float($value) ? (int)$value : $value);
        // If it's 10 digits starting with 9, prefix with 0
        if (strlen($num) === 10 && $num[0] === '9') {
            $num = '0' . $num;
        }
        return $num;
    }

    private function generateControlNumber(string $prefix): string
    {
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->format('m');

        $last = Farmer::where('app_no', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('created_at', 'desc')
            ->first();

        $lastSeries = 0;
        if ($last && $last->app_no) {
            $parts      = explode('-', $last->app_no);
            $lastSeries = (int) end($parts);
        }

        return "{$prefix}-{$year}-{$month}-" . sprintf('%05d', $lastSeries + 1);
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                )->format('Y-m-d');
            } catch (\Exception) {
                return null;
            }
        }

        foreach (['Y-m-d', 'm/d/Y', 'd/m/Y', 'M d, Y', 'd-m-Y', 'm-d-Y'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->format('Y-m-d');
            } catch (\Exception) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
