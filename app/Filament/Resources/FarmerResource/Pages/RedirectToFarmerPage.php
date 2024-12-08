<?php


namespace App\Filament\Resources\FarmerResource\Pages;

use App\Models\Pds\Personnel;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Filament\Resources\FarmerResource;
use App\Models\Farmer;

class RedirectToFarmerPage extends Page
{
    protected static string $resource = FarmerResource::class;

    public function mount()
    {
        $user = Auth::user();

        // Check if the user has the "reg_user" role                
        if ($user->roles->contains('name', 'panel_user')) {

            // Check if the user has an existing Personnel record
            $farmer = Farmer::where('user_id', $user->id)->first();
            
            if ($farmer) {
                // Redirect to the edit page if a Personnel record exists                
                return redirect()->route('filament.admin.resources.farmers.edit', $farmer);
            }
        }

        // If no Personnel record or the user doesn't have the "reg_user" role, proceed to the create page
        return redirect()->route('filament.admin.resources.farmers.create');
    }
}
