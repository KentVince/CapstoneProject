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
        $data['farmer_id'] =   $data['farmer_id'] ;
        return $data;
    }

    
}
