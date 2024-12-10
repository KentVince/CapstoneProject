<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Models\Farmer;
use App\Models\Farm;
use Filament\Forms\Get;
use Filament\Pages\Page;
use BaconQrCode\Common\Mode;
use App\Models\Pds\Personnel;
use Filament\Facades\Filament;
use App\Models\Pds\Personnelname;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Tabs;
use App\Models\Pds\PersonnelAddress;
use Filament\Events\Auth\Registered;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;

use Illuminate\Http\RedirectResponse;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;

use App\Services\UserRegistrationService;

use Filament\Forms\Components\DatePicker;
use Illuminate\Validation\Rules\Password;
use App\Filament\Components\PersonnelForm;
use Filament\Forms\Components\Wizard\Step;
use App\Filament\Pages\Componets\FarmerForm;
use App\Filament\Traits\Pds\HasPersonnelAddress;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Filament\Traits\HasPersonalInfoComponents;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Register extends BaseRegister
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.auth.register';

    protected ?string $maxWidth = '7xl';
    // protected ?string $maxWidth = 'screen';

    protected function getForms(): array
    {


        $farmerForm = new FarmerForm(null);
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema(
                        $farmerForm->getRegistrationSchema()
                    )
                    // ->statePath('data') // end makeForm schema
            ), // end form
        ];
    }


    protected function handleRegistration(array $data): Model
    {

        /**
         *  init instance using app() helper, so laravel
         *  handles all object dependencies
         */

        $regService = app(UserRegistrationService::class);

        $user = $regService->registerUser($data);



        if (!isset($user->farmer_id)) {

            /**
             *  in case registerUser($data) did not set the
             *  farmer_id then lets assign personnel here
             */
            $farmer = Farmer::where('user_id', $user->id)->first();

            if ($farmer) {
                $user->farmer_id = $farmer->id;
                $user->save();

                Farm::where('farmer_id', null)->update(['farmer_id' => $farmer->id]);
            }


        }
        return $user;
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
            // $this->rateLimit(5, 10);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        /**
         *  let not forget to clear the session data after
         *  registration 'reg_data' and 'reg_data_timestamp'
         */
        session()->forget('reg_data');
        session()->forget('reg_data_timestamp');

        session()->regenerate();


        /**
         * redirect to edit page
         */
        if (isset($user->farmer_id)) {
            return new CustomRegistrationResponse(route('filament.admin.resources.farmers.edit', $user->farmer_id));
        } else {
            // Handle missing farmer_id, maybe log an error or notify admin
            throw new \Exception('Personnel ID missing for user registration');
        }
    }

    /**
     *  use blank formactions to get rid of
     *  the default sign up button
     *  and use our custom "register" button at the last part of
     *  the wizard
     */
    protected function getFormActions(): array
    {
        return [];
    }

    // protected function getRoleFormComponent(): Component
    // {
    //     return Select::make('role')
    //         ->options([
    //             'buyer' => 'Buyer',
    //             'seller' => 'Seller',
    //         ])
    //         ->default('buyer')
    //         ->required();
    // }
}
