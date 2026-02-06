<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Models\Farmer;
use App\Models\Farm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Notifications\Notification;
use App\Filament\Resources\FarmerResource;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\Operation\HasControl;

class CreateFarmer extends CreateRecord
{
    use HasControl;

    protected static string $resource = FarmerResource::class;
    protected static bool $canCreateAnother = false;

    /**
     * Override the record creation handler to prevent Filament double-save.
     */
    protected function handleRecordCreation(array $data): Farmer
    {
        return $this->saveFarmer($data);
    }

    /**
     * Custom logic to save only Farmer + Farm (no User record).
     */
    protected function saveFarmer(array $data): Farmer
    {
        DB::beginTransaction();

        try {
            // ✅ Required auto-fill fields
            $data['app_no'] = $this->generateControlNumber('COF');
            $data['crop'] = 'Coffee';
            $data['province'] = 'Davao de Oro';

            // ✅ Create Farmer
            $farmer = Farmer::create($data);

            // ✅ Auto-create linked Farm record
            Farm::create([
                'farmer_id'   => $farmer->id,
                'name'        => $data['name'] ?? 'Unnamed Farm',
                'barangay'    => $data['barangay'] ?? null,
                'municipality'=> $data['municipality'] ?? null,
                'province'    => $data['province'] ?? 'Davao de Oro',
                'lot_hectare' => $data['lot_hectare'] ?? null,
            ]);

            // ✅ Generate QR Code for the farmer
            $this->generateFarmerQr($farmer);

            DB::commit();

            Notification::make()
                ->title('✅ Farmer Registered')
                ->body("Farmer <b>{$farmer->firstname} {$farmer->lastname}</b> saved successfully with QR code.")
                ->success()
                ->send();

            return $farmer;

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('❌ Farmer creation failed: ' . $th->getMessage());

            Notification::make()
                ->title('⚠️ Error Creating Farmer')
                ->body('Something went wrong: ' . $th->getMessage())
                ->danger()
                ->send();

            throw $th;
        }
    }

    /**
     * Generate and store a QR code for the farmer.
     */
    protected function generateFarmerQr(Farmer $farmer): void
    {
        try {
            $qrFolder = 'public/qrcodes';
            $relativeFolder = 'qrcodes';

            if (!Storage::exists($qrFolder)) {
                Storage::makeDirectory($qrFolder);
            }

            $filename = "{$farmer->app_no}.png";
            $qrPath = "{$relativeFolder}/{$filename}";
            $fullPath = Storage::path("public/{$qrPath}");

            QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate(
                    "CAFARM Farmer: {$farmer->app_no}\nName: {$farmer->firstname} {$farmer->lastname}",
                    $fullPath
                );

            $farmer->update(['qr_code' => $qrPath]);
        } catch (\Throwable $th) {
            Log::error("⚠️ QR generation failed for {$farmer->app_no}: {$th->getMessage()}");
        }
    }
}
