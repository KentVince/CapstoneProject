<?php

namespace App\Filament\Traits;

use Carbon\Carbon;
use App\Models\Farmer;
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
use App\Filament\Traits\Pds\HasfarmerAddress;

trait HasFarmerInfoComponents
{
 //   use HasfarmerAddress;

    public function Step_FarmerInfo(?Farmer $farmer = null, ?bool $is_signup = false): Step
    {

        return
            Step::make('Farmer Info')
            ->label(new HtmlString('<span class="sm:whitespace-normal md:whitespace-pre-line md:inline">Farmer Information</span>'))
            ->icon('heroicon-o-user')
            // ->description('Enter your Personal Details')
            ->completedIcon('heroicon-m-hand-thumb-up')
           
            ->schema([



              
                    Tabs::make('Basic Information')
                   
                        ->tabs([
                            Tab::make('Farm Information')
                            ->schema([
                                Grid::make('farmerinfo')
                                   // ->when($farmer != null, fn($grid) => $grid->relationship('contactId'))
                                    ->columns([
                                        'md' => 1,  // normal medium monitor
                                        'lg' => 2,  // my small monitor
                                        '2xl' => 3, // my large monitor
                                    ])
                                    ->label('')
                                    ->schema([
                                        
                                      TextInput::make('app_no')
                                        ->default('Kentoi-0001'),

                                     TextInput::make('crop')
                                        ->dehydrated()
                                        ->default('Coffee'),

                                        Select::make('funding_source')
                                        ->searchable()
                                        ->required()
                                        ->options([
                                            'Self-Financed' => 'Self-Financed',
                                            'Borrowing' => 'Borrowing',
                                            'Lender' => 'Lender',
                                        ]),
                
                                    DatePicker::make('date_of_application')
                                    ->default(Carbon::now()->format('Y-m-d')) // Ensure the date is in the correct format
                                        ->disabled()
                                        ->dehydrated(),

                                   
                                    ]),  // end Fieldset contactId schema
                            ])
                            ->icon('heroicon-o-user'),


                            Tab::make('Basic Info')
                                // ->extraAttributes(['class' => 'personal-info-test'])
                              
                                ->schema([


                                    Grid::make('farmernames')
                                    //     ->when($farmer != null, fn($grid) => $grid->relationship('contactId'))
                                         ->columns([
                                             'md' => 1,  // normal medium monitor
                                             'lg' => 2,  // my small monitor
                                             '2xl' => 3, // my large monitor
                                         ])
                                         ->label('')
                                         ->schema([

                                    

                                    
                                    
                                TextInput::make('lastname')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('firstname')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('middlename')
                                    ->required()
                                    ->maxLength(255),

                                    Select::make('province')
                                    ->options([
                                        'Davao de Oro' => 'Davao de Oro', // The key must match the default value
                                        'Davao del Sur' => 'Davao del Sur',
                                    ])
                                    ->default('Davao de Oro')
                                    ->disabled(),

                                    Select::make('municipality')
                                    ->label('Municipality')
                                    ->required()
                                    ->options(\App\Models\Municipality::whereNotNull('citymunCode')->pluck('citymunDesc', 'citymunCode'))
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(fn (callable $set) => $set('barangay', null)),

                                    Select::make('barangay')
                                    ->label('Barangay')
                                    ->required()
                                    ->searchable()
                                    ->options(function (callable $get) {
                                        $municipalityCode = $get('municipality'); // Get the selected municipality's code
                                        if ($municipalityCode) {
                                            return \App\Models\Barangay::where('citymunCode', $municipalityCode)
                                                ->whereNotNull('brgyDesc') // Ensure no null descriptions
                                                ->pluck('brgyDesc', 'id');
                                        }
                                        return [];
                                    }),

                                TextInput::make('street')
                                    ->required()
                                    ->maxLength(255),
                               

                             

                                Select::make('sex')
                                    ->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                    ])
                                    ->searchable()
                                    ->required(),

                                DatePicker::make('birthdate')
                                    ->required(),

                                TextInput::make('age')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('civil_status')
                                    ->options([
                                        'single' => 'Single',
                                        'married' => 'Married',
                                        'widowed' => 'Widowed',
                                        'separated' => 'Separated',
                                        'divorced' => 'Divorced',
                                    ])
                                    ->searchable()
                                    ->required(),

                                    TextInput::make('spouse')
                                    ->placeholder('If married, name of spouse')
                                    ->required()
                                    ->maxLength(255),

                                    Select::make('ip')
                                    ->label('Ethnicity')
                                    ->required()
                                    ->options([
                                        'Non-ip' => 'Non-ip',
                                        'Bisaya' => 'Bisaya / Binisaya',
                                        'Boholano' => 'Boholano',
                                        'Cagan' => 'Cagan / Kagan',
                                        'Cebuano' => 'Cebuano',
                                        'Davawenyo' => 'Davawenyo',
                                        'Dibabawon' => 'Dibabawon',
                                        'Ilonggo' => 'Hiligaynon/Ilonggo',
                                        'Ilocano' => 'Ilocano',
                                        'Mandaya' => 'Mandaya',
                                        'Manguangan' => 'Manguangan',
                                        'Manobo' => 'Manobo',
                                        'Mansaka' => 'Mansaka',
                                        'Tagalog' => 'Tagalog',
                                        'Others' => 'Others',
                                    ])
                                    ->searchable()
                                    ,

        

                                    Select::make('pwd')
                                    ->label('Disability')
                                    ->required()
                                    ->searchable()
                                    ->options([
                                        'No' => 'No',
                                        'Mental/Intellectual' => 'Mental/Intellectual',
                                        'Hearing Disability' => 'Hearing Disability',
                                        'Psychosocial Disability' => 'Psychosocial Disability',
                                        'Visual Disability' => 'Visual Disability',
                                        'Speech Impairment' => 'Speech Impairment',
                                        'Disability due to chronic  illness' => 'Disability due to chronic  illness',
                                        'Orthopaedic' => 'Orthopaedic (Musculoskeletal) Disability',
                                        'Learning Disability' => 'Learning Disability',
                                        'Others' => 'Others',
                                    ]),
                                ]),
                                ]) ->icon('heroicon-o-identification'),  // end Personal Info tab
    
                            Tab::make('Contact / IDs')
                                ->schema([
                                    Grid::make()
                                      //  ->when($farmer != null, fn($grid) => $grid->relationship('contactId'))
                                        ->columns([
                                            'md' => 1,  // normal medium monitor
                                            'lg' => 2,  // my small monitor
                                            '2xl' => 3, // my large monitor
                                        ])
                                        ->label('')
                                        ->schema([
                                            TextInput::make('tel_no')
                                                ->maxLength(20)
                                                ->hidden($is_signup)
                                                ->label('Telephone No.'),
    
                                            TextInput::make('phone_no')
                                                ->default($this->formData['phone_no'] ?? null)
                                                ->maxLength(20)
                                                ->required()
                                                ->label('Mobile No.'),

                                                
    
                                            TextInput::make('email_add')
                                                ->default($this->formData['email_add'] ?? null)
                                                ->email()
                                                ->required()
                                                ->hidden($is_signup)
                                                ->label('Email Add:'),
    
                                            TextInput::make('bir_tin')
                                                ->hidden($is_signup)
                                                ->label('BIR TIN'),
    
                                            TextInput::make('gsis_id_no')
                                                ->hidden($is_signup)
                                                ->maxLength(50)
                                                ->label('GSIS UMID No.'),
    
                                            TextInput::make('gsis_bp_id_no')
                                                ->hidden($is_signup)
                                                ->label('GSIS BP No.'),
    
                                            TextInput::make('hdmf_id_no')
                                                ->hidden($is_signup)
                                                ->maxLength(50)
                                                ->label('HDMF ID No.'),
    
                                            TextInput::make('phic_id_no')
                                                ->hidden($is_signup)
                                                ->maxLength(50)
                                                ->label('PHIC ID No.'),
    
                                            TextInput::make('sss_id_no')
                                                ->hidden($is_signup)
                                                ->maxLength(50)
                                                ->label('SSS ID No.'),
    
                                            TextInput::make('blood_type')
                                                ->hidden($is_signup)
                                                ->maxLength(5)
                                                ->label('Blood type'),
                                        ]),  // end Fieldset contactId schema
                                ])
                                ->icon('heroicon-o-credit-card'),


                               


                                Tab::make('Bank Information')
                                ->schema([
                                    Grid::make()
                                      //  ->when($farmer != null, fn($grid) => $grid->relationship('contactId'))
                                        ->columns([
                                            'md' => 1,  // normal medium monitor
                                            'lg' => 2,  // my small monitor
                                            '2xl' => 3, // my large monitor
                                        ])
                                        ->label('')
                                        ->schema([
                                            

                                            TextInput::make('bank_name')
                                            ->required()
                                            ->maxLength(255),
        
                                        TextInput::make('bank_account_no')
                                            ->required()
                                            ->maxLength(255),
        
                                        TextInput::make('bank_branch')
                                            ->required()
                                            ->maxLength(255),
                                        ]),  // end Fieldset contactId schema
                                ])
                                ->icon('heroicon-o-banknotes'),




                                Tab::make('Beneficiaries')
                                ->schema([
                                    Grid::make()
                                    //    ->when($farmer != null, fn($grid) => $grid->relationship('contactId'))
                                        ->columns([
                                            'md' => 1,  // normal medium monitor
                                            'lg' => 2,  // my small monitor
                                            '2xl' => 3, // my large monitor
                                        ])
                                        ->label('')
                                        ->schema([
                                            
                                        TextInput::make('primary_beneficiaries')
                                            ->required()
                                            ->maxLength(255),
            
                                        TextInput::make('primary_beneficiaries_age')
                                            ->label('Age')
                                            ->required()
                                            ->maxLength(255),
            
                                        TextInput::make('primary_beneficiaries_relationship')
                                            ->label('Relationship')
                                            ->required()
                                            ->maxLength(255),
            
                                        TextInput::make('secondary_beneficiaries')
                                            ->required()
                                            ->maxLength(255),
            
                                        TextInput::make('secondary_beneficiaries_age')
                                            ->label('Age')
                                            ->required()
                                            ->maxLength(255),
            
                                        TextInput::make('secondary_beneficiaries_relationship')
                                            ->label('Relationship')
                                            ->required()
                                            ->maxLength(255),


                                            TextInput::make('assignee')
                                           
                                            ->required()
                                            ->maxLength(255),

                                            TextInput::make('reason_assignment')
                                           
                                            ->required()
                                            ->maxLength(255),


                                ])
                                ])
                                ->icon('heroicon-o-users'),




                          



               

                ])


                ]);
          
    }

    public function checkSameAsPermanent() : bool
    {
        $res = $this->formData['addresses_residential'] ?? [];
        $per = $this->formData['addresses_permanent'] ?? [];

        return (!empty($res) && !empty($per) && $this->addressesMatch($res, $per));
    }

    protected function addressesMatch ($res, $per) : bool
    {
        $filter = ['id', 'created_at', 'updated_at', 'address_type' ];

        $resFiltered = collect($res)->map(fn($item) => collect($item)->except($filter)->toArray())->values()->toArray();
        $perFiltered = collect($per)->map(fn($item) => collect($item)->except($filter)->toArray())->values()->toArray();

        $constNull = $this->containsOnlyNull($resFiltered) && $this->containsOnlyNull($perFiltered);

        return !($constNull) && ($resFiltered === $perFiltered);
    }

    protected function containsOnlyNull(array $arr) : bool
    {
        $ret = true;
        foreach ($arr as $arrs) {
            foreach ($arrs as $key => $value) {
                if ($value !== null) {
                    $ret = false;
                }
            }
        }
        return $ret;
    }
}
