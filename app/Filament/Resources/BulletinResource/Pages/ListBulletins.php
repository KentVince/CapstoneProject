<?php

namespace App\Filament\Resources\BulletinResource\Pages;

use App\Filament\Resources\BulletinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListBulletins extends ListRecords
{
    protected static string $resource = BulletinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->color('gray')
                ->icon('heroicon-o-plus')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = Auth::user()->name ?? 'System';
                    return $data;
                }),
        ];
    }
}
