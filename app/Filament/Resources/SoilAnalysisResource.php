<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoilAnalysisResource\Pages;
use App\Models\SoilAnalysis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SoilAnalysisResource extends Resource
{
    protected static ?string $model = SoilAnalysis::class;

    protected static ?string $navigationIcon  = 'heroicon-o-globe-asia-australia';
    protected static ?string $navigationLabel = 'Soil Analysis';
    protected static ?string $pluralLabel     = 'Soil Analyses';
    protected static ?string $navigationGroup = 'Soil Fertility';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('SoilAnalysisTabs')
                ->tabs([
                    // ————————————————————— Farmer / Farm —————————————————————
                    Tab::make('Farmer & Farm')
                        ->schema([
                            Grid::make(3)->schema([
                                // Assumes SoilAnalysis::farmer() -> belongsTo(Farmer::class)
                                Forms\Components\Select::make('farmer_id')
                                    ->label('Farmer')
                                    ->relationship('farmer', 'app_no') // or use 'lastname' etc.
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                // Assumes SoilAnalysis::farm() -> belongsTo(Farm::class)
                                Forms\Components\Select::make('farm_id')
                                    ->label('Farm')
                                    ->relationship('farm', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('farm_name')
                                    ->label('Farm Name (optional override)')
                                    ->maxLength(255),
                            ]),
                            Grid::make(3)->schema([
                                Forms\Components\Select::make('soil_type')
                                    ->options([
                                        'Clay' => 'Clay',
                                        'Loam' => 'Loam',
                                        'Sandy Loam' => 'Sandy Loam',
                                        'Silty Clay' => 'Silty Clay',
                                        'Other' => 'Other',
                                    ])
                                    ->native(false)
                                    ->required(),

                                Forms\Components\Select::make('crop_variety')
                                    ->label('Crop Variety')
                                    ->options([
                                        'Coffee - Arabica' => 'Coffee - Arabica',
                                        'Coffee - Robusta' => 'Coffee - Robusta',
                                        'Coffee - Excelsa/Liberica' => 'Coffee - Excelsa/Liberica',
                                        'Banana' => 'Banana',
                                        'Cacao' => 'Cacao',
                                        'Corn' => 'Corn',
                                        'Rice' => 'Rice',
                                        'Coconut' => 'Coconut',
                                        'Other' => 'Other',
                                    ])
                                    ->native(false)
                                    ->required(),

                                Forms\Components\TextInput::make('location')
                                    ->label('Sampling Location (GPS or Address)')
                                    ->maxLength(255),
                            ]),
                            Grid::make(3)->schema([
                                Forms\Components\DatePicker::make('date_collected')
                                    ->label('Date Collected')
                                    ->native(false),
                            ]),
                        ]),

                    // ————————————————————— Lab Information —————————————————————
                    Tab::make('Lab Information')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('ref_no')
                                    ->label('Reference No.')
                                    ->default(fn () => 'REF-'.strtoupper(Str::random(6)))
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('submitted_by')
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('date_submitted')
                                    ->native(false),
                            ]),
                            Grid::make(3)->schema([
                                Forms\Components\DatePicker::make('date_analyzed')
                                    ->native(false),

                                Forms\Components\TextInput::make('lab_no')
                                    ->label('Laboratory No.')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('field_no')
                                    ->label('Field No.')
                                    ->maxLength(255),
                            ]),
                        ]),

                    // ————————————————————— Soil Properties —————————————————————
                    Tab::make('Soil Properties')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('ph_level')
                                    ->label('Soil pH')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('pH'),

                                Forms\Components\TextInput::make('organic_matter')
                                    ->label('Organic Matter')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('%'),
                            ]),
                        ]),

                    // ————————————————————— Nutrients —————————————————————
                    Tab::make('Nutrients (NPK & P)')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('nitrogen')
                                    ->label('Nitrogen (N)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('phosphorus')
                                    ->label('Phosphorus (P)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('ppm'),

                                Forms\Components\TextInput::make('potassium')
                                    ->label('Potassium (K)')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step('0.01')
                                    ->suffix('ppm'),
                            ]),
                        ]),

                    // ————————————————————— Recommendation —————————————————————
                    Tab::make('Recommendation')
                        ->schema([
                            Forms\Components\Textarea::make('recommendation')
                                ->rows(6)
                                ->autosize()
                                ->helperText('Summarize fertilizer rates, timing, condition-specific advice, etc.'),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
        

                Tables\Columns\TextColumn::make('farm_name')
                    ->label('Farm Name')
                    ->toggleable()
                    ->searchable(),

                      Tables\Columns\TextColumn::make('submitted_by')
                    ->label('Submitted By')
                    ->toggleable(),

                     Tables\Columns\TextColumn::make('date_collected')
                    ->date()
                    ->label('Date Collected'),

                Tables\Columns\TextColumn::make('date_analyzed')
                    ->date()
                    ->label('Date Analyzed'),



                // Attributes
                Tables\Columns\TextColumn::make('soil_type')
                    ->badge()
                    ->sortable(),

              

             
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('soil_type')
                    ->options([
                        'Clay' => 'Clay',
                        'Loam' => 'Loam',
                        'Sandy Loam' => 'Sandy Loam',
                        'Silty Clay' => 'Silty Clay',
                        'Other' => 'Other',
                    ]),
                Tables\Filters\TrashedFilter::make()
                    ->hidden(fn () => !in_array(SoftDeletingScope::class, class_uses_recursive(SoilAnalysis::class))),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSoilAnalyses::route('/'),
            'create' => Pages\CreateSoilAnalysis::route('/create'),
            'edit'   => Pages\EditSoilAnalysis::route('/{record}/edit'),
        ];
    }
}
