<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Farmer;
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

    protected function getFormActions(): array
    {
        return [];
    }

    public function finalSave(): void
    {
        DB::beginTransaction();
        try {
            $this->saveWithUserCreation();  // Call your save method
            DB::commit();

            session()->flash('success', 'Farmer registered successfully.');
            $this->redirect("/");
        } catch (\Throwable $th) {
            DB::rollBack();
             \Illuminate\Support\Facades\Log::error("Farmer creation failed: " . $th->getMessage());
            throw $th;
        }
    }

protected function saveWithUserCreation(): void
{
    $firstName = $this->data['firstname'] . " " . $this->data['lastname'];
    $email = $this->data['contactId']['email_add'] ?? '';

    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $firstName,
            'password' => bcrypt('11111111'),
        ]
    );

    // ✅ Actually capture the created Farmer instance
    $this->data['user_id'] = $user->id;
    $farmer = Farmer::create($this->data);

    if (!$farmer) {
        Log::warning("⚠ Failed to create Farmer record for user {$user->id}");
        return;
    }

    // ✅ Relink user
    $user->farmer_id = $farmer->id;
    $user->assignRole('panel_user');
    $user->save();

 // ✅ Generate and store QR Code safely
// ✅ Generate and store QR Code safely (no Log calls)
try {
    $qrFolder = 'public/qrcodes';
    $relativeFolder = 'qrcodes';

    // Ensure folder exists
    if (!Storage::exists($qrFolder)) {
        Storage::makeDirectory($qrFolder);
    }

    // Define file path and public URL
    $filename = "{$farmer->app_no}.png";
    $qrPath = "{$relativeFolder}/{$filename}";
    $fullPath = Storage::path("public/{$qrPath}");

    // Generate QR with farmer info
    QrCode::format('png')
        ->size(300)
        ->margin(1)
        ->errorCorrection('H')
        ->generate(
            "CAFARM Farmer: {$farmer->app_no}\nName: {$farmer->firstname} {$farmer->lastname}",
            $fullPath
        );

    // Save relative path to database
    $farmer->update(['qr_code' => $qrPath]);

    // ✅ Show success notification
    \Filament\Notifications\Notification::make()
        ->title('QR Code Generated ✅')
        ->body("QR code for <b>{$farmer->app_no}</b> created successfully.")
        ->success()
        ->send();

        } catch (\Throwable $th) {
            // ⚠️ QR failed — proceed without interrupting save
            \Filament\Notifications\Notification::make()
                ->title('QR Generation Skipped ⚠️')
                ->body('The QR code could not be generated, but the farmer record was still saved.')
                ->warning()
                ->send();
        }

}


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['app_no'] = $this->generateControlNumber('COF');
        $data['crop'] = 'Coffee';
        $data['province'] = 'Davao de Oro';
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
