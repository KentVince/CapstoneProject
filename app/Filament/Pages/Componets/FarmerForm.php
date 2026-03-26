<?php

namespace App\Filament\Pages\Componets;

use App\Models\Farmer;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use App\Filament\Traits\HasFarmInfoComponents;
use App\Filament\Traits\HasLoginInfoComponents;
use App\Filament\Traits\HasFarmerInfoComponents;


class FarmerForm
{
  //  protected static ?string $navigationIcon = 'heroicon-o-document-text';

   //  protected static string $view = 'filament.pages.componets.farmer-form';


    use HasFarmerInfoComponents,
        HasFarmInfoComponents,
        HasLoginInfoComponents;


    protected ?Farmer $farmer;
    public array $formData = [];



    public function __construct(?Farmer $farmer)
    {



        $this->farmer = $farmer;

        /**
         *  initialized or load formData
         *  base on session reg_data
         */
        $this->formData = session('reg_data', []);
    }


    /**
     *  wizard for sign up form
     */
    public function getRegistrationSchema(): array
    {

        /**
         *  set session expiration and clear session if expired
         */
        // $expirationTime = 3; // in minutes
        // if (session()->has('reg_data_timestamp')) {
        //     $lastUpdated = session('reg_data_timestamp');
        //     if ($lastUpdated->diffInMinutes(now()) > $expirationTime) {
        //         $this->clearSessionData();
        //     }
        // }

        return [
            /**
             *  save all form entries at each step by calling saveStepData() after validation.
             */
            Wizard::make([
                $this->Step_LoginInfo()
                    ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire))
                    ,

                $this->Step_FarmerInfo($this->farmer, true)
                    ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire))
                    ,

                $this->Step_FarmInfo()
                    ->visible(fn (Get $get): bool => ($get('user_type') ?? 'farmer') !== 'agricultural_professional')
                    ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire))
                    ,




                // $this->Step_SupportDocs(),
            ])
                ->columns(1)
                ->columnSpanFull()
                // ->startOnStep(6) // start on Statutory
                ->persistStepInQueryString()
                ->statePath('data')

                // ->skippable()

                /**
                 *  register button at the last step of the wizard
                 */
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="sm"
                            wire:submit="register"
                        >
                            Register
                        </x-filament::button>
                    BLADE))),
        ];
    }



    public function getAccountSchema(bool $showSubmitAction = true): array
    {
        $wizard = Wizard::make([

            $this->Step_FarmerInfo($this->farmer, true)
                ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire)),

            $this->Step_FarmInfo()
                ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire)),

        ])
            ->columns(1)
            ->columnSpanFull();

        if ($showSubmitAction) {
            $wizard->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button type="submit" size="sm">Save Changes</x-filament::button>
            BLADE)));
        }

        return [$wizard];
    }



    public function saveStepData(Component $livewire)
    {
      

        $existingData = session('reg_data', []);

        $updatedData = array_merge($existingData, $this->formData);

        session()->put('reg_data', $updatedData);
        session()->put('reg_data_timestamp', now());
    }


}
