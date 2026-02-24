<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Farm;
use App\Models\Farmer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use App\Filament\Pages\Componets\FarmerForm;
use App\Filament\Resources\FarmerResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FarmerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Farmer::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Farmer Information';
    protected static ?string $pluralLabel = 'Farmers';

    /**
     * Hide from panel_user (default users)
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Hide from panel users
        return !$user->hasRole('panel_user');
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view', 'view_any', 'create', 'update', 'restore', 'restore_any',
            'replicate', 'reorder', 'delete', 'delete_any',
            'force_delete', 'force_delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        $farmerForm = new FarmerForm($form->getModelInstance());

        return $form->schema([
            Section::make('Farmer Details')
                ->schema($farmerForm->getAccountSchema(false))
                ->columns(2),

            Section::make('QR Code')
                ->description('This QR Code is automatically generated based on the farmer’s Application Number.')
                ->schema([
                    ViewField::make('qr_code')
                        ->label('QR Code Preview')
                        ->view('components.qr-code')
                        ->visible(fn ($record) => filled($record?->qr_code)),
                ])
                ->collapsible(),

                
        ]);
        
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // 🟩 QR Thumbnail Column (no redirect)
            Tables\Columns\ViewColumn::make('qr_code')
                ->label('QR Code')
                ->view('components.qr-inline')
                ->alignCenter()
                ->state(fn ($record) => $record), // ✅ this passes the entire record, not just the string

            // 🟦 Clickable Application Number — goes to Edit page
            Tables\Columns\TextColumn::make('app_no')
                ->label('Application No.')
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn ($state) => strtoupper($state)),

        Tables\Columns\TextColumn::make('full_name')
            ->label('Full Name')
            ->getStateUsing(function ($record) {
                $middleInitial = $record->middlename ? strtoupper(substr($record->middlename, 0, 1)) . '.' : '';
                return "{$record->lastname}, {$record->firstname} {$middleInitial}";
            })
            ->searchable(['lastname', 'firstname', 'middlename'])
            ->sortable(['lastname', 'firstname', 'middlename']),

        Tables\Columns\TextColumn::make('municipality_name')
            ->label('Municipality')
            ->sortable()
            ->searchable(query: function ($query, $search) {
                $query->whereHas('municipalityData', function ($q) use ($search) {
                    $q->where('municipality', 'like', "%{$search}%");
                });
            }),

        Tables\Columns\TextColumn::make('barangay_name')
            ->label('Barangay')
            ->sortable()
            ->searchable(query: function ($query, $search) {
                $query->whereHas('barangayData', function ($q) use ($search) {
                    $q->where('barangay', 'like', "%{$search}%");
                });
            }),

            Tables\Columns\TextColumn::make('phone_no')
                ->label('Phone No.')
                ->sortable(),
        ])
        ->recordUrl(null) // ✅ disables full-row redirect
        ->modifyQueryUsing(function (Builder $query) {
            $user = auth()->user();
            if ($user && $user->isAgriculturalProfessional()) {
                $professional = $user->agriculturalProfessional;
                if ($professional && $professional->agency === 'MAGRO' && $professional->municipality) {
                    $query->where('municipality', $professional->municipality);
                }
            }
        })


        ->actions([
            

             Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('print_card')
                            ->label('Print Card')
                            ->icon('heroicon-o-printer')
                            ->color('success')
                            ->modalHeading('Farmer QR Card')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->modalContent(function (Farmer $record) {
                                $middleInitial = $record->middlename ? strtoupper(substr($record->middlename, 0, 1)) . '.' : '';
                                $fullName = "{$record->lastname}, {$record->firstname} {$middleInitial}";
                                $qrUrl = $record->qr_code ? asset('storage/' . $record->qr_code) : null;

                                return view('components.farmer-print-card', [
                                    'fullName' => $fullName,
                                    'appNo' => $record->app_no,
                                    'qrUrl' => $qrUrl,
                                ]);
                            }),
                        Tables\Actions\EditAction::make()
                            ->modalWidth('7xl')
                            ->mutateRecordDataUsing(function (array $data, Farmer $record): array {
                                $farm = $record->farm;
                                if ($farm) {
                                    $data = array_merge($data, $farm->toArray());
                                }
                                return $data;
                            })
                            ->using(function (array $data, Farmer $record): Farmer {
                                $record->update(array_intersect_key($data, array_flip($record->getFillable())));

                                if ($farm = $record->farm) {
                                    $farm->update([
                                        'name'         => $data['name']         ?? $farm->name,
                                        'barangay'     => $data['barangay']     ?? $farm->barangay,
                                        'municipality' => $data['municipality'] ?? $farm->municipality,
                                        'province'     => $data['province']     ?? $farm->province,
                                        'lot_hectare'  => $data['lot_hectare']  ?? $farm->lot_hectare,
                                    ]);
                                }

                                return $record->fresh();
                            })
                            ->after(function (Farmer $record): void {
                                if (empty($record->qr_code) || !Storage::exists("public/{$record->qr_code}")) {
                                    try {
                                        $qrFolder = 'public/qrcodes';
                                        if (!Storage::exists($qrFolder)) {
                                            Storage::makeDirectory($qrFolder);
                                        }
                                        $qrPath  = "qrcodes/{$record->app_no}.png";
                                        $fullPath = Storage::path("public/{$qrPath}");

                                        QrCode::format('png')
                                            ->size(300)
                                            ->margin(1)
                                            ->errorCorrection('H')
                                            ->generate(
                                                "CAFARM Farmer: {$record->app_no}\nName: {$record->firstname} {$record->lastname}",
                                                $fullPath
                                            );

                                        $record->update(['qr_code' => $qrPath]);

                                        Notification::make()
                                            ->title('QR Code Generated')
                                            ->success()
                                            ->send();
                                    } catch (\Throwable $th) {
                                        Log::error("QR generation failed for {$record->app_no}: {$th->getMessage()}");
                                        Notification::make()
                                            ->title('QR Generation Failed')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),
                        Tables\Actions\DeleteAction::make(),
                    ])
                     ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions')
                    ->button()
                    ->color('gray')
                    ->label('') // no button text




        ])
           ->actionsColumnLabel('Action') // ✅ this adds the header label

            
        

        
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index2' => Pages\ListFarmers::route('/index2'),
            'index'  => Pages\RedirectToFarmerPage::route('/'),
        ];
    }

    public static function afterSave(Farmer $record): void
    {
        if ($record->qr_code) {
            Notification::make()
                ->title('QR Code Generated Successfully ✅')
                ->body("QR for {$record->app_no} has been generated and saved.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('⚠ QR Code not generated')
                ->body('Please check storage permissions or configuration.')
                ->warning()
                ->send();
        }
    }
}
