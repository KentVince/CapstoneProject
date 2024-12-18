<?php

namespace App\Filament\Resources\SoilAnalysisResource\Pages;

use App\Filament\Resources\SoilAnalysisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSoilAnalyses extends ListRecords
{
    protected static string $resource = SoilAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
