<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Farmer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use App\Filament\Pages\Componets\FarmerForm;
use App\Filament\Resources\FarmerResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Notifications\Notification;

class FarmerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Farmer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Farmer Information';
    protected static ?string $pluralLabel = 'Farmers';

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
                ->schema($farmerForm->getAccountSchema())
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
                ->color('primary')
                ->formatStateUsing(fn ($state) => strtoupper($state))
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:underline text-blue-600',
                    'title' => 'Click to edit this record',
                ])
                ->getStateUsing(fn ($record) => $record->app_no)
                ->url(fn ($record): string => FarmerResource::getUrl('edit', ['record' => $record])),

            Tables\Columns\TextColumn::make('lastname')
                ->label('Last Name')
                ->searchable(),

            Tables\Columns\TextColumn::make('firstname')
                ->label('First Name')
                ->searchable(),

            Tables\Columns\TextColumn::make('barangay')
                ->label('Barangay')
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Date Added')
                ->date('M d, Y')
                ->sortable(),
        ])
        ->recordUrl(null) // ✅ disables full-row redirect


        ->actions([
            Tables\Actions\EditAction::make(),
        ])

            
        

        
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
            'index' => Pages\RedirectToFarmerPage::route('/'),
            'create' => Pages\CreateFarmer::route('/create'),
            'edit' => Pages\EditFarmer::route('/{record}/edit'),
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
