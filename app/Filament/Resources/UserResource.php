<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Web App Users';
    protected static ?int $navigationSort = 6;

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

    // -------------------------- FORM --------------------------
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),

            Forms\Components\DateTimePicker::make('email_verified_at'),

            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                ->visibleOn('create')
                ->dehydrated(fn($state) => filled($state)),

            Forms\Components\TextInput::make('farmer_id')
                ->numeric()
                ->default(null),

            Forms\Components\Select::make('roles')
                ->label('Role(s)')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    // -------------------------- TABLE --------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(', ')
                    ->color('success'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])

            // ✅ Compact 3-dots Action Group with Role editor
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('impersonate')
                        ->label('Impersonate')
                        ->icon('heroicon-o-finger-print')
                        ->color('warning')
                        ->action(function (User $record) {
                            auth()->user()->impersonate($record);
                            return redirect()->route('filament.admin.pages.dashboard');
                        }),

                    Tables\Actions\Action::make('manage_roles')
                        ->label('Manage Roles')
                        ->icon('heroicon-m-key')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('roles')
                                ->label('Assign Role(s)')
                                ->multiple()
                                ->options(Role::all()->pluck('name', 'name'))
                                ->searchable()
                                ->preload(),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->syncRoles($data['roles'] ?? []);
                        })
                        ->modalHeading('Manage User Roles')
                        ->modalSubmitActionLabel('Save Roles')
                        ->modalIcon('heroicon-o-key')
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions')
                ->button()
                ->color('gray')
                ->label(''),
            ])
            ->actionsColumnLabel('Action') // ✅ adds “Action” header

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // -------------------------- RELATIONS --------------------------
    public static function getRelations(): array
    {
        return [];
    }

    // -------------------------- PAGES --------------------------
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
