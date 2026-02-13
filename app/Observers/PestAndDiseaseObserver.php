<?php

namespace App\Observers;

use App\Models\PestAndDisease;
use App\Models\User;
use App\Services\FcmNotificationService;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class PestAndDiseaseObserver
{
    /**
     * Handle the PestAndDisease "created" event.
     * Send notification to all admin users when new pest/disease is detected
     */
    public function created(PestAndDisease $pestAndDisease): void
    {
        try {
            // Get all users with admin-related roles
            // Try to get users with specific roles, but handle if roles don't exist
            $adminUsers = collect();

            try {
                $adminUsers = User::role(['super_admin', 'panel_user'])
                    ->get();
            } catch (\Exception $e) {
                // If role check fails, send to all users
                $adminUsers = User::all();
            }

            // If no specific roles found, send to all users (fallback)
            if ($adminUsers->isEmpty()) {
                $adminUsers = User::all();
            }

            // Send database notification to each admin user
            foreach ($adminUsers as $user) {
                Notification::make()
                    ->title('🐛 New Pest/Disease Detection')
                    ->body("**{$pestAndDisease->pest}** detected in {$pestAndDisease->area} - Severity: {$pestAndDisease->severity}")
                    ->icon('heroicon-o-exclamation-triangle')
                    ->iconColor('warning')
                    ->actions([
                        Action::make('view')
                            ->label('View Details')
                            ->url(route('filament.admin.resources.pest-and-diseases.index'))
                            ->button(),
                    ])
                    ->sendToDatabase($user);
            }

            // Also send a broadcast notification (real-time popup)
            Notification::make()
                ->title('🐛 New Pest/Disease Detection')
                ->body("**{$pestAndDisease->pest}** ({$pestAndDisease->severity} severity)")
                ->icon('heroicon-o-bug-ant')
                ->iconColor('warning')
                ->actions([
                    Action::make('view')
                        ->label('View Now')
                        ->url(route('filament.admin.resources.pest-and-diseases.index'))
                        ->button()
                        ->markAsRead(),
                ])
                ->broadcast($adminUsers);
        } catch (\Exception $e) {
            // Log the error but don't fail the entire process
            \Log::error('Error sending pest/disease notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle the PestAndDisease "updated" event.
     * Optional: Send notification when status changes
     */
    public function updated(PestAndDisease $pestAndDisease): void
    {
        // Send FCM notification to farmer when validation_status changes
        // Note: use wasChanged() not isDirty() — in the 'updated' event, attributes are already synced
        if ($pestAndDisease->wasChanged('validation_status') &&
            in_array($pestAndDisease->validation_status, ['approved', 'disapproved'])) {
            FcmNotificationService::sendValidationUpdate($pestAndDisease);
        }
    }
}
