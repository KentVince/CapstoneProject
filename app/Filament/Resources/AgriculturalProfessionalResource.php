<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Barangay;
use App\Models\MobileUser;
use App\Models\Municipality;
use App\Models\AgriculturalProfessional;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\QrCodeService;
use App\Filament\Resources\AgriculturalProfessionalResource\Pages;

class AgriculturalProfessionalResource extends Resource
{
    protected static ?string $model = AgriculturalProfessional::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Agricultural Professionals';
    protected static ?string $pluralLabel = 'Agricultural Professionals';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 2;

    /**
     * Only show this resource for admin users, not for agricultural professionals, agri_expert, or panel users
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Hide from agricultural professionals, agri_expert role, and panel users
        return !$user->isAgriculturalProfessional() && !$user->hasRole('panel_user') && !$user->hasRole('agri_expert');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Personal Information')
                ->schema([
                    TextInput::make('app_no')
                        ->label('Application No.')
                        ->disabled()
                        ->dehydrated()
                        ->visible(fn ($record) => $record !== null),

                    Select::make('agency')
                        ->label('Agency')
                        ->options([
                            'MAGRO' => 'MAGRO',
                            'PAGRO' => 'PAGRO',
                            'DDOSC' => 'DDOSC',
                        ])
                        ->required()
                        ->searchable(),

                    TextInput::make('lastname')
                        ->label('Last Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('firstname')
                        ->label('First Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('middlename')
                        ->label('Middle Name')
                        ->maxLength(255),

                    Select::make('sex')
                        ->label('Sex')
                        ->options([
                            'Male' => 'Male',
                            'Female' => 'Female',
                        ]),

                    DatePicker::make('birthdate')
                        ->label('Birthdate')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('age', now()->diffInYears(\Carbon\Carbon::parse($state)));
                            }
                        }),

                    TextInput::make('age')
                        ->label('Age')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(),

                    Select::make('municipality')
                        ->label('Municipality')
                        ->options(Municipality::pluck('municipality', 'code'))
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('barangay', null)),

                    Select::make('barangay')
                        ->label('Barangay')
                        ->options(function (Get $get) {
                            $municipalityCode = $get('municipality');
                            if (!$municipalityCode) {
                                return [];
                            }
                            return Barangay::where('muni_filter', $municipalityCode)
                                ->pluck('barangay', 'code');
                        })
                        ->searchable(),

                    TextInput::make('phone_no')
                        ->label('Phone No.')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('email_add')
                        ->label('Email Address')
                        ->email()
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make('QR Code')
                ->description('This QR Code is automatically generated based on the Application Number.')
                ->schema([
                    ViewField::make('qr_code')
                        ->label('QR Code Preview')
                        ->view('components.qr-code'),
                ])
                ->collapsible()
                ->visible(fn ($record) => $record !== null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('qr_code')
                    ->label('QR Code')
                    ->view('components.qr-inline')
                    ->alignCenter()
                    ->state(fn ($record) => $record),

                Tables\Columns\TextColumn::make('app_no')
                    ->label('Application No.')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->url(fn ($record): string => AgriculturalProfessionalResource::getUrl('edit', ['record' => $record])),

                Tables\Columns\TextColumn::make('agency')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(function ($record) {
                        $middleInitial = $record->middlename ? strtoupper(substr($record->middlename, 0, 1)) . '.' : '';
                        return "{$record->lastname}, {$record->firstname} {$middleInitial}";
                    })
                    ->searchable(['lastname', 'firstname', 'middlename']),

                Tables\Columns\TextColumn::make('municipality')
                    ->label('Municipality')
                    ->formatStateUsing(function ($state) {
                        return Municipality::where('code', $state)->value('municipality') ?? $state;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone_no')
                    ->label('Phone No.')
                    ->sortable(),
            ])
            ->recordUrl(null)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('print_card')
                        ->label('Print Card')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->modalHeading('Professional QR Card')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalContent(function (AgriculturalProfessional $record) {
                            $middleInitial = $record->middlename ? strtoupper(substr($record->middlename, 0, 1)) . '.' : '';
                            $fullName = "{$record->lastname}, {$record->firstname} {$middleInitial}";
                            $qrUrl = $record->qr_code ? asset('storage/' . $record->qr_code) : null;

                            return view('components.farmer-print-card', [
                                'fullName' => $fullName,
                                'appNo' => $record->app_no,
                                'qrUrl' => $qrUrl,
                                'rsbsaNo' => null,
                            ]);
                        }),
                    Tables\Actions\EditAction::make()
                        ->after(function (AgriculturalProfessional $record): void {
                            if (empty($record->qr_code) || !Storage::disk('public')->exists($record->qr_code)) {
                                try {
                                    $filePath = "professionals_qr/{$record->app_no}.png";
                                    $result = QrCodeService::generate($record->app_no, $filePath);

                                    if ($result) {
                                        $record->updateQuietly(['qr_code' => $filePath]);
                                        Notification::make()
                                            ->title('QR Code Generated')
                                            ->success()
                                            ->send();
                                    }
                                } catch (\Throwable $th) {
                                    Log::error("QR generation failed for {$record->app_no}: {$th->getMessage()}");
                                }
                            }
                        }),
                    Tables\Actions\Action::make('regenerate_qr')
                        ->label('Regenerate QR')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (AgriculturalProfessional $record): void {
                            try {
                                $filePath = "professionals_qr/{$record->app_no}.png";
                                $result = QrCodeService::generate($record->app_no, $filePath);

                                if ($result) {
                                    $record->updateQuietly(['qr_code' => $filePath]);
                                    Notification::make()
                                        ->title('QR Code Regenerated')
                                        ->body("QR code for <b>{$record->app_no}</b> has been regenerated.")
                                        ->success()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('QR Generation Failed')
                                        ->warning()
                                        ->send();
                                }
                            } catch (\Throwable $th) {
                                Log::error("QR regeneration failed for {$record->app_no}: {$th->getMessage()}");
                                Notification::make()
                                    ->title('QR Generation Failed')
                                    ->body($th->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions')
                    ->button()
                    ->color('gray')
                    ->label(''),
            ])
            ->actionsColumnLabel('Action')
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
            'index' => Pages\ListAgriculturalProfessionals::route('/'),
           // 'create' => Pages\CreateAgriculturalProfessional::route('/create'),
            'edit' => Pages\EditAgriculturalProfessional::route('/{record}/edit'),
        ];
    }
}
