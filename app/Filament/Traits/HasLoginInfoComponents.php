<?php

namespace App\Filament\Traits;

use App\Models\User;
use Filament\Forms\Set;
use Livewire\Component;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\DB;
use App\Traits\Operation\HasControl;
use App\Filament\Pages\Auth\Register;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\Wizard\Step;

trait HasLoginInfoComponents
{
    public function Step_LoginInfo(): Step
    {

        // dd($this->formData['name']);
        return
            Step::make('Login Info')
            ->icon('heroicon-o-arrow-right-circle')
            ->columns([
                'md' => 1,
                'lg' => 2,  // my small monitor
                // '2xl' => 3, // my large monitor
            ])
            ->schema([

                Select::make('user_type')
                    ->label('I am registering as')
                    ->options([
                        'farmer' => 'Farmer',
                        'agricultural_professional' => 'Agricultural Professional',
                    ])
                    ->default('farmer')
                    ->required()
                    ->reactive()
                    ->columnSpanFull()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $prefix = $state === 'agricultural_professional' ? 'AP' : 'COF';
                        $controlNumber = (new class { use HasControl; })->generateControlNumber($prefix);
                        $set('app_no', $controlNumber);
                    }),

                TextInput::make('name')
                    ->default($this->formData['name'] ?? null)
                    ->label('Username')
                    //->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->default($this->formData['email'] ?? null)
                    ->label('Email Address')
                    ->email()
                    //->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email'),

                TextInput::make('password')
                    ->default($this->formData['password'] ?? null)
                    ->label('Password')
                    ->password()
                    //->required()
                    ->rule(Password::default())
                    ->same('passwordConfirmation')
                    ->revealable(),

                TextInput::make('passwordConfirmation')
                    ->default($this->formData['passwordConfirmation'] ?? null)
                    ->label('Confirm Password')
                    ->password()
                    //->required(),
            ])
        ;
    }
}
