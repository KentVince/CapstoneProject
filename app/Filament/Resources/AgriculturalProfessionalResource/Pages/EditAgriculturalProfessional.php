<?php

namespace App\Filament\Resources\AgriculturalProfessionalResource\Pages;

use App\Filament\Resources\AgriculturalProfessionalResource;
use Filament\Resources\Pages\EditRecord;

class EditAgriculturalProfessional extends EditRecord
{
    protected static string $resource = AgriculturalProfessionalResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
