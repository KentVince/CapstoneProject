<?php

namespace App\Observers;

use App\Models\PestDiseaseExpertComment;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class PestDiseaseExpertCommentObserver
{
    /**
     * Send FCM to the farmer whenever any expert adds a comment —
     * works for both the admin panel (Filament) and the mobile API.
     */
    public function created(PestDiseaseExpertComment $comment): void
    {
        try {
            $detection = $comment->pestAndDisease()->first();
            if (!$detection) return;

            $appNo = $detection->app_no;
            if (!$appNo) return;

            $expert      = $comment->expert()->with('agriculturalProfessional')->first();
            $agency      = $expert?->agriculturalProfessional?->agency;
            $agencyLabel = $agency ? "Expert from {$agency}" : ($expert?->name ?? 'An Expert');

            $this->sendFcm(
                $appNo,
                'New Expert Comment',
                "{$agencyLabel} added a recommendation for your detection.",
                [
                    'type'        => 'pest_extra_comment',
                    'case_id'     => (string) $detection->case_id,
                    'pest'        => $detection->pest ?? '',
                    'expert_name' => $agencyLabel,
                    'message'     => $comment->message,
                ]
            );
        } catch (\Exception $e) {
            Log::error('PestDiseaseExpertCommentObserver FCM error: ' . $e->getMessage());
        }
    }

    private function sendFcm(string $appNo, string $title, string $body, array $data): void
    {
        $messaging  = app('firebase.messaging');
        $mobileUser = \App\Models\MobileUser::where('app_no', $appNo)->first();

        $payload = $mobileUser?->fcm_token
            ? ['token' => $mobileUser->fcm_token]
            : ['topic' => 'app_' . str_replace('-', '_', $appNo)];

        // Data-only message — title/body in data so Flutter background handler always runs
        $data['title'] = $title;
        $data['body']  = $body;

        $messaging->send(
            CloudMessage::fromArray(
                array_merge($payload, [
                    'data'    => $data,
                    'android' => ['priority' => 'high'],
                ])
            )
        );
    }
}
