<?php

namespace App\Filament\Resources\AgriculturalProfessionalResource\Pages;

use App\Filament\Resources\AgriculturalProfessionalResource;
use App\Mail\ProfessionalRegistered;
use App\Models\AgriculturalProfessional;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ListAgriculturalProfessionals extends ListRecords
{
    protected static string $resource = AgriculturalProfessionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->using(function (array $data): AgriculturalProfessional {
                    DB::beginTransaction();

                    try {
                        // Generate app_no
                        $data['app_no'] = $this->generateAppNo();

                        // Calculate age from birthdate
                        if (!empty($data['birthdate'])) {
                            $data['age'] = now()->diffInYears(Carbon::parse($data['birthdate']));
                        }

                        $professional = AgriculturalProfessional::create($data);

                        DB::commit();

                        // Send registration email
                        if (!empty($professional->email_add)) {
                            try {
                                Mail::to($professional->email_add)->send(new ProfessionalRegistered($professional));

                                Notification::make()
                                    ->title('Professional Registered')
                                    ->body("Agricultural Professional <b>{$professional->firstname} {$professional->lastname}</b> saved successfully. Registration email sent to <b>{$professional->email_add}</b>.")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $mailError) {
                                Log::warning("Email failed for Professional {$professional->app_no}: " . $mailError->getMessage());

                                Notification::make()
                                    ->title('Professional Registered')
                                    ->body("Agricultural Professional <b>{$professional->firstname} {$professional->lastname}</b> saved successfully, but the email could not be sent.")
                                    ->warning()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('Professional Registered')
                                ->body("Agricultural Professional <b>{$professional->firstname} {$professional->lastname}</b> saved successfully.")
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
                }),
        ];
    }

    protected function generateAppNo(): string
    {
        $now    = Carbon::now();
        $year   = $now->year;
        $month  = $now->format('m');
        $prefix = 'AP';

        $last = AgriculturalProfessional::where('app_no', 'like', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('created_at', 'desc')
            ->first();

        $series = 1;
        if ($last && $last->app_no) {
            $parts  = explode('-', $last->app_no);
            $series = (int) end($parts) + 1;
        }

        return "{$prefix}-{$year}-{$month}-" . sprintf('%05d', $series);
    }
}
