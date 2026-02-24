<?php

namespace App\Filament\Resources\PestAndDiseaseResource\Pages;

use App\Filament\Resources\PestAndDiseaseResource;
use App\Models\Farm;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPestAndDiseases extends ListRecords
{
    protected static string $resource = PestAndDiseaseResource::class;

    // Auto-refresh table every 10 seconds when Flutter syncs new data
    protected function getTablePollingInterval(): ?string
    {
        return '10s';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        $farmId = request()->query('farm_id');

        if ($farmId) {
            $farm = Farm::find($farmId);
            if ($farm) {
                return "Filtered by farm: {$farm->name}";
            }
        }

        return null;
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $farmId = request()->query('farm_id');
        if ($farmId && \Illuminate\Support\Facades\Schema::hasColumn('pest_and_disease', 'farm_id')) {
            $query->where('farm_id', $farmId);
        }

        return $query;
    }
}
