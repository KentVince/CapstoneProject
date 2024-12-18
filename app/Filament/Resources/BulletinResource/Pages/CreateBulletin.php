<?php

namespace App\Filament\Resources\BulletinResource\Pages;

use Filament\Actions;
use App\Traits\Operation\HasControl;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BulletinResource;

class CreateBulletin extends CreateRecord
{
    use HasControl;
    protected static string $resource = BulletinResource::class;

    

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['created_by'] = auth()->id(); // Set the created_by field to the logged-in user's ID
        return $data;
    }

}
