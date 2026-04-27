<?php

namespace App\Filament\Traits;

use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;

trait HasFarmInfoComponents
{
    public function Step_FarmInfo(): Step
    {
        return Step::make('FarmInfo')
            ->label(new HtmlString('<span class="sm:whitespace-normal md:whitespace-pre-line md:inline">Farm Information</span>'))
            ->icon('heroicon-o-chevron-up')
            ->completedIcon('heroicon-m-hand-thumb-up')
            ->schema([

                Tabs::make('Farm Information')
                    ->tabs([

                        Tab::make('Farm Location')
                            ->icon('heroicon-o-map-pin')
                            ->columns([
                                'md'  => 1,
                                'lg'  => 2,
                                '2xl' => 3,
                            ])
                            ->schema([

                                TextInput::make('farm_name')
                                    ->label('Name of Farm'),

                                Select::make('farmer_address_prv')
                                    ->label('Province')
                                    ->options(['Davao de Oro' => 'Davao de Oro'])
                                    ->default('Davao de Oro')
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('farmer_address_mun')
                                    ->label('Municipality')
                                    ->default('01')
                                    ->options(\App\Models\Municipality::whereNotNull('code')->pluck('municipality', 'code'))
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(fn (callable $set) => $set('farmer_address_bgy', null)),

                                Select::make('farmer_address_bgy')
                                    ->label('Barangay')
                                    ->searchable()
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $municipalityCode = $get('farmer_address_mun');
                                        if ($municipalityCode) {
                                            return \App\Models\Barangay::where('muni_filter', $municipalityCode)
                                                ->whereNotNull('barangay')
                                                ->get(['code', 'barangay'])
                                                ->mapWithKeys(fn ($b) => [
                                                    $b->code => trim(explode(',', $b->barangay)[0])
                                                ])
                                                ->toArray();
                                        }
                                        return [];
                                    }),

                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->default($this->formData['latitude'] ?? null),

                                TextInput::make('longtitude')
                                    ->label('Longitude')
                                    ->default($this->formData['longtitude'] ?? null),

                            ]),

                        Tab::make('Crop Info')
                            ->icon('heroicon-o-sun')
                            ->columns([
                                'md'  => 1,
                                'lg'  => 2,
                                '2xl' => 3,
                            ])
                            ->schema([

                                TextInput::make('crop_name')
                                    ->label('Crop Name')
                                    ->default('Coffee')
                                    ->disabled()
                                    ->dehydrated(),

                                Select::make('crop_variety')
                                    ->label('Crop Variety')
                                    ->options([
                                        'Arabica'       => 'Arabica',
                                        'Robusta'       => 'Robusta',
                                        'Excelsa'       => 'Excelsa',
                                        'Liberica'      => 'Liberica',
                                        'Mixed Variety' => 'Mixed Variety',
                                    ])
                                    ->searchable(),

                                TextInput::make('crop_area')
                                    ->label('Crop Area (ha)')
                                    ->numeric(),

                                Select::make('soil_type')
                                    ->label('Soil Type')
                                    ->options([
                                        'Clay'       => 'Clay',
                                        'Loam'       => 'Loam',
                                        'Sandy Loam' => 'Sandy Loam',
                                        'Silty Clay' => 'Silty Clay',
                                        'Other'      => 'Other',
                                    ])
                                    ->searchable(),

                                Select::make('cropping')
                                    ->label('Cropping System')
                                    ->options([
                                        'Monocropping'        => 'Monocropping',
                                        'Multiple Cropping'   => 'Multiple Cropping',
                                        'Intercropping'       => 'Intercropping',
                                        'Crop Rotation'       => 'Crop Rotation',
                                        'Mixed Cropping'      => 'Mixed Cropping',
                                        'Agroforestry System' => 'Agroforestry System',
                                    ])
                                    ->searchable(),

                                Select::make('farmworker')
                                    ->label('Farm Worker')
                                    ->options([
                                        'Yes' => 'Yes',
                                        'No'  => 'No',
                                    ]),

                            ]),

                        Tab::make('Status')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->columns([
                                'md'  => 1,
                                'lg'  => 2,
                                '2xl' => 3,
                            ])
                            ->schema([

                                TextInput::make('verified_area')
                                    ->label('Verified Area'),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending'  => 'Pending',
                                        'verified' => 'Verified',
                                        'rejected' => 'Rejected',
                                    ])
                                    ->default('pending'),

                            ]),

                    ]),

            ]);
    }
}
