<?php

namespace App\Filament\Resources\SoilAnalysisResource\Pages;

use App\Filament\Resources\SoilAnalysisResource;
use App\Models\SoilAnalysis;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoilAnalysis extends EditRecord
{
    protected static string $resource = SoilAnalysisResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $model = $this->record;
        if ($model instanceof SoilAnalysis && $model->validation_status === 'pending' && is_null($model->admin_viewed_at)) {
            $model->updateQuietly(['admin_viewed_at' => now()]);
        }
        // Mark related header notifications as read
        auth()->user()?->unreadNotifications()
            ->where('data', 'like', '%viewRecord=' . $model->id . '%')
            ->update(['read_at' => now()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
