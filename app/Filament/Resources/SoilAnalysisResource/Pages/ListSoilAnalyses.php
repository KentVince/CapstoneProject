<?php

namespace App\Filament\Resources\SoilAnalysisResource\Pages;

use App\Filament\Resources\SoilAnalysisResource;
use App\Imports\SoilAnalysisImport;
use App\Models\Farm;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListSoilAnalyses extends ListRecords
{
    protected static string $resource = SoilAnalysisResource::class;

    /** Captured from ?farm_id= on mount; serialized in Livewire state so polls keep the filter */
    public ?int $filteredFarmId = null;

    /** Captured from ?viewRecord= on mount */
    public ?int $filteredViewRecord = null;

    public function mount(): void
    {
        $this->filteredFarmId     = request()->query('farm_id')    ? (int) request()->query('farm_id')    : null;
        $this->filteredViewRecord = request()->query('viewRecord') ? (int) request()->query('viewRecord') : null;
        parent::mount();
    }

    public function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if ($this->filteredFarmId && $query) {
            $query->where('soil_analysis.farm_id', $this->filteredFarmId);
        }

        return $query;
    }

    // Auto-refresh table every 15 seconds when Flutter syncs new data
    protected function getTablePollingInterval(): ?string
    {
        return '15s';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importSoilAnalysis')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Soil Analysis from Excel')
                ->modalDescription('Upload an Excel file (.xlsx) with soil analysis data. Existing records with the same Sample ID will be skipped.')
                ->modalSubmitActionLabel('Import')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel File (.xlsx)')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->maxSize(10240)
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data): void {
                    $file     = $data['file'];
                    $importer = new SoilAnalysisImport();

                    Excel::import($importer, $file);

                    $body = "Imported: {$importer->importedCount} | Skipped: {$importer->skippedCount}";
                    if ($importer->errors) {
                        $body .= "\n" . implode("\n", array_slice($importer->errors, 0, 5));
                        if (count($importer->errors) > 5) {
                            $body .= "\n... and " . (count($importer->errors) - 5) . ' more.';
                        }
                    }

                    if ($importer->importedCount > 0) {
                        Notification::make()
                            ->title('Import Successful')
                            ->body($body)
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Nothing Imported')
                            ->body($body)
                            ->warning()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->color('gray')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->successRedirectUrl(fn ($record) => SoilAnalysisResource::getUrl('index') . '?viewRecord=' . $record->id),
        ];
    }

    public function getSubheading(): ?string
    {
        if ($this->filteredFarmId) {
            $farm = Farm::find($this->filteredFarmId);
            if ($farm) {
                return "Filtered by farm: {$farm->farm_name}";
            }
        }

        return null;
    }

    public function getFooter(): ?View
    {
        if ($this->filteredViewRecord) {
            return view('filament.resources.soil-analysis.auto-open-modal', [
                'recordId' => $this->filteredViewRecord,
                'scrollTo' => request()->query('scrollTo'),
            ]);
        }

        return null;
    }

    /**
     * Explicitly mark all unread notifications related to this record as read.
     */
    public function markRelatedNotificationsRead(int $recordId): void
    {
        auth()->user()?->unreadNotifications()
            ->where('data', 'like', '%viewRecord=' . $recordId . '%')
            ->update(['read_at' => now()]);
    }
}
