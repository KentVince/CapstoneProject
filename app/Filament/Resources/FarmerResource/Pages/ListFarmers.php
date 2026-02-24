<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Exports\FarmerImportTemplateExport;
use App\Filament\Resources\FarmerResource;
use App\Imports\FarmerFarmImport;
use App\Mail\FarmerRegistered;
use App\Models\Farm;
use App\Models\Farmer;
use App\Traits\Operation\HasControl;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ListFarmers extends ListRecords
{
    use HasControl;

    protected static string $resource = FarmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel File')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->required()
                        ->helperText('Upload an Excel file (.xlsx, .xls) or CSV file with farmer and farm information.'),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/' . $data['file']);

                    try {
                        $import = new FarmerFarmImport();
                        Excel::import($import, $filePath);
                        $import->collectResults();

                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        $farmersCount = $import->getFarmersImported();
                        $farmsCount   = $import->getFarmsImported();
                        $message      = "Successfully imported {$farmersCount} farmer(s) and {$farmsCount} farm(s).";
                        if ($import->skippedCount > 0) {
                            $message .= " Skipped {$import->skippedCount} row(s).";
                        }

                        if (!empty($import->errors)) {
                            $errorDetails = implode("\n", array_slice($import->errors, 0, 5));
                            Notification::make()
                                ->title('Import Completed with Warnings')
                                ->body($message . "\n\nIssues:\n" . $errorDetails)
                                ->warning()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Import Successful')
                                ->body($message)
                                ->success()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        Notification::make()
                            ->title('Import Failed')
                            ->body('Error: ' . $e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                })
                ->modalHeading('Import Farmers & Farms from Excel')
                ->modalDescription('Upload an Excel file to bulk import farmer and farm records. The file should have column headers matching the database fields.')
                ->modalSubmitActionLabel('Import'),

            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return Excel::download(
                        new FarmerImportTemplateExport(),
                        'farmer_import_template.xlsx'
                    );
                }),

            Actions\CreateAction::make()
                ->modalWidth('7xl')
                ->modalHeading('Register New Farmer')
                ->createAnother(false)
                ->using(function (array $data): Farmer {
                    DB::beginTransaction();

                    try {
                        $data['app_no']   = $this->generateControlNumber('COF');
                        $data['crop']     = 'Coffee';
                        $data['province'] = 'Davao de Oro';

                        $farmer = Farmer::create($data);

                        Farm::create([
                            'farmer_id'    => $farmer->id,
                            'name'         => $data['name']         ?? 'Unnamed Farm',
                            'barangay'     => $data['barangay']     ?? null,
                            'municipality' => $data['municipality'] ?? null,
                            'province'     => 'Davao de Oro',
                            'lot_hectare'  => $data['lot_hectare']  ?? null,
                        ]);

                        // Generate QR Code
                        try {
                            $qrFolder = 'public/qrcodes';
                            if (!Storage::exists($qrFolder)) {
                                Storage::makeDirectory($qrFolder);
                            }
                            $qrPath   = "qrcodes/{$farmer->app_no}.png";
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
                        } catch (\Throwable $qrErr) {
                            Log::warning("QR generation failed for {$farmer->app_no}: {$qrErr->getMessage()}");
                        }

                        DB::commit();

                        // Send registration email
                        if (!empty($farmer->email_add)) {
                            try {
                                Mail::to($farmer->email_add)->send(new FarmerRegistered($farmer));

                                Notification::make()
                                    ->title('Farmer Registered')
                                    ->body("Farmer <b>{$farmer->firstname} {$farmer->lastname}</b> saved. Registration email sent to <b>{$farmer->email_add}</b>.")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $mailErr) {
                                Log::warning("Email failed for {$farmer->app_no}: {$mailErr->getMessage()}");

                                Notification::make()
                                    ->title('Farmer Registered')
                                    ->body("Farmer <b>{$farmer->firstname} {$farmer->lastname}</b> saved, but the email could not be sent.")
                                    ->warning()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('Farmer Registered')
                                ->body("Farmer <b>{$farmer->firstname} {$farmer->lastname}</b> saved successfully.")
                                ->success()
                                ->send();
                        }

                        return $farmer;

                    } catch (\Throwable $th) {
                        DB::rollBack();
                        Log::error('Farmer creation failed: ' . $th->getMessage());

                        Notification::make()
                            ->title('Error Creating Farmer')
                            ->body('Something went wrong: ' . $th->getMessage())
                            ->danger()
                            ->send();

                        throw $th;
                    }
                }),
        ];
    }
}
