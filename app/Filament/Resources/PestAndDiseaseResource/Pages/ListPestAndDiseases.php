<?php

namespace App\Filament\Resources\PestAndDiseaseResource\Pages;

use App\Filament\Resources\PestAndDiseaseResource;
use App\Models\Farm;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListPestAndDiseases extends ListRecords
{
    protected static string $resource = PestAndDiseaseResource::class;

    // Auto-refresh table every 3 seconds when Flutter syncs new data
    protected function getTablePollingInterval(): ?string
    {
        return '3s';
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
