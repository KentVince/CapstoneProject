<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Models\Farmer;
use App\Models\Farm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Mail;
use App\Mail\FarmerRegistered;
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
            $data['app_no'] = $this->generateControlNumber('COF');

            // ✅ Create Farmer
            $farmer = Farmer::create($data);

            // ✅ Auto-create linked Farm record
            Farm::create([
                'farmer_id'          => $farmer->id,
                'farm_name'          => $data['farm_name']          ?? 'Unnamed Farm',
                'farmer_address_bgy' => $data['farmer_address_bgy'] ?? null,
                'farmer_address_mun' => $data['farmer_address_mun'] ?? null,
                'farmer_address_prv' => $data['farmer_address_prv'] ?? 'Davao de Oro',
                'crop_name'          => $data['crop_name']          ?? null,
                'crop_variety'       => $data['crop_variety']       ?? null,
                'crop_area'          => $data['crop_area']          ?? null,
                'soil_type'          => $data['soil_type']          ?? null,
                'cropping'           => $data['cropping']           ?? null,
                'farmworker'         => $data['farmworker']         ?? null,
                'latitude'           => $data['latitude']           ?? null,
                'longtitude'         => $data['longtitude']         ?? null,
                'status'             => 'pending',
            ]);

            // ✅ Generate QR Code for the farmer
            $this->generateFarmerQr($farmer);

            DB::commit();

            // Send registration email if farmer has an email address
            if (!empty($farmer->email_add)) {
                try {
                    Mail::to($farmer->email_add)->send(new FarmerRegistered($farmer));

                    Notification::make()
                        ->title('✅ Farmer Registered')
                        ->body("Farmer <b>{$farmer->first_name} {$farmer->last_name}</b> saved successfully. Registration email sent to <b>{$farmer->email_add}</b>.")
                        ->success()
                        ->send();
                } catch (\Throwable $mailError) {
                    Log::warning("Email sending failed for Farmer {$farmer->app_no}: " . $mailError->getMessage());

                    Notification::make()
                        ->title('✅ Farmer Registered')
                        ->body("Farmer <b>{$farmer->first_name} {$farmer->last_name}</b> saved successfully, but the email could not be sent.")
                        ->warning()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('✅ Farmer Registered')
                    ->body("Farmer <b>{$farmer->first_name} {$farmer->last_name}</b> saved successfully with QR code.")
                    ->success()
                    ->send();
            }

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
        $qrPath = "qrcodes/{$farmer->app_no}.png";

        $saved = QrCodeService::generate(
            "CAFARM Farmer: {$farmer->app_no}\nName: {$farmer->first_name} {$farmer->last_name}",
            $qrPath
        );

        if ($saved) {
            $farmer->update(['qr_code' => $qrPath]);
        }
    }
}
