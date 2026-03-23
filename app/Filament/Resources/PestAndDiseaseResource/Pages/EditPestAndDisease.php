<?php

namespace App\Filament\Resources\PestAndDiseaseResource\Pages;

use App\Filament\Resources\PestAndDiseaseResource;
use App\Models\PestAndDisease;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPestAndDisease extends EditRecord
{
    protected static string $resource = PestAndDiseaseResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $model = $this->record;
        if ($model instanceof PestAndDisease && $model->validation_status === 'pending' && is_null($model->admin_viewed_at)) {
            $model->updateQuietly(['admin_viewed_at' => now()]);
        }
        // Mark related header notifications as read
        auth()->user()?->unreadNotifications()
            ->where('data', 'like', '%detail-modal=' . $model->case_id . '%')
            ->update(['read_at' => now()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
