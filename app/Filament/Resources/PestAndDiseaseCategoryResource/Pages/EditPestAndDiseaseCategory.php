<?php

namespace App\Filament\Resources\PestAndDiseaseCategoryResource\Pages;

use App\Filament\Resources\PestAndDiseaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPestAndDiseaseCategory extends EditRecord
{
    protected static string $resource = PestAndDiseaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
