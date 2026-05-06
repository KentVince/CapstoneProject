<?php

namespace App\Filament\Resources\AgriculturalProfessionalResource\Pages;

use App\Filament\Resources\AgriculturalProfessionalResource;
use App\Services\QrCodeService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EditAgriculturalProfessional extends EditRecord
{
    protected static string $resource = AgriculturalProfessionalResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $professional = $this->record;

        if (empty($professional->qr_code) || !Storage::disk('public')->exists($professional->qr_code)) {
            try {
                $filePath = "professionals_qr/{$professional->app_no}.png";
                $result = QrCodeService::generate($professional->app_no, $filePath);

                if ($result) {
                    $professional->updateQuietly(['qr_code' => $filePath]);

                    Notification::make()
                        ->title('QR Code Generated')
                        ->body("QR code for <b>{$professional->app_no}</b> has been created successfully.")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('QR Generation Failed')
                        ->body('An error occurred while creating the QR code, but the record was still saved.')
                        ->warning()
                        ->send();
                }
            } catch (\Throwable $th) {
                Notification::make()
                    ->title('QR Generation Failed')
                    ->body('An error occurred while creating the QR code, but the record was still saved.')
                    ->warning()
                    ->send();
            }
        }

        // Sync user roles to match exactly what was selected on the professional record.
        // `agri_expert` is always included (required for panel access). Any other roles
        // (like the default `panel_user`) are removed if they aren't in the selection.
        if ($professional->user_id && $professional->user) {
            try {
                $user = $professional->user;
                $selected = $professional->roles ?? [];

                $roles = array_values(array_unique(array_merge(['agri_expert'], $selected)));

                foreach ($roles as $roleName) {
                    Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                }

                $user->syncRoles($roles);

                Notification::make()
                    ->title('Roles Updated')
                    ->body('Roles for this professional now match the selection.')
                    ->success()
                    ->send();
            } catch (\Throwable $th) {
                Notification::make()
                    ->title('Role Update Warning')
                    ->body('Record was saved, but roles could not be updated: ' . $th->getMessage())
                    ->warning()
                    ->send();
            }
        }
    }
}
