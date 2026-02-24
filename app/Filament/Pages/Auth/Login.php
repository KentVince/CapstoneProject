<?php
namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as AuthLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use App\Services\AgriculturalProfessionalAuthService;
use Illuminate\Support\Facades\Auth;

class Login extends AuthLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent()
                    ->extraInputAttributes(['tabindex' => 2]),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {         
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email': 'name';        
        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        
        // Check if login is an email
        if (filter_var($data['login'], FILTER_VALIDATE_EMAIL)) {
            $authService = new AgriculturalProfessionalAuthService();
            
            // Check if this is an agricultural professional email
            if ($authService->isProfessionalEmail($data['login'])) {
                // Try to authenticate as agricultural professional
                $user = $authService->authenticate($data['login'], $data['password']);
                
                if ($user) {
                    Auth::login($user, $data['remember'] ?? false);
                    return $this->getLoginResponse($user);
                }
                
                // Authentication failed
                $this->throwFailureValidationException();
            }
        }

        // Fall back to default authentication (admin users)
        return parent::authenticate();
    }

    /**
     * Get the appropriate LoginResponse
     */
    protected function getLoginResponse($user): ?LoginResponse
    {
        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {   
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    protected function getLoginFormComponent() : Component 
    {
        return TextInput::make('login')
            ->label("Login")                  
            ->placeholder("Username or email")    
            ->required()
            ->autocomplete()
            ->autofocus();
    }
}