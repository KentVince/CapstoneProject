<?php

namespace App\Filament\Traits;

use Carbon\Carbon;
use App\Models\Farm;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\ToggleButtons;
// use App\Filament\Traits\Pds\HasfarmerAddress;

trait HasFarmInfoComponents
{
 //   use HasfarmerAddress;

    public function Step_FarmInfo(?Farm $farm = null, ?bool $is_signup = false): Step
    {




                    return
                    Step::make('FarmInfo')
                    ->label(new HtmlString('<span class="sm:whitespace-normal md:whitespace-pre-line md:inline">Farm Information</span>'))
                    ->icon('heroicon-o-chevron-up')
                    // ->description('Enter your Personal Details')
                    ->completedIcon('heroicon-m-hand-thumb-up')

                    ->schema([

                            Tabs::make('Farm Information')

                                ->tabs([
                                    Tab::make('B.1.Farm Location')
                                    ->default(fn()=>$this->formData['eligibilities'] ?? [[]])
                                    ->columns([
                                        'md' => 1,  // normal medium monitor
                                        'lg' => 2,  // my small monitor
                                        '2xl' => 3, // my large monitor
                                    ])
                                    ->schema([

                                        TextInput::make('name')->label('Name of Farm')->required(),
                                        TextInput::make('lot_hectare')->required(),


                                        Select::make('province')
                                    ->options([
                                        'Davao de Oro' => 'Davao de Oro', // The key must match the default value
                                        'Davao del Sur' => 'Davao del Sur',
                                    ])
                                    ->default('Davao de Oro')
                                    ->hidden()
                                    ->disabled(),



                                    // Select::make('municipality')
                                    // ->label('Municipality')
                                    // ->required()
                                    // ->options(\App\Models\Municipality::whereNotNull('code')->pluck('municipality', 'code'))
                                    // ->reactive()
                                    // ->searchable()
                                    // ->afterStateUpdated(fn (callable $set) => $set('barangay', null)),

                                    Select::make('municipality')
                                    ->label('Municipality')
                                    ->required()
                                    ->default('01') // Set the default municipality code to '01' (Compostela)
                                    ->options(\App\Models\Municipality::whereNotNull('code')->pluck('municipality', 'code'))
                                    ->disabled() // Disable the field so it cannot be edited
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(fn (callable $set) => $set('barangay', null)),

                                    Select::make('barangay')
                                    ->label('Barangay')
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $municipalityCode = $get('municipality'); // Get the selected municipality's code
                                        if ($municipalityCode) {
                                            return \App\Models\Barangay::where('muni_filter', $municipalityCode)
                                                ->whereNotNull('barangay') // Ensure no null descriptions
                                                ->pluck('barangay', 'code');
                                        }
                                        return [];
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('purok', null)),

                                    Select::make('purok')
                                    ->label('Purok')
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->options(function (callable $get) {
                                        $barangayCode = $get('barangay'); // Get the selected municipality's code

                                        if ($barangayCode) {
                                            return \App\Models\Purok::where('purok_filter', $barangayCode)
                                                ->whereNotNull('purok_sitio') // Ensure no null descriptions
                                                ->pluck('purok_sitio', 'id');
                                        }
                                        return [];
                                    }),

                                    ])
                                    ->icon('heroicon-o-map-pin'),


                                    Tab::make('B.2.Boundaries')
                                        // ->extraAttributes(['class' => 'personal-info-test'])

                                        ->schema([


                                            Grid::make('farmernames')
                                            //     ->when($farmer != null, fn($grid) => $grid->relationship('contactId'))
                                                 ->columns([
                                                     'md' => 1,  // normal medium monitor
                                                     'lg' => 2,  // my small monitor
                                                     '2xl' => 4, // my large monitor
                                                 ])
                                                 ->label('')
                                                 ->schema([


                                                    TextInput::make('latitude')->required()
                                                    ->default($this->formData['latitude'] ?? null),
                                                    TextInput::make('longitude')->required()
                                                    ->default($this->formData['longitude'] ?? null),

                                                    TextInput::make('north')->required()
                                                    ->default($this->formData['north'] ?? null),
                                                    TextInput::make('south')->required()
                                                    ->default($this->formData['south'] ?? null),
                                                    TextInput::make('east')->required()
                                                    ->default($this->formData['east'] ?? null),
                                                    TextInput::make('west')->required()
                                                    ->default($this->formData['west'] ?? null),








                                        ]),
                                        ]) ->icon('heroicon-o-code-bracket-square'),  // end Personal Info tab




                                        Tab::make('B.3-7.Planning Method')
                                        ->columns([
                                            'md' => 1,  // normal medium monitor
                                            'lg' => 2,  // my small monitor
                                            '2xl' => 3, // my large monitor
                                        ])
                                        ->schema([



                                            TextInput::make('variety')->required()
                                            ->default($this->formData['variety'] ?? null),
                                            TextInput::make('planning_method')->required(),

                                            DatePicker::make('date_of_sowing'),
                                            DatePicker::make('date_of_planning'),
                                            TextInput::make('population_density')->required(),

                                        ])
                                        ->icon('heroicon-o-calendar-date-range'),


                                        Tab::make('B.8.Population Density ')
                                        ->columns([
                                            'md' => 1,  // normal medium monitor
                                            'lg' => 2,  // my small monitor
                                            '2xl' => 3, // my large monitor
                                        ])
                                        ->schema([




                                            TextInput::make('age_group')->required(),
                                            TextInput::make('no_of_hills')->required(),


                                        ])
                                        ->icon('heroicon-o-credit-card'),


                                        Tab::make('B.9-13.Land Category')
                                        ->columns([
                                            'md' => 1,  // normal medium monitor
                                            'lg' => 2,  // my small monitor
                                            '2xl' => 3, // my large monitor
                                        ])
                                        ->schema([



                                            TextInput::make('land_category')->required(),
                                            TextInput::make('soil_type')->required(),
                                            TextInput::make('topography')->required(),
                                            TextInput::make('source_of_irrigation')->required(),
                                            TextInput::make('tenurial_status')->required(),



                                        ])
                                        ->icon('heroicon-o-map'),











                        ])


                        ]);






    }

    // public function checkSameAsPermanent() : bool
    // {
    //     $res = $this->formData['addresses_residential'] ?? [];
    //     $per = $this->formData['addresses_permanent'] ?? [];

    //     return (!empty($res) && !empty($per) && $this->addressesMatch($res, $per));
    // }

    // protected function addressesMatch ($res, $per) : bool
    // {
    //     $filter = ['id', 'created_at', 'updated_at', 'address_type' ];

    //     $resFiltered = collect($res)->map(fn($item) => collect($item)->except($filter)->toArray())->values()->toArray();
    //     $perFiltered = collect($per)->map(fn($item) => collect($item)->except($filter)->toArray())->values()->toArray();

    //     $constNull = $this->containsOnlyNull($resFiltered) && $this->containsOnlyNull($perFiltered);

    //     return !($constNull) && ($resFiltered === $perFiltered);
    // }

    // protected function containsOnlyNull(array $arr) : bool
    // {
    //     $ret = true;
    //     foreach ($arr as $arrs) {
    //         foreach ($arrs as $key => $value) {
    //             if ($value !== null) {
    //                 $ret = false;
    //             }
    //         }
    //     }
    //     return $ret;
    // }
}
