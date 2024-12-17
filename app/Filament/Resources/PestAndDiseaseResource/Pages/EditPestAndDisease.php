<?php

namespace App\Filament\Resources\PestAndDiseaseResource\Pages;

use App\Filament\Resources\PestAndDiseaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPestAndDisease extends EditRecord
{
    protected static string $resource = PestAndDiseaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
