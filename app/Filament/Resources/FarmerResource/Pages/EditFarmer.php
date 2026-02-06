<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use App\Models\Farmer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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

    /**
     * ✅ After saving changes, generate QR if missing.
     */
    protected function afterSave(): void
    {
        $farmer = $this->record;

        // Only generate QR if missing or file not exists
        if (empty($farmer->qr_code) || !Storage::exists("public/{$farmer->qr_code}")) {
            try {
                $qrFolder = 'public/qrcodes';
                $relativeFolder = 'qrcodes';

                if (!Storage::exists($qrFolder)) {
                    Storage::makeDirectory($qrFolder);
                }

                $filename = "{$farmer->app_no}.png";
                $qrPath = "{$relativeFolder}/{$filename}";
                $fullPath = Storage::path("public/{$qrPath}");

                // Generate QR code image
                QrCode::format('png')
                    ->size(300)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate(
                        "CAFARM Farmer: {$farmer->app_no}\nName: {$farmer->firstname} {$farmer->lastname}",
                        $fullPath
                    );

                // Save path
                $farmer->update(['qr_code' => $qrPath]);

                Notification::make()
                    ->title('✅ QR Code Generated')
                    ->body("QR code for <b>{$farmer->app_no}</b> has been created successfully.")
                    ->success()
                    ->send();
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
