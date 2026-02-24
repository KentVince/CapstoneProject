<?php

namespace App\Filament\Resources\SoilAnalysisResource\Pages;

use Filament\Actions;
use App\Traits\Operation\HasControl;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SoilAnalysisResource;

class CreateSoilAnalysis extends CreateRecord
{
    use HasControl;
    protected static string $resource = SoilAnalysisResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure farmer_id and sample_id are preserved
        if (!isset($data['farmer_id']) || empty($data['farmer_id'])) {
            throw new \Exception('Farmer ID is required');
        }
        if (!isset($data['sample_id']) || empty($data['sample_id'])) {
            throw new \Exception('Sample ID was not generated. Please select a farm again.');
        }
        return $data;
    }

    
}
