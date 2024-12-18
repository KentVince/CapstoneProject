<?php

namespace App\Filament\Resources\PestAndDiseaseCategoryResource\Pages;

use App\Filament\Resources\PestAndDiseaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPestAndDiseaseCategories extends ListRecords
{
    protected static string $resource = PestAndDiseaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
