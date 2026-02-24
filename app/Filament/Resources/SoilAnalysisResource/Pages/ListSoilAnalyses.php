<?php

namespace App\Filament\Resources\SoilAnalysisResource\Pages;

use App\Filament\Resources\SoilAnalysisResource;
use App\Models\Farm;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListSoilAnalyses extends ListRecords
{
    protected static string $resource = SoilAnalysisResource::class;

    // Auto-refresh table every 10 seconds when Flutter syncs new data
    protected function getTablePollingInterval(): ?string
    {
        return '10s';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('7xl'),
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

    public function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $farmId = request()->query('farm_id');
        if ($farmId) {
            $query->where('farm_id', $farmId);
        }

        return $query;
    }

    public function getFooter(): ?View
    {
        $viewRecordId = request()->query('viewRecord');

        if ($viewRecordId) {
            return view('filament.resources.soil-analysis.auto-open-modal', [
                'recordId' => $viewRecordId,
            ]);
        }

        return null;
    }
}
