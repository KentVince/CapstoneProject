<?php

namespace App\Observers;

use App\Models\Bulletin;
use App\Models\Farm;
use App\Models\MobileUser;
use App\Services\FcmNotificationService;
use Illuminate\Support\Facades\Log;

class BulletinObserver
{
    /**
     * Send FCM push notification to ALL mobile users when a bulletin is created.
     */
    public function created(Bulletin $bulletin): void
    {
        $this->sendBulletinNotification($bulletin);
    }

    /**
     * Send FCM push notification when a bulletin is updated
     * (only if notification_sent was just flipped to true, i.e. a re-publish).
     */
    public function updated(Bulletin $bulletin): void
    {
        // Only re-notify if notification_sent was explicitly set to true again
        if ($bulletin->wasChanged('notification_sent') && $bulletin->notification_sent) {
            $this->sendBulletinNotification($bulletin);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function sendBulletinNotification(Bulletin $bulletin): void
    {
        try {
            $category = $bulletin->category ?? 'Announcement';
            $title    = "[{$category}] " . ($bulletin->title ?? 'New Bulletin');
            $body     = strip_tags($bulletin->content ?? 'A new bulletin has been posted. Tap to view.');
            $body     = mb_strlen($body) > 120 ? mb_substr($body, 0, 117) . '...' : $body;

            $data = [
                'type'        => 'bulletin',
                'bulletin_id' => (string) $bulletin->bulletin_id,
                'category'    => $category,
                'title'       => $bulletin->title ?? '',
                'date_posted' => $bulletin->date_posted
                    ? $bulletin->date_posted->format('Y-m-d')
                    : now()->format('Y-m-d'),
            ];

            $targetFarmIds = $bulletin->target_farm_ids ?? [];

            if (empty($targetFarmIds)) {
                $this->broadcastToAll($bulletin, $title, $body, $data);
            } else {
                $this->sendToTargetedFarms($bulletin, $title, $body, $data, $targetFarmIds);
            }

            $this->markNotificationSent($bulletin);

        } catch (\Exception $e) {
            Log::error("BulletinObserver: Failed to send notification for bulletin #{$bulletin->bulletin_id}: " . $e->getMessage());
        }
    }

    /**
     * Broadcast to every mobile user (no farm targeting set).
     */
    private function broadcastToAll(Bulletin $bulletin, string $title, string $body, array $data): void
    {
        // 1. Send to 'all_users' topic — Flutter app subscribes to this on startup
        $topicSent = FcmNotificationService::sendToTopic('all_users', $title, $body, $data);

        if ($topicSent) {
            Log::info("BulletinObserver: Bulletin #{$bulletin->bulletin_id} broadcast to 'all_users' topic.");
        }

        // 2. Also send to individual tokens as fallback (for devices that haven't subscribed to topic)
        $tokens = MobileUser::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->pluck('fcm_token')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($tokens)) {
            $result = FcmNotificationService::sendToMultiple($tokens, $title, $body, $data);

            Log::info("BulletinObserver: Bulletin #{$bulletin->bulletin_id} broadcast multicast sent.", [
                'success'  => $result['success'],
                'failures' => $result['failures'],
                'total'    => count($tokens),
            ]);
        } else {
            Log::info("BulletinObserver: No FCM tokens found for broadcast (topic delivery still active).");
        }
    }

    /**
     * Send only to mobile users whose farmer owns one of the targeted farms.
     */
    private function sendToTargetedFarms(Bulletin $bulletin, string $title, string $body, array $data, array $targetFarmIds): void
    {
        // Resolve farms → farmer ids → app_nos
        $farmerIds = Farm::whereIn('id', $targetFarmIds)
            ->pluck('farmer_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($farmerIds)) {
            Log::warning("BulletinObserver: Bulletin #{$bulletin->bulletin_id} targeted but no farmers resolved from farm IDs.", [
                'farm_ids' => $targetFarmIds,
            ]);
            return;
        }

        $mobileUsers = MobileUser::whereIn('farmer_id', $farmerIds)->get();

        $appNos = $mobileUsers->pluck('app_no')->filter()->unique()->values();
        $tokens = $mobileUsers->pluck('fcm_token')->filter(fn ($t) => !empty($t))->unique()->values()->toArray();

        // 1. Send to each targeted farmer's per-user topic
        $topicSuccess = 0;
        foreach ($appNos as $appNo) {
            $topic = 'app_' . str_replace('-', '_', $appNo);
            if (FcmNotificationService::sendToTopic($topic, $title, $body, $data)) {
                $topicSuccess++;
            }
        }

        // 2. Multicast to their FCM tokens as fallback
        $multicast = ['success' => 0, 'failures' => 0];
        if (!empty($tokens)) {
            $multicast = FcmNotificationService::sendToMultiple($tokens, $title, $body, $data);
        }

        Log::info("BulletinObserver: Bulletin #{$bulletin->bulletin_id} sent to targeted farms.", [
            'farm_ids'        => $targetFarmIds,
            'farmer_count'    => count($farmerIds),
            'topic_success'   => $topicSuccess,
            'token_success'   => $multicast['success'],
            'token_failures'  => $multicast['failures'],
            'total_tokens'    => count($tokens),
        ]);
    }

    private function markNotificationSent(Bulletin $bulletin): void
    {
        // Use DB update to avoid triggering the observer loop
        \DB::table('bulletins')
            ->where('bulletin_id', $bulletin->bulletin_id)
            ->update(['notification_sent' => true]);
    }
}
