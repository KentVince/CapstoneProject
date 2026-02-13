<?php

namespace App\Filament\Resources\AgriculturalProfessionalResource\Pages;

use App\Filament\Resources\AgriculturalProfessionalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgriculturalProfessionals extends ListRecords
{
    protected static string $resource = AgriculturalProfessionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
