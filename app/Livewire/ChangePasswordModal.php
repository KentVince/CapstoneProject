<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Rules\CurrentPassword;

class ChangePasswordModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public bool $isOpen = false;

    protected $listeners = ['openChangePasswordModal' => 'openModal'];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->rules([new CurrentPassword()]),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->minLength(8)
                            ->different('current_password')
                            ->validationMessages([
                                'different' => 'The new password must be different from your current password.',
                            ])
                            ->helperText('Must be at least 8 characters long.'),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->same('password')
                            ->validationMessages([
                                'same' => 'Password confirmation does not match.',
                            ]),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function updatePassword()
    {
        $data = $this->form->getState();

        $user = Auth::user();

        // Update password
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        // Log the user out
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        // Flash success message for the login page
        session()->flash('status', 'Your password has been changed successfully. Please log in with your new password.');

        // Redirect to login page
        return redirect(filament()->getLoginUrl());
    }

    public function openModal(): void
    {
        \Log::info('Opening change password modal');
        $this->isOpen = true;
        $this->form->fill();

        // Dispatch browser event for Alpine.js
        $this->dispatch('modal-opened')->self();
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->form->fill();
    }

    public function getListeners()
    {
        return [
            'openChangePasswordModal' => 'openModal',
        ];
    }

    public function render()
    {
        return view('livewire.change-password-modal');
    }
}
