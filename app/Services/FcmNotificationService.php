<?php

namespace App\Services;

use App\Models\MobileUser;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmNotificationService
{
    /**
     * Send FCM notification to the farmer's topic when detection is validated.
     * The Flutter app subscribes to topic 'app_COF_2026_02_00001' (app_no with dashes replaced by underscores).
     */
    public static function sendValidationUpdate($detection): void
    {
        Log::info("FCM trigger called for detection #{$detection->case_id}, status: {$detection->validation_status}, app_no: {$detection->app_no}");

        try {
            $messaging = app('firebase.messaging');

            // Build the FCM topic from the detection's app_no
            $appNo = $detection->app_no ?? '';
            if (empty($appNo)) {
                Log::warning("FCM: No app_no found for detection #{$detection->case_id}");
                return;
            }

            $topic = 'app_' . str_replace('-', '_', $appNo);

            $status = $detection->validation_status; // 'approved' or 'disapproved'
            $pest = $detection->pest ?? 'Unknown';

            $title = $status === 'approved'
                ? 'Detection Approved'
                : 'Detection Disapproved';

            $body = $status === 'approved'
                ? "Your detection of \"{$pest}\" has been approved by an expert."
                : "Your detection of \"{$pest}\" was disapproved."
                  . ($detection->expert_comments ? " Reason: {$detection->expert_comments}" : '');

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'type' => 'validation_update',
                    'detection_id' => (string) $detection->case_id,
                    'status' => $status,
                    'pest' => $pest,
                    'expert_comments' => $detection->expert_comments ?? '',
                ]);

            $messaging->send($message);

            Log::info("FCM sent to topic '{$topic}': {$status} for detection #{$detection->case_id}");

        } catch (\Exception $e) {
            Log::error("FCM send failed: " . $e->getMessage());
        }
    }

    /**
     * Send FCM notification to a specific device token
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
