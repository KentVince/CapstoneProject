<?php

namespace App\Filament\Resources\PestAndDiseaseResource\Pages;

use App\Filament\Resources\PestAndDiseaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePestAndDisease extends CreateRecord
{
    protected static string $resource = PestAndDiseaseResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['farmer_id'] =   $data['farmer_id'] ;
        return $data;
    }


}
