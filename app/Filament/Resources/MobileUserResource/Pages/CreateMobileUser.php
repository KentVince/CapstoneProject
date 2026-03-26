<?php

namespace App\Filament\Resources\MobileUserResource\Pages;

use App\Filament\Resources\MobileUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMobileUser extends CreateRecord
{
    protected static string $resource = MobileUserResource::class;


     /**
     * Optional: confirm note about hashing before create (UX nice-to-have)
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Mobile user created successfully';
    }
}
