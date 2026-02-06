<?php

namespace App\Services;

use App\Models\MobileUser;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmNotificationService
{
    /**
     * Send FCM notification for detection validation status update
     */
    public static function sendValidationNotification(
        string $appNo,
        string $status,
        string $pest,
        int|string|null $detectionId,
        ?string $comments = null
    ): bool {
        $mobileUser = MobileUser::where('app_no', $appNo)->first();

        if (!$mobileUser || !$mobileUser->fcm_token) {
            Log::info("FCM: No token found for app_no: {$appNo}");
            return false;
        }

        $title = $status === 'approved'
            ? 'Detection Approved'
            : 'Detection Disapproved';

        $body = "Your detection of '{$pest}' has been {$status}";
        if ($status === 'disapproved' && $comments) {
            $body .= ". Reason: {$comments}";
        }

        return self::send($mobileUser->fcm_token, $title, $body, [
            'type' => 'validation_update',
            'detection_id' => (string) ($detectionId ?? ''),
            'status' => $status,
            'pest' => $pest,
            'comments' => $comments ?? '',
        ]);
    }

    /**
     * Send FCM notification to a specific token
     */
    public static function send(
        string $fcmToken,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            $messaging = app('firebase.messaging');

            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData($data);

            $messaging->send($message);

            Log::info("FCM: Notification sent successfully to token: " . substr($fcmToken, 0, 20) . "...");
            return true;

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::warning("FCM: Token not found (device unregistered)", [
                'token' => substr($fcmToken, 0, 20) . '...',
            ]);
            return false;

        } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
            Log::error("FCM: Invalid message", [
                'error' => $e->getMessage(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("FCM: Exception while sending notification", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send FCM notification to a topic
     */
    public static function sendToTopic(
        string $topic,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            $messaging = app('firebase.messaging');

            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData($data);

            $messaging->send($message);

            Log::info("FCM: Notification sent to topic: {$topic}");
            return true;

        } catch (\Exception $e) {
            Log::error("FCM: Exception while sending to topic", [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send FCM notification to multiple tokens
     */
    public static function sendToMultiple(
        array $fcmTokens,
        string $title,
        string $body,
        array $data = []
    ): array {
        try {
            $messaging = app('firebase.messaging');

            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $report = $messaging->sendMulticast($message, $fcmTokens);

            Log::info("FCM: Multicast sent", [
                'success' => $report->successes()->count(),
                'failures' => $report->failures()->count(),
            ]);

            return [
                'success' => $report->successes()->count(),
                'failures' => $report->failures()->count(),
            ];

        } catch (\Exception $e) {
            Log::error("FCM: Exception while sending multicast", [
                'error' => $e->getMessage(),
            ]);
            return ['success' => 0, 'failures' => count($fcmTokens)];
        }
    }
}
