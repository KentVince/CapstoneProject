<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Exports\FarmerImportTemplateExport;
use App\Filament\Resources\FarmerResource;
use App\Imports\FarmerFarmImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListFarmers extends ListRecords
{
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

                        // Clean up the temp file
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        // Show results
                        $message = "Successfully imported {$import->importedCount} farmer(s).";
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
                        // Clean up on error
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

            Actions\CreateAction::make(),
        ];
    }
}
