<?php

namespace App\Observers;

use App\Models\SoilAnalysis;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Log;

class SoilAnalysisObserver
{
    /**
     * Handle the SoilAnalysis "created" event.
     * Send notification to all admin users when new soil analysis is synced from Flutter
     */
    public function created(SoilAnalysis $soilAnalysis): void
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

            // Prepare notification details
            $farmName = $soilAnalysis->farm_name ?? 'Unknown Farm';
            $phLevel = $soilAnalysis->ph_level ? number_format($soilAnalysis->ph_level, 2) : 'N/A';
            $dateCollected = $soilAnalysis->date_collected?->format('M d, Y') ?? 'N/A';

            $viewUrl = route('filament.admin.resources.soil-analyses.index', [
                'viewRecord' => $soilAnalysis->id,
            ]);

            // Send database notification to each admin user
            foreach ($adminUsers as $user) {
                Notification::make()
                    ->title('New Soil Analysis Data Synced')
                    ->body("**{$farmName}** - pH: {$phLevel} (Collected: {$dateCollected})")
                    ->icon('heroicon-o-beaker')
                    ->iconColor('success')
                    ->actions([
                        Action::make('view')
                            ->label('View Details')
                            ->url($viewUrl)
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($user);
            }

            // Also send a broadcast notification (real-time popup)
            Notification::make()
                ->title('New Soil Analysis Data Synced')
                ->body("**{$farmName}** soil analysis data has been synced from the mobile app.")
                ->icon('heroicon-o-beaker')
                ->iconColor('success')
                ->actions([
                    Action::make('view')
                        ->label('View Now')
                        ->url($viewUrl)
                        ->button()
                        ->markAsRead(),
                ])
                ->broadcast($adminUsers);
        } catch (\Exception $e) {
            // Log the error but don't fail the entire process
            Log::error('Error sending soil analysis notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle the SoilAnalysis "updated" event.
     * Send FCM notification to farmer when expert validation/recommendation is provided
     */
    public function updated(SoilAnalysis $soilAnalysis): void
    {
        // Send FCM notification to farmer when expert_comments or validation_status changes
        // Note: use wasChanged() not isDirty() — in the 'updated' event, attributes are already synced
        if (($soilAnalysis->wasChanged('expert_comments') || $soilAnalysis->wasChanged('validation_status')) &&
            !empty($soilAnalysis->expert_comments)) {

            try {
                // Get the farmer
                $farmer = $soilAnalysis->farmer;
                if (!$farmer) {
                    return;
                }

                // Get the expert/validator name
                $expertName = $soilAnalysis->validator?->name ?? 'Agricultural Expert';

                // Prepare FCM message content
                $status = $soilAnalysis->validation_status;
                $farmName = $soilAnalysis->farm_name ?? 'Your soil analysis';

                $title = 'Expert Recommendation Received';
                $body = "An expert added a recommendation for your soil analysis.";

                // Generate sample_id (format: farm_id-date-analysis_id)
                $sampleId = sprintf(
                    '%s-%s-%s',
                    $soilAnalysis->farm_id ?? 'X',
                    $soilAnalysis->date_collected?->format('m-d-y') ?? now()->format('m-d-y'),
                    str_pad($soilAnalysis->id, 2, '0', STR_PAD_LEFT)
                );

                // Send FCM notification to farmer's device
                $appNo = $farmer->app_no;
                if ($appNo) {
                    $this->sendFcmNotification(
                        $appNo,
                        $title,
                        $body,
                        [
                            'type' => 'soil_recommendation_update',
                            'analysis_id' => (string) $soilAnalysis->id,
                            'sample_id' => $sampleId,
                            'recommendation' => $soilAnalysis->expert_comments ?? '',
                            'expert_name' => $expertName,
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error('Error sending soil analysis recommendation FCM: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send FCM notification to farmer
     */
    private function sendFcmNotification($appNo, $title, $body, $data)
    {
        try {
            $messaging = app('firebase.messaging');

            // Get mobile user to find FCM token
            $mobileUser = \App\Models\MobileUser::where('app_no', $appNo)->first();

            if ($mobileUser && $mobileUser->fcm_token) {
                // Send to specific device using token
                $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);
                $message = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
                    'token' => $mobileUser->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                ]);

                $messaging->send($message);
            } else {
                // Use topic-based messaging
                $topic = 'app_' . str_replace('-', '_', $appNo);
                $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);
                $message = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                ]);

                $messaging->send($message);
            }
        } catch (\Exception $e) {
            Log::error('FCM notification error: ' . $e->getMessage());
        }
    }
}
