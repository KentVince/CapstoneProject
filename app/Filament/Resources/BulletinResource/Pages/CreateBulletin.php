<?php

namespace App\Filament\Resources\BulletinResource\Pages;

use App\Filament\Resources\BulletinResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBulletin extends CreateRecord
{
    protected static string $resource = BulletinResource::class;

    /**
     * Automatically attach creator info and mark as not yet notified.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->name ?? 'System';
        $data['notification_sent'] = false;
        $data['date_posted'] = now();
        return $data;
    }

    // FCM notification is handled by BulletinObserver::created()

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
