<?php

namespace App\Filament\Resources\PestAndDiseaseResource\Pages;

use App\Filament\Resources\PestAndDiseaseResource;
use App\Imports\PestAndDiseaseImport;
use App\Models\Farm;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListPestAndDiseases extends ListRecords
{
    protected static string $resource = PestAndDiseaseResource::class;

    // Auto-refresh table every 15 seconds when Flutter syncs new data
    protected function getTablePollingInterval(): ?string
    {
        return '15s';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importPestAndDisease')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->hidden()
                ->modalHeading('Import Pest & Disease from Excel')
                ->modalDescription('Upload an Excel file (.xlsx) with pest and disease data. Rows missing pest or date_detected will be skipped.')
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
                    $importer = new PestAndDiseaseImport();

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

    public function getFooter(): ?View
    {
        $viewRecordId = request()->query('viewRecord');

        if ($viewRecordId) {
            return view('filament.resources.pest-and-disease.auto-open-modal', [
                'recordId'  => $viewRecordId,
                'scrollTo'  => request()->query('scrollTo'),
            ]);
        }

        return null;
    }

    /**
     * Explicitly mark all unread notifications related to this record as read.
     * Called from auto-open-modal.blade.php on the same Livewire request,
     * bypassing any ->markAsRead() timing issues in the notification panel.
     */
    public function markRelatedNotificationsRead(int $recordId): void
    {
        auth()->user()?->unreadNotifications()
            ->where(function ($q) use ($recordId) {
                $q->where('data', 'like', '%viewRecord=' . $recordId . '%')
                  ->orWhere('data', 'like', '%detail-modal=' . $recordId . '%');
            })
            ->update(['read_at' => now()]);
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $farmId = request()->query('farm_id');
        if ($farmId && \Illuminate\Support\Facades\Schema::hasColumn('pest_and_disease', 'farm_id')) {
            $query->where('farm_id', $farmId);
        }

        return $query->orderBy('case_id', 'desc');
    }
}
