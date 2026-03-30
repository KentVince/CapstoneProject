<?php

namespace App\Observers;

use App\Models\SoilAnalysisExpertComment;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;

class SoilAnalysisExpertCommentObserver
{
    /**
     * Send FCM to the farmer whenever any expert adds a comment —
     * works for both the admin panel (Filament) and the mobile API.
     */
    public function created(SoilAnalysisExpertComment $comment): void
    {
        try {
            $analysis = $comment->soilAnalysis()->with(['farmer', 'validator'])->first();
            if (!$analysis) return;

            $farmer = $analysis->farmer;
            if (!$farmer || !$farmer->app_no) return;

            $expert   = $comment->expert()->with('agriculturalProfessional')->first();
            $agency   = $expert?->agriculturalProfessional?->agency;
            $agencyLabel = $agency ? "Expert from {$agency}" : ($expert?->name ?? 'An Expert');

            $sampleId = sprintf(
                '%s-%s-%s',
                $analysis->farm_id ?? 'X',
                $analysis->date_collected?->format('m-d-y') ?? now()->format('m-d-y'),
                str_pad($analysis->id, 2, '0', STR_PAD_LEFT)
            );

            $this->sendFcm(
                $farmer->app_no,
                'New Expert Comment',
                "{$agencyLabel} added a recommendation for your soil analysis.",
                [
                    'type'           => 'soil_recommendation_update',
                    'is_extra'       => 'true',
                    'analysis_id'    => (string) $analysis->id,
                    'sample_id'      => $sampleId,
                    'recommendation' => $comment->message,
                    'expert_name'    => $agencyLabel,
                ]
            );
        } catch (\Exception $e) {
            Log::error('SoilAnalysisExpertCommentObserver FCM error: ' . $e->getMessage());
        }
    }

    private function sendFcm(string $appNo, string $title, string $body, array $data): void
    {
        $messaging  = app('firebase.messaging');
        $mobileUser = \App\Models\MobileUser::where('app_no', $appNo)->first();
        $topic      = 'app_' . str_replace('-', '_', $appNo);

        // Data-only message — title/body in data so Flutter background handler always runs
        $data['title'] = $title;
        $data['body']  = $body;

        if ($mobileUser?->fcm_token) {
            // Try device token first; fall back to topic if token is stale/invalid
            try {
                $messaging->send(
                    CloudMessage::fromArray([
                        'token'   => $mobileUser->fcm_token,
                        'data'    => $data,
                        'android' => ['priority' => 'high'],
                    ])
                );
                return;
            } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                \Illuminate\Support\Facades\Log::warning("FCM: Stale token for app_no {$appNo}, falling back to topic");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("FCM: Token send failed for app_no {$appNo}, falling back to topic: " . $e->getMessage());
            }
        }

        // Use topic (either no token stored, or token was stale)
        $messaging->send(
            CloudMessage::fromArray([
                'topic'   => $topic,
                'data'    => $data,
                'android' => ['priority' => 'high'],
            ])
        );
    }
}
