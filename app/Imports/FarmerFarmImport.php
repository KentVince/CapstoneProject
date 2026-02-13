<?php

namespace App\Imports;

use App\Models\Farm;
use App\Models\Farmer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class FarmerFarmImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because heading row is 1, and index is 0-based

            try {
                // Skip rows without required farmer fields
                if (empty($row['lastname']) || empty($row['firstname'])) {
                    $this->skippedCount++;
                    $this->errors[] = "Row {$rowNumber}: Missing lastname or firstname. Skipped.";
                    continue;
                }

                // Parse birthdate and calculate age
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

                // Create or update farmer based on app_no
                $farmerData = [
                    'app_no'                           => $row['app_no'] ?? null,
                    'user_type'                        => $row['user_type'] ?? 'farmer',
                    'agency'                           => $row['agency'] ?? null,
                    'date_of_application'              => $this->parseDate($row['date_of_application'] ?? null),
                    'funding_source'                   => $row['funding_source'] ?? 'Self-Financed',
                    'crop'                             => $row['crop'] ?? 'Coffee',
                    'province'                         => $row['province'] ?? 'Davao de Oro',
                    'lastname'                         => $row['lastname'],
                    'firstname'                        => $row['firstname'],
                    'middlename'                       => $row['middlename'] ?? '',
                    'municipality'                     => $row['municipality'] ?? '',
                    'barangay'                         => $row['barangay'] ?? '',
                    'purok'                            => $row['purok'] ?? '',
                    'sex'                              => $row['sex'] ?? '',
                    'birthdate'                        => $birthdate,
                    'age'                              => $age ?? ($row['age'] ?? 0),
                    'civil_status'                     => $row['civil_status'] ?? 'single',
                    'spouse'                           => $row['spouse'] ?? null,
                    'ip'                               => $row['ip'] ?? '',
                    'pwd'                              => $row['pwd'] ?? '',
                    'phone_no'                         => $row['phone_no'] ?? '',
                    'email_add'                        => $row['email_add'] ?? null,
                    'bank_name'                        => $row['bank_name'] ?? null,
                    'bank_account_no'                  => $row['bank_account_no'] ?? null,
                    'bank_branch'                      => $row['bank_branch'] ?? null,
                    'primary_beneficiaries'            => $row['primary_beneficiaries'] ?? '',
                    'primary_beneficiaries_age'        => $row['primary_beneficiaries_age'] ?? 0,
                    'primary_beneficiaries_relationship' => $row['primary_beneficiaries_relationship'] ?? '',
                    'secondary_beneficiaries'          => $row['secondary_beneficiaries'] ?? '',
                    'secondary_beneficiaries_age'      => $row['secondary_beneficiaries_age'] ?? 0,
                    'secondary_beneficiaries_relationship' => $row['secondary_beneficiaries_relationship'] ?? '',
                    'assignee'                         => $row['assignee'] ?? null,
                    'reason_assignment'                => $row['reason_assignment'] ?? null,
                ];

                // Use app_no as unique key if present, otherwise just create
                if (!empty($row['app_no'])) {
                    $farmer = Farmer::updateOrCreate(
                        ['app_no' => $row['app_no']],
                        $farmerData
                    );
                } else {
                    $farmer = Farmer::create($farmerData);
                }

                // Create farm record if farm data is present
                if (!empty($row['farm_name']) || !empty($row['lot_hectare']) || !empty($row['variety'])) {
                    $farmData = [
                        'farmer_id'           => $farmer->id,
                        'name'                => $row['farm_name'] ?? '',
                        'lot_hectare'         => $row['lot_hectare'] ?? '',
                        'sitio'               => $row['farm_sitio'] ?? '',
                        'barangay'            => $row['farm_barangay'] ?? ($row['barangay'] ?? ''),
                        'municipality'        => $row['farm_municipality'] ?? ($row['municipality'] ?? ''),
                        'province'            => $row['farm_province'] ?? ($row['province'] ?? 'Davao de Oro'),
                        'latitude'            => $row['latitude'] ?? '',
                        'longitude'           => $row['longitude'] ?? '',
                        'north'               => $row['north'] ?? '',
                        'south'               => $row['south'] ?? '',
                        'east'                => $row['east'] ?? '',
                        'west'                => $row['west'] ?? '',
                        'variety'             => $row['variety'] ?? '',
                        'planning_method'     => $row['planning_method'] ?? null,
                        'date_of_sowing'      => $row['date_of_sowing'] ?? null,
                        'date_of_planning'    => $row['date_of_planning'] ?? '',
                        'date_of_harvest'     => $row['date_of_harvest'] ?? null,
                        'population_density'  => $row['population_density'] ?? null,
                        'age_group'           => $row['age_group'] ?? '',
                        'no_of_hills'         => $row['no_of_hills'] ?? '',
                        'land_category'       => $row['land_category'] ?? null,
                        'soil_type'           => $row['soil_type'] ?? null,
                        'topography'          => $row['topography'] ?? null,
                        'source_of_irrigation' => $row['source_of_irrigation'] ?? null,
                        'tenurial_status'     => $row['tenurial_status'] ?? null,
                    ];

                    Farm::create($farmData);
                }

                $this->importedCount++;

            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Row {$rowNumber}: {$e->getMessage()}";
                Log::warning("Excel import error at row {$rowNumber}: {$e->getMessage()}");
            }
        }
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric date serial
        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                )->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try common date formats
        $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'M d, Y', 'd-m-Y', 'm-d-Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Last resort: let Carbon parse it
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
