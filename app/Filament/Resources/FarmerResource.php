<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Farmer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FarmerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FarmerResource\RelationManagers;

class FarmerResource extends Resource
{
    protected static ?string $model = Farmer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Wizard::make()
                ->columnSpanFull() // Make the wizard take up the full width
                ->schema([
                    // Step 1: Basic Information
                    Forms\Components\Wizard\Step::make('Basic Information')
                        ->description('Provide the farmer\'s basic information')
                        ->schema([
                            Forms\Components\TextInput::make('crop')
                                ->hidden()
                                ->dehydrated()
                                ->default('Coffee'),
        
                            Forms\Components\DatePicker::make('date_of_application')
                                ->hidden()
                                ->dehydrated(),

                            Forms\Components\Select::make('funding_source')
                                ->searchable()
                                ->required()
                                ->options([
                                    'Self-Financed' => 'Self-Financed',
                                    'Borrowing' => 'Borrowing',
                                    'Lender' => 'Lender',
                                ]),
        
                            Forms\Components\TextInput::make('lastname')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('firstname')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('middlename')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('street')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('barangay')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('municipality')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('province')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('phone_no')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Select::make('sex')
                                ->options([
                                    'Male' => 'Male',
                                    'Female' => 'Female',
                                ])
                                ->searchable()
                                ->required(),

                            Forms\Components\DatePicker::make('birthdate')
                                ->required(),

                            Forms\Components\TextInput::make('age')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Select::make('civil_status')
                                ->options([
                                    'single' => 'Single',
                                    'married' => 'Married',
                                    'widowed' => 'Widowed',
                                    'separated' => 'Separated',
                                    'divorced' => 'Divorced',
                                ])
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('pwd')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('ip')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('spouse')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(3), // Layout: 3 columns

                    // Step 2: Bank Information
                    Forms\Components\Wizard\Step::make('Bank Information')
                        ->description('Enter the farmer\'s bank details')
                        ->schema([
                            Forms\Components\TextInput::make('bank_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('bank_account_no')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('bank_branch')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(3), // Layout: 3 columns

                    // Step 3: Beneficiaries
                    Forms\Components\Wizard\Step::make('Beneficiaries')
                        ->description('Provide information about the farmer\'s beneficiaries')
                        ->schema([
                            Forms\Components\TextInput::make('primary_beneficiaries')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('primary_beneficiaries_age')
                                ->label('Age')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('primary_beneficiaries_relationship')
                                ->label('Relationship')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('secondary_beneficiaries')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('secondary_beneficiaries_age')
                                ->label('Age')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('secondary_beneficiaries_relationship')
                                ->label('Relationship')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(3), // Layout: 3 columns
                ])
                ->skippable() // Allow skipping steps if needed
               
              
        ])
        ->columns(1); // Make the form take up the full width
}

    
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmers::route('/'),
         //  'create' => Pages\CreateFarmer::route('/create'),
            'edit' => Pages\EditFarmer::route('/{record}/edit'),
        ];
    }
}
