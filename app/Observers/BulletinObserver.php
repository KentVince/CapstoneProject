<?php

namespace App\Observers;

use App\Models\Bulletin;
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

            // 1. Send to 'all_users' topic — Flutter app subscribes to this on startup
            $topicSent = FcmNotificationService::sendToTopic('all_users', $title, $body, $data);

            if ($topicSent) {
                Log::info("BulletinObserver: Bulletin #{$bulletin->bulletin_id} sent to 'all_users' topic.");
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

                Log::info("BulletinObserver: Bulletin #{$bulletin->bulletin_id} multicast sent.", [
                    'success'  => $result['success'],
                    'failures' => $result['failures'],
                    'total'    => count($tokens),
                ]);
            } else {
                Log::info("BulletinObserver: No FCM tokens found for multicast (topic delivery still active).");
            }

            $this->markNotificationSent($bulletin);

        } catch (\Exception $e) {
            Log::error("BulletinObserver: Failed to send notification for bulletin #{$bulletin->bulletin_id}: " . $e->getMessage());
        }
    }

    private function markNotificationSent(Bulletin $bulletin): void
    {
        // Use DB update to avoid triggering the observer loop
        \DB::table('bulletins')
            ->where('bulletin_id', $bulletin->bulletin_id)
            ->update(['notification_sent' => true]);
    }
}
