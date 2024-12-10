<?php

namespace App\Filament\Pages\Componets;

use App\Models\Farmer;
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



    public function getAccountSchema(): array
    {
        return [
            Wizard::make([


            $this->Step_FarmerInfo($this->farmer, true)
                ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire))
                ,

            $this->Step_FarmInfo()
                ->afterValidation(fn(Component $livewire) => $this->saveStepData($livewire))
                ,
            ])
                // ->startOnStep(5)  // start on Statutory
                ->columns(1)
                ->columnSpanFull()
                // ->skippable()

                // don't set statePath() if you want to save using default saving
                // method but if you insist then
                // modify $this->data in CreatePersonnel->finalSave() accordingly
                //
                // ->statePath('data')

                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="button"
                        size="sm"
                        wire:click="finalSave"
                    >
                        Save
                    </x-filament::button>
                BLADE)))
                ,
        ];
    }



    public function saveStepData(Component $livewire)
    {
        /**
         *  retrieve the current form entries
         */

        // $this->formData = $livewire->form->getState();
        $this->formData = $livewire->data;
        // if (method_exists($livewire, 'form') && $livewire->form) {

        //     // $formState = $livewire->form->getState();
        //     $formData = $livewire->data;

        //     for debuging
        //     Log::info('Form State: ', $formData);

        //     // $this->formData = $formState;
        //     $this->formData = $formData;
        // }

        $existingData = session('reg_data', []);

        $updatedData = array_merge($existingData, $this->formData);

        session()->put('reg_data', $updatedData);
        session()->put('reg_data_timestamp', now());
    }


}
