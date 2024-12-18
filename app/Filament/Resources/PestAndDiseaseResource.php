<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PestAndDisease;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PestAndDiseaseResource\Pages;
use App\Filament\Resources\PestAndDiseaseResource\RelationManagers;

class PestAndDiseaseResource extends Resource
{
    protected static ?string $model = PestAndDisease::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';
    protected static ?string $navigationLabel = 'Records';
    protected static ?string $pluralLabel = 'Records';
    protected static ?string $navigationGroup = 'Pest and Disease';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([



                Section::make()
                ->columns([
                    'md' => 1,  // normal medium monitor
                    'lg' => 2,  // my small monitor
                    '2xl' => 2, // my large monitor
                ])
            ->schema([


                Forms\Components\Select::make('farm_id')
                ->relationship('farm', 'name') // Dropdown shows lot_hectare from Farm table
                ->label('Farm')
                ->reactive() // Makes the field trigger changes
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        // Fetch the farmer_id of the selected farm
                        $farmerId = \App\Models\Farm::find($state)?->farmer_id;
                        $set('farmer_id', $farmerId); // Set the farmer_id in the TextInput
                    } else {
                        $set('farmer_id', null); // Clear the farmer_id if farm is unselected
                    }
                })
                ->required(),

            Forms\Components\Hidden::make('farmer_id')
                ->label('Farmer ID'),


                Forms\Components\Select::make('type')
                ->label('Type')
                ->options([
                    'Pest' => 'Pest',
                    'Disease' => 'Disease',
                ])
                ->reactive() // Makes the field trigger changes
                ->required(),

            Forms\Components\Select::make('name')
                ->label('Name')
                ->options(function (callable $get) {
                    $categoryType = $get('type'); // Get the selected category (Pest/Disease)
                    if ($categoryType) {
                        return \App\Models\PestAndDiseaseCategory::where('type', $categoryType)
                            ->pluck('name', 'name'); // Fetch names for the selected category
                    }
                    return [];
                })
                ->required()
                ->reactive() // Refresh options dynamically
                ->disabled(fn (callable $get) => !$get('type')), // Disable if no category is selected

                // Forms\Components\TextInput::make('type')->required(),
                // Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Select::make('severity')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date_detected')->required(),
                Forms\Components\TextInput::make('diagnosis_result')->required(),
                Forms\Components\TextInput::make('recommended_treatment')->required(),
                Forms\Components\TextInput::make('treatment_status')->required(),

                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->directory('pest-images')
                    ->nullable(),

                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->step(0.000001)
                    ->required(),

                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->step(0.000001)
                    ->required(),



            ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('date_detected')->date(),
            // Tables\Columns\TextColumn::make('farmer.firstname')->label('Farmer'),
            Tables\Columns\TextColumn::make('farm.name')->label('Farm Name'),

            Tables\Columns\TextColumn::make('type'),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('severity')->badge(),
        //    Tables\Columns\ImageColumn::make('image_url')->label('Image'),
            Tables\Columns\TextColumn::make('diagnosis_result'),
            Tables\Columns\TextColumn::make('recommended_treatment'),
            Tables\Columns\TextColumn::make('treatment_status'),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPestAndDiseases::route('/'),
          //  'create' => Pages\CreatePestAndDisease::route('/create'),
            'edit' => Pages\EditPestAndDisease::route('/{record}/edit'),
        ];
    }
}