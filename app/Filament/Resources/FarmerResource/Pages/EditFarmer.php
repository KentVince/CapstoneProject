<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use App\Models\Farmer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use App\Services\QrCodeService;
use Filament\Notifications\Notification;

class EditFarmer extends EditRecord
{
    protected static string $resource = FarmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = Farmer::findOrFail($record)->load('farm');
        $farm = $this->record->farm;
        $farmData = $farm ? $farm->toArray() : [];

        $recordData = collect($this->record)
            ->except('farm')
            ->merge($farmData);

        $this->form->fill($recordData->toArray());
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index2');
    }

    protected function afterSave(): void
    {
        $farmer = $this->record;

        // Only generate QR if missing or file not exists
        if (empty($farmer->qr_code) || !Storage::disk('public')->exists($farmer->qr_code)) {
            try {
                $qrPath = "farmers_qr/{$farmer->app_no}.png";
                $data = "CAFARM Farmer: {$farmer->app_no}\nName: {$farmer->first_name} {$farmer->last_name}";

                $result = QrCodeService::generate($data, $qrPath);

                if ($result) {
                    $farmer->updateQuietly(['qr_code' => $qrPath]);

                    Notification::make()
                        ->title('✅ QR Code Generated')
                        ->body("QR code for <b>{$farmer->app_no}</b> has been created successfully.")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('⚠️ QR Generation Failed')
                        ->body('An error occurred while creating the QR code, but the record was still saved.')
                        ->warning()
                        ->send();
                }
            } catch (\Throwable $th) {
                Notification::make()
                    ->title('⚠️ QR Generation Failed')
                    ->body('An error occurred while creating the QR code, but the record was still saved.')
                    ->warning()
                    ->send();
            }
        }
    }
}
