<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FarmerImportTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            // Farmer Information
            'app_no',
            'user_type',
            'agency',
            'date_of_application',
            'funding_source',
            'crop',
            'province',
            'lastname',
            'firstname',
            'middlename',
            'municipality',
            'barangay',
            'purok',
            'sex',
            'birthdate',
            'age',
            'civil_status',
            'spouse',
            'ip',
            'pwd',
            'phone_no',
            'email_add',
            'bank_name',
            'bank_account_no',
            'bank_branch',
            'primary_beneficiaries',
            'primary_beneficiaries_age',
            'primary_beneficiaries_relationship',
            'secondary_beneficiaries',
            'secondary_beneficiaries_age',
            'secondary_beneficiaries_relationship',
            'assignee',
            'reason_assignment',

            // Farm Information
            'farm_name',
            'lot_hectare',
            'farm_sitio',
            'farm_barangay',
            'farm_municipality',
            'farm_province',
            'latitude',
            'longitude',
            'north',
            'south',
            'east',
            'west',
            'variety',
            'planning_method',
            'date_of_sowing',
            'date_of_planning',
            'date_of_harvest',
            'population_density',
            'age_group',
            'no_of_hills',
            'land_category',
            'soil_type',
            'topography',
            'source_of_irrigation',
            'tenurial_status',
        ];
    }

    public function array(): array
    {
        // Sample row to guide users
        return [
            [
                'APP-2026-001',          // app_no
                'farmer',                // user_type
                '',                      // agency
                '2026-01-15',            // date_of_application
                'Self-Financed',         // funding_source
                'Coffee',                // crop
                'Davao de Oro',          // province
                'Dela Cruz',             // lastname
                'Juan',                  // firstname
                'Santos',                // middlename
                '',                      // municipality (code)
                '',                      // barangay (code)
                '',                      // purok (code)
                'Male',                  // sex
                '1990-05-15',            // birthdate
                '35',                    // age
                'married',               // civil_status
                'Maria Dela Cruz',       // spouse
                'Non-IP',                // ip
                'None',                  // pwd
                '09171234567',           // phone_no
                'juan@email.com',        // email_add
                'BDO',                   // bank_name
                '1234567890',            // bank_account_no
                'Nabunturan Branch',     // bank_branch
                'Maria Dela Cruz',       // primary_beneficiaries
                '30',                    // primary_beneficiaries_age
                'Spouse',                // primary_beneficiaries_relationship
                'Pedro Dela Cruz',       // secondary_beneficiaries
                '10',                    // secondary_beneficiaries_age
                'Son',                   // secondary_beneficiaries_relationship
                '',                      // assignee
                '',                      // reason_assignment

                // Farm Information
                'Dela Cruz Farm',        // farm_name
                '2.5',                   // lot_hectare
                'Sitio Mahayag',         // farm_sitio
                '',                      // farm_barangay
                '',                      // farm_municipality
                'Davao de Oro',          // farm_province
                '7.5857',                // latitude
                '125.9653',              // longitude
                'River',                 // north
                'Road',                  // south
                'Mountain',              // east
                'Creek',                 // west
                'Robusta',               // variety
                'Direct Seeding',        // planning_method
                '2025-06-01',            // date_of_sowing
                '2025-06-15',            // date_of_planning
                '2026-01-15',            // date_of_harvest
                '2000',                  // population_density
                'Mature',                // age_group
                '500',                   // no_of_hills
                'Alienable & Disposable',// land_category
                'Clay Loam',             // soil_type
                'Hilly',                 // topography
                'Rainfed',               // source_of_irrigation
                'Owned',                 // tenurial_status
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '16A34A'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 14, 'C' => 14, 'D' => 20, 'E' => 18,
            'F' => 12, 'G' => 16, 'H' => 16, 'I' => 16, 'J' => 16,
            'K' => 16, 'L' => 16, 'M' => 14, 'N' => 10, 'O' => 16,
            'P' => 8,  'Q' => 14, 'R' => 18, 'S' => 12, 'T' => 10,
            'U' => 16, 'V' => 20, 'W' => 14, 'X' => 18, 'Y' => 20,
            'Z' => 22, 'AA' => 26, 'AB' => 30, 'AC' => 22,
            'AD' => 26, 'AE' => 34, 'AF' => 14, 'AG' => 20,
            'AH' => 18, 'AI' => 14, 'AJ' => 16, 'AK' => 18,
            'AL' => 20, 'AM' => 18, 'AN' => 12, 'AO' => 12,
            'AP' => 12, 'AQ' => 12, 'AR' => 12, 'AS' => 12,
            'AT' => 14, 'AU' => 18, 'AV' => 18, 'AW' => 18,
            'AX' => 18, 'AY' => 18, 'AZ' => 14, 'BA' => 14,
            'BB' => 16, 'BC' => 14, 'BD' => 14, 'BE' => 20,
            'BF' => 16,
        ];
    }
}
