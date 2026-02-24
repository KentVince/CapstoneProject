<?php

namespace App\Imports;

use App\Models\Farmer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class FarmersSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    /**
     * Maps row index to the created Farmer's ID,
     * so the farms sheet can link records by row position.
     */
    public array $rowIndexToFarmerId = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                if (empty($row['lastname']) || empty($row['firstname'])) {
                    $this->skippedCount++;
                    $this->errors[] = "Farmers sheet row {$rowNumber}: Missing lastname or firstname. Skipped.";
                    continue;
                }

                $birthdate = null;
                $age = null;
                if (!empty($row['birthdate'])) {
                    try {
                        $birthdate = $this->parseDate($row['birthdate']);
                        $age = $birthdate ? Carbon::parse($birthdate)->age : null;
                    } catch (\Exception $e) {
                        $birthdate = null;
                        $age = null;
                    }
                }

                // Auto-generate app_no using same format as CreateFarmer
                $appNo = $this->generateControlNumber('COF');

                $farmerData = [
                    'app_no'                              => $appNo,
                    'user_type'                           => $row['user_type'] ?? 'farmer',
                    'agency'                              => $row['agency'] ?? null,
                    'date_of_application'                 => $this->parseDate($row['date_of_application'] ?? null),
                    'funding_source'                      => $row['funding_source'] ?? 'Self-Financed',
                    'crop'                                => $row['crop'] ?? 'Coffee',
                    'province'                            => $row['province'] ?? 'Davao de Oro',
                    'lastname'                            => $row['lastname'],
                    'firstname'                           => $row['firstname'],
                    'middlename'                          => $row['middlename'] ?? '',
                    'municipality'                        => $row['municipality'] ?? '',
                    'barangay'                            => $row['barangay'] ?? '',
                    'purok'                               => $row['purok'] ?? '',
                    'sex'                                 => $row['sex'] ?? '',
                    'birthdate'                           => $birthdate,
                    'age'                                 => $age ?? ($row['age'] ?? 0),
                    'civil_status'                        => $row['civil_status'] ?? 'single',
                    'spouse'                              => $row['spouse'] ?? null,
                    'ip'                                  => $row['ip'] ?? '',
                    'pwd'                                 => $row['pwd'] ?? '',
                    'phone_no'                            => $row['phone_no'] ?? '',
                    'bank_name'                           => $row['bank_name'] ?? null,
                    'bank_account_no'                     => $row['bank_account_no'] ?? null,
                    'bank_branch'                         => $row['bank_branch'] ?? null,
                    'primary_beneficiaries'               => $row['primary_beneficiaries'] ?? '',
                    'primary_beneficiaries_age'           => $row['primary_beneficiaries_age'] ?? 0,
                    'primary_beneficiaries_relationship'  => $row['primary_beneficiaries_relationship'] ?? '',
                    'secondary_beneficiaries'             => $row['secondary_beneficiaries'] ?? '',
                    'secondary_beneficiaries_age'         => $row['secondary_beneficiaries_age'] ?? 0,
                    'secondary_beneficiaries_relationship' => $row['secondary_beneficiaries_relationship'] ?? '',
                    'assignee'                            => $row['assignee'] ?? null,
                    'reason_assignment'                   => $row['reason_assignment'] ?? null,
                ];

                $farmer = Farmer::create($farmerData);

                // Store mapping by row index so farms sheet can find this farmer
                $this->rowIndexToFarmerId[$index] = $farmer->id;

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Farmers sheet row {$rowNumber}: {$e->getMessage()}";
                Log::warning("Farmers sheet import error at row {$rowNumber}: {$e->getMessage()}");
            }
        }
    }

    private function generateControlNumber(string $prefix): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->format('m');

        $lastControl = Farmer::where('app_no', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('created_at', 'desc')
            ->first();

        $lastSeries = 0;
        if ($lastControl && $lastControl->app_no) {
            $parts = explode('-', $lastControl->app_no);
            $lastSeries = (int) end($parts);
        }

        $newSeries = sprintf('%05d', $lastSeries + 1);

        return "{$prefix}-{$year}-{$month}-{$newSeries}";
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
            } catch (\Exception $e) {
                return null;
            }
        }

        $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'M d, Y', 'd-m-Y', 'm-d-Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
