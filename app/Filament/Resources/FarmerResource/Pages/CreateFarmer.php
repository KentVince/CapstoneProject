<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Farmer;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Filament\Resources\FarmerResource;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\Operation\HasControl;


class CreateFarmer extends CreateRecord
{
    use HasControl;
    protected static string $resource = FarmerResource::class;

    protected static bool $canCreateAnother = false;

    protected function getFormActions(): array
    {        
        return [
            // comment to get rid  of Save button
            
            // Action::make('save')
            //     ->label('Save')
            //     ->action(function (): void {
            //         DB::beginTransaction();
            //         try {
            //             $this->saveWithUserCreation(); 
            //             DB::commit();
            //         } catch (\Throwable $th) {
            //             DB::rollBack();
            //             throw $th;
            //         }                    
            //     })
            //     ->color('primary'), 
        ];
    }

    public function finalSave(): void
    {   
        DB::beginTransaction();
        try {
            $this->saveWithUserCreation();  // Call your save method
            DB::commit();

            // Optionally show a success message or redirect the user
            session()->flash('success', 'farmer registered successfully.');
            $this->redirect("/");
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            throw $th; // Handle the error (you can show a custom error message here)
        }
    }

    protected function saveWithUserCreation(): void
    {        
        $firstName = $this->data['farmernames'][array_key_first($this->data['farmernames'])]['first_name'];
        $email = $this->data['contactId']['email_add'] ?? '';
        
        // if ($email == null) {
        //     throw new \Exception('Email required.');
        // }

        // Create or get existing user by email
        $user = User::firstOrCreate([
            'email' => $email,
        ], [
            'name' => $firstName,
            'password' => bcrypt('11111111'), // Default password
        ]);
        
        // set form data with the newly created user's ID
        $this->data['user_id'] = $user->id;              
        $this->create(); 
        
        // Get the created farmer's ID (after create() is called)
        $farmer = Farmer::where('user_id', $user->id)->first();
        
        // Save the farmer_id to the User model
        if ($farmer) {
            $user->farmer_id = $farmer->id;
            $user->save(); // Update the user with the farmer_id
        }

        $roleName = 'panel_user'; // Set initial role for newly registered users
        $user->assignRole($roleName);        
    }
    
 

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure user_id is set and generate app_no before saving
        $data['user_id'] = auth()->id(); // Example: use authenticated user
        $data['app_no'] = $this->generateControlNumber('COF');
        return $data;
    }
   


    protected function getCreatedNotification(): ?Notification
    {
        // Return null to suppress the default notification
        // and show our custom Notif above.
        return null;
    }
}
