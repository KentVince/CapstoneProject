<?php

namespace App\Filament\Resources\AgriculturalProfessionalResource\Pages;

use App\Models\AgriculturalProfessional;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProfessionalRegistered;
use Filament\Notifications\Notification;
use App\Filament\Resources\AgriculturalProfessionalResource;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;

class CreateAgriculturalProfessional extends CreateRecord
{
    protected static string $resource = AgriculturalProfessionalResource::class;
    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): AgriculturalProfessional
    {
        DB::beginTransaction();

        try {
            $data['app_no'] = $this->generateAppNo();

            if (!empty($data['birthdate'])) {
                $data['age'] = now()->diffInYears(Carbon::parse($data['birthdate']));
            }

            $professional = AgriculturalProfessional::create($data);

            DB::commit();

            // Send registration email if professional has an email address
            if (!empty($professional->email_add)) {
                try {
                    Mail::to($professional->email_add)->send(new ProfessionalRegistered($professional));

                    Notification::make()
                        ->title('Professional Registered')
                        ->body("Agricultural Professional <b>{$professional->firstname} {$professional->lastname}</b> saved successfully. Registration email sent to <b>{$professional->email_add}</b>.")
                        ->success()
                        ->send();
                } catch (\Throwable $mailError) {
                    Log::warning("Email sending failed for Professional {$professional->app_no}: " . $mailError->getMessage());

                    Notification::make()
                        ->title('Professional Registered')
                        ->body("Agricultural Professional <b>{$professional->firstname} {$professional->lastname}</b> saved successfully, but the email could not be sent.")
                        ->warning()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Professional Registered')
                    ->body("Agricultural Professional <b>{$professional->firstname} {$professional->lastname}</b> saved successfully with QR code.")
                    ->success()
                    ->send();
            }

            return $professional;

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Agricultural Professional creation failed: ' . $th->getMessage());

            Notification::make()
                ->title('Error Creating Record')
                ->body('Something went wrong: ' . $th->getMessage())
                ->danger()
                ->send();

            throw $th;
        }
    }

    protected function generateAppNo(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->format('m');
        $prefix = 'AP';

        $last = AgriculturalProfessional::where('app_no', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('created_at', 'desc')
            ->first();

        $series = 1;
        if ($last && $last->app_no) {
            $parts = explode('-', $last->app_no);
            $series = (int) end($parts) + 1;
        }

        return "{$prefix}-{$year}-{$month}-" . sprintf('%05d', $series);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
