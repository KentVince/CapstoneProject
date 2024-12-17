<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SoilAnalysis;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;

use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SoilAnalysisResource\Pages;
use App\Filament\Resources\SoilAnalysisResource\RelationManagers;

class SoilAnalysisResource extends Resource
{
    protected static ?string $model = SoilAnalysis::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-asia-australia';
   
    protected static ?string $navigationLabel = 'Soil Health';
    protected static ?string $pluralLabel = 'Soil Health ';
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Tabs::make('Soil Analysis Tabs')
            ->columnSpanFull() // Name the group of tabs
                ->tabs([
                    // Tab 1: General Information
                    Tabs\Tab::make('General Information')
                        ->schema([
                          
                            Grid::make()
                            ->columns([
                                'md' => 1,  // normal medium monitor
                                'lg' => 2,  // my small monitor
                                '2xl' => 3, // my large monitor
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
                                ->label('Farmer ID')
                                ->required(),

                                
                            Forms\Components\TextInput::make('ref_no')
                                ->label('Reference Number')
                                ->required(),
                            Forms\Components\TextInput::make('submitted_by')
                                ->label('Submitted By')
                                ->required(),
                            Forms\Components\DatePicker::make('date_collected')
                                ->label('Date Collected')
                                ->required(),
                            Forms\Components\DatePicker::make('date_submitted')
                                ->label('Date Submitted')
                                ->required(),
                            Forms\Components\DatePicker::make('date_analyzed')
                                ->label('Date Analyzed')
                                ->required(),
                        ]),
                    ]),


                           

                    // Tab 2: Laboratory Details
                    Tabs\Tab::make('Laboratory Details')
                        ->schema([


                            Grid::make()
                            ->columns([
                                'md' => 1,  // normal medium monitor
                                'lg' => 2,  // my small monitor
                                '2xl' => 3, // my large monitor
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('lab_no')
                                ->label('Lab Number')
                                ->required(),
                            Forms\Components\TextInput::make('field_no')
                                ->label('Field Number')
                                ->required(),
                            Forms\Components\TextInput::make('soil_type')
                                ->label('Soil Type')
                                ->required(),
                            ]),

                         
                        ]),

                    // Tab 3: Result Analysis
                    Tabs\Tab::make('Result Analysis')
                        ->schema([

                            Grid::make()
                            ->columns([
                                'md' => 1,  // normal medium monitor
                                'lg' => 2,  // my small monitor
                                '2xl' => 3, // my large monitor
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('pH_level')
                                ->label('pH Level')
                                ->numeric()
                                ->step(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('w_om')
                                ->label('Weight Organic Matter')
                                ->numeric()
                                ->step(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('p_ppm')
                                ->label('Phosphorus (ppm)')
                                ->numeric()
                                ->step(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('k_ppm')
                                ->label('Potassium (ppm)')
                                ->numeric()
                                ->step(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('wb_om')
                                ->label('Water-Based Organic Matter')
                                ->numeric()
                                ->step(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('wb_oc')
                                ->label('Water-Based Organic Carbon')
                                ->numeric()
                                ->step(0.01)
                                ->required(),

                            ]),


                            
                        ]),

                    // Tab 4: Nutrient Requirements
                    Tabs\Tab::make('Nutrient Requirements')
                        ->schema([

                            Grid::make()
                            ->columns([
                                'md' => 1,  // normal medium monitor
                                'lg' => 2,  // my small monitor
                                '2xl' => 3, // my large monitor
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('crop_variety')
                                ->label('Crop Variety')
                                ->required(),
                            Forms\Components\TextInput::make('nutrient_req_N')
                                ->label('Nitrogen Requirement (N)')
                                ->required(),
                            Forms\Components\TextInput::make('nutrient_req_P2O3')
                                ->label('Phosphorus Requirement (P₂O₃)')
                                ->required(),
                            Forms\Components\TextInput::make('nutrient_req_K2O')
                                ->label('Potassium Requirement (K₂O)')
                                ->required(),
                            Forms\Components\TextInput::make('lime_req')
                                ->label('Lime Requirement')
                                ->required(),
                            Forms\Components\TextInput::make('pH_preference')
                                ->label('pH Preference')
                                ->required(),

                                Forms\Components\TextInput::make('organic_matter')
                                ->label('Organic Matter')
                                ->required(),

                            ]),


                            
                        ]),
                ])
                ->activeTab(1) // Default active tab is the first one
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('farmer.firstname')->label('Farmer'),
                Tables\Columns\TextColumn::make('farm.lot_hectare')->label('Farm Lot'),
                Tables\Columns\TextColumn::make('ref_no'),
                Tables\Columns\TextColumn::make('submitted_by'),
                Tables\Columns\TextColumn::make('date_collected')->date(),
                Tables\Columns\TextColumn::make('date_analyzed')->date(),
                Tables\Columns\TextColumn::make('soil_type'),
                Tables\Columns\TextColumn::make('pH_level')->sortable(),
                Tables\Columns\TextColumn::make('crop_variety'),
                Tables\Columns\TextColumn::make('nutrient_req_N'),
                Tables\Columns\TextColumn::make('nutrient_req_P2O3'),
                Tables\Columns\TextColumn::make('nutrient_req_K2O'),
                Tables\Columns\TextColumn::make('lime_req'),
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
            'index' => Pages\ListSoilAnalyses::route('/'),
           // 'create' => Pages\CreateSoilAnalysis::route('/create'),
            'edit' => Pages\EditSoilAnalysis::route('/{record}/edit'),
        ];
    }
}
