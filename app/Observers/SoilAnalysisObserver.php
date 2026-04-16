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
                $adminUsers = User::role(['super_admin', 'panel_user', 'agri_expert'])
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
            ], false);

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

            // Send FCM notification to the farmer so Flutter shows the new record immediately
            try {
                $farmer = $soilAnalysis->farmer;
                $appNo = $farmer?->app_no;
                if ($appNo) {
                    $this->sendFcmNotification(
                        $appNo,
                        'New Soil Analysis Record',
                        "A soil analysis record has been added for {$farmName}.",
                        [
                            'type'        => 'soil_analysis_created',
                            'analysis_id' => (string) $soilAnalysis->id,
                            'sample_id'   => $soilAnalysis->sample_id ?? '',
                            'farm_name'   => $farmName,
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error('Error sending soil_analysis_created FCM to farmer: ' . $e->getMessage());
            }
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

            // Defer FCM calls to after the HTTP response so the UI stays fast
            $analysisId = $soilAnalysis->id;

            dispatch(function () use ($analysisId) {
                try {
                    $soilAnalysis = SoilAnalysis::find($analysisId);
                    if (!$soilAnalysis) return;

                    $farmer = $soilAnalysis->farmer;
                    if (!$farmer) return;

                    $expertName = $soilAnalysis->validator?->name ?? 'Agricultural Expert';
                    $title = 'Expert Recommendation Received';
                    $body = "An expert added a recommendation for your soil analysis.";

                    $sampleId = sprintf(
                        '%s-%s-%s',
                        $soilAnalysis->farm_id ?? 'X',
                        $soilAnalysis->date_collected?->format('m-d-y') ?? now()->format('m-d-y'),
                        str_pad($soilAnalysis->id, 2, '0', STR_PAD_LEFT)
                    );

                    $appNo = $farmer->app_no;
                    if ($appNo) {
                        (new self)->sendFcmNotification(
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
            })->afterResponse();
        }
    }

    /**
     * Send FCM notification to farmer (data-only for reliable background delivery)
     */
    private function sendFcmNotification($appNo, $title, $body, $data)
    {
        try {
            $messaging = app('firebase.messaging');

            // Data-only message — no 'notification' key so background handler always fires
            $data['title'] = $title;
            $data['body']  = $body;

            $topic = 'app_' . str_replace('-', '_', $appNo);
            $mobileUser = \App\Models\MobileUser::where('app_no', $appNo)->first();

            if ($mobileUser && $mobileUser->fcm_token) {
                // Try token first; fall back to topic if token is stale/invalid
                try {
                    $message = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
                        'token'   => $mobileUser->fcm_token,
                        'data'    => $data,
                        'android' => ['priority' => 'high'],
                    ]);
                    $messaging->send($message);
                    return;
                } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                    Log::warning("FCM: Stale token for app_no {$appNo}, falling back to topic");
                } catch (\Exception $e) {
                    Log::warning("FCM: Token send failed for app_no {$appNo}, falling back to topic: " . $e->getMessage());
                }
            }

            // Use topic (either no token stored, or token was stale)
            $message = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
                'topic'   => $topic,
                'data'    => $data,
                'android' => ['priority' => 'high'],
            ]);
            $messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM notification error: ' . $e->getMessage());
        }
    }
}
