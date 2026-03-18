<?php

namespace App\Filament\Traits;

use App\Models\Farmer;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Wizard\Step;
use App\Traits\Operation\HasControl;


trait HasFarmerInfoComponents
{
    use HasControl;

    public function Step_FarmerInfo(?Farmer $farmer = null, ?bool $is_signup = false): Step
    {
        return Step::make('Farmer Info')
            ->label(fn (Get $get): HtmlString => new HtmlString(
                '<span class="sm:whitespace-normal md:whitespace-pre-line md:inline">' .
                (($get('user_type') ?? 'farmer') === 'agricultural_professional' ? 'Information' : 'Farmer Information') .
                '</span>'
            ))
            ->icon('heroicon-o-user')
            ->completedIcon('heroicon-m-hand-thumb-up')
            ->schema([

                Grid::make()
                    ->columns([
                        'md' => 1,
                        'lg' => 2,
                        '2xl' => 3,
                    ])
                    ->schema([
                        TextInput::make('app_no')
                            ->label('Application No.')
                            ->default(fn () => (new class { use HasControl; })->generateControlNumber('COF'))
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('rsbsa_no')
                            ->label('RSBSA No.')
                            ->maxLength(255),

                        Select::make('agency')
                            ->label('Agency')
                            ->searchable()
                            ->visible(fn (Get $get): bool => $get('user_type') === 'agricultural_professional')
                            ->required(fn (Get $get): bool => $get('user_type') === 'agricultural_professional')
                            ->options([
                                'MAGRO' => 'MAGRO',
                                'PAGRO' => 'PAGRO',
                                'DDOSC' => 'DDOSC',
                            ]),
                    ]),

                Tabs::make('Basic Information')
                    ->tabs([

                        Tab::make('Basic Info')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Grid::make()
                                    ->columns([
                                        'md' => 1,
                                        'lg' => 2,
                                        '2xl' => 3,
                                    ])
                                    ->schema([

                                        TextInput::make('last_name')
                                            ->label('Last Name')
                                            ->maxLength(255),

                                        TextInput::make('first_name')
                                            ->label('First Name')
                                            ->maxLength(255),

                                        TextInput::make('middle_name')
                                            ->label('Middle Name')
                                            ->maxLength(255),

                                        TextInput::make('ext_name')
                                            ->label('Extension Name (e.g. Jr., Sr., III)')
                                            ->maxLength(50),

                                        DatePicker::make('birthday')
                                            ->label('Birthday'),

                                        Select::make('gender')
                                            ->label('Gender')
                                            ->options([
                                                'Male'   => 'Male',
                                                'Female' => 'Female',
                                            ])
                                            ->searchable(),
                                    ]),
                            ]),

                        Tab::make('Address')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Grid::make()
                                    ->columns([
                                        'md' => 1,
                                        'lg' => 2,
                                        '2xl' => 3,
                                    ])
                                    ->schema([

                                        Select::make('farmer_address_prv')
                                            ->label('Province')
                                            ->options(['Davao de Oro' => 'Davao de Oro'])
                                            ->default('Davao de Oro')
                                            ->disabled()
                                            ->dehydrated(),

                                        Select::make('farmer_address_mun')
                                            ->label('Municipality')
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
                                            })
                                            ->afterStateUpdated(fn (callable $set) => $set('farmer_address_prk', null)),

                                        Select::make('farmer_address_prk')
                                            ->label('Purok / Sitio')
                                            ->searchable()
                                            ->reactive()
                                            ->options(function (callable $get) {
                                                $barangayCode = $get('farmer_address_bgy');
                                                if ($barangayCode) {
                                                    return \App\Models\Purok::where('purok_filter', $barangayCode)
                                                        ->whereNotNull('purok_sitio')
                                                        ->pluck('purok_sitio', 'id');
                                                }
                                                return [];
                                            }),
                                    ]),
                            ]),

                        Tab::make('Contact / IDs')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Grid::make()
                                    ->columns([
                                        'md' => 1,
                                        'lg' => 2,
                                        '2xl' => 3,
                                    ])
                                    ->schema([

                                        TextInput::make('contact_num')
                                            ->label('Mobile No.')
                                            ->default($this->formData['contact_num'] ?? null)
                                            ->maxLength(20),

                                        TextInput::make('email_add')
                                            ->label('Email Address')
                                            ->default($this->formData['email_add'] ?? null)
                                            ->email(),
                                    ]),
                            ]),

                    ]),
            ]);
    }
}
