<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MobileUserResource\Pages;
use App\Models\MobileUser;
use App\Models\Farmer;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;

class MobileUserResource extends Resource
{
    protected static ?string $model = MobileUser::class;
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Mobile App Users';
    protected static ?string $pluralLabel = 'Mobile Users';
    protected static ?int $navigationSort = 4;
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // 🔗 Farmer selector dropdown
            Select::make('farmer_id')
                ->label('Select Farmer')
                ->relationship('farmer', 'lastname')
                ->preload()
                ->searchable()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    // Auto-fill app_no when farmer is selected
                    $farmer = Farmer::find($state);
                    $set('app_no', $farmer?->app_no);
                    $set('full_name', trim("{$farmer->firstname} {$farmer->middlename}. {$farmer->lastname}"));
                    $set('contact_no', $farmer?->phone_no);
                    $set('email', $farmer?->email);
                })
                ->helperText('Select a farmer record to link with this mobile user.')
                ->required(),

            TextInput::make('app_no')
                ->label('Application No.')
                ->disabled()
                ->dehydrated(),

            TextInput::make('type')
                ->label('User Type')
                ->default('farmer')
                ->readOnly(),

            TextInput::make('username')
                ->label('Username')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->default(Hash::make('cafarm123')) // ✅ Default password here
                ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

            TextInput::make('farm_name')
                ->label('Farm Name')
                ->disabled()
                ->dehydrated(),

            TextInput::make('barangay')
                ->label('Barangay')
                ->disabled()
                ->dehydrated(),

            TextInput::make('contact_no')
                ->label('Contact Number')
                ->disabled()
                ->dehydrated(),

            TextInput::make('email')
                ->label('Email')
                ->disabled()
                ->dehydrated(),

            TextInput::make('farm_location')
                ->label('Farm Location')
                ->nullable(),

            TextInput::make('farm_size')
                ->label('Farm Size (ha)')
                ->nullable(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')->sortable(),
                TextColumn::make('username')->searchable(),
                TextColumn::make('app_no')->label('Application No.'),
                TextColumn::make('farm_name')->label('Farm Name')->searchable(),
                TextColumn::make('type')->badge()->color(fn($state) => $state === 'farmer' ? 'success' : 'gray'),
                TextColumn::make('barangay')->label('Barangay'),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Created'),
            ])
            ->filters([])
            // ->actions([
            //     Tables\Actions\EditAction::make(),
            //     Tables\Actions\DeleteAction::make(),
            // ])

                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMobileUsers::route('/'),
           // 'create' => Pages\CreateMobileUser::route('/create'),
          //  'edit' => Pages\EditMobileUser::route('/{record}/edit'),

            
        ];
    }
}
