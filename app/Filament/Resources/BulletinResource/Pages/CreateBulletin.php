<?php

namespace App\Filament\Resources\BulletinResource\Pages;

use App\Filament\Resources\BulletinResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\FcmService;
use Filament\Notifications\Notification;

class CreateBulletin extends CreateRecord
{
    protected static string $resource = BulletinResource::class;

    /**
     * ✅ Automatically attach creator info and mark as not yet notified
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->name ?? 'System';
        $data['notification_sent'] = false;
        $data['date_posted'] = now();
        return $data;
    }

    /**
     * ✅ After saving bulletin, send FCM push to topic
     */
protected function afterCreate(): void
{
    $record = $this->record;

    if (! $record->notification_sent) {
        $title = $record->title;
        $body  = strip_tags(substr($record->content ?? '', 0, 120)) ?: 'New bulletin posted';

        $ok = app(\App\Services\FcmService::class)->sendToTopic(
            'all_users',
            $title,
            $body,
            [
                'bulletin_id' => (string) $record->bulletin_id,
                'category'    => (string) $record->category,
            ]
        );

        if ($ok) {
            $record->update(['notification_sent' => true]);
        }
    }
}

}
