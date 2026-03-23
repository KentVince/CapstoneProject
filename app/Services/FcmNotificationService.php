<?php

namespace App\Services;

use App\Models\Farm;
use App\Models\MobileUser;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class FcmNotificationService
{
    /**
     * Android high-priority config so messages wake the device even in doze mode.
     * Data-only messages (no 'notification' key) ensure onBackgroundMessage always runs.
     */
    private static function androidHighPriority(): AndroidConfig
    {
        return AndroidConfig::fromArray(['priority' => 'high']);
    }

    /**
     * Send FCM notification to the farmer's topic when detection is validated.
     * Uses DATA-ONLY message so Flutter background handler always runs on all Android OEMs.
     */
    public static function sendValidationUpdate($detection): void
    {
        Log::info("FCM trigger called for detection #{$detection->case_id}, status: {$detection->validation_status}, app_no: {$detection->app_no}");

        try {
            $messaging = app('firebase.messaging');

            $appNo = $detection->app_no ?? '';
            if (empty($appNo)) {
                Log::warning("FCM: No app_no found for detection #{$detection->case_id}");
                return;
            }

            $topic = 'app_' . str_replace('-', '_', $appNo);

            $status = $detection->validation_status;
            $pest = $detection->pest ?? 'Unknown';

            $title = $status === 'approved'
                ? 'Detection Approved'
                : 'Detection Disapproved';

            $body = $status === 'approved'
                ? "Your detection of \"{$pest}\" has been approved by an expert."
                : "Your detection of \"{$pest}\" was disapproved."
                  . ($detection->expert_comments ? " Reason: {$detection->expert_comments}" : '');

            // Data-only message — no withNotification() so background handler always fires
            $message = CloudMessage::withTarget('topic', $topic)
                ->withData([
                    'type' => 'validation_update',
                    'title' => $title,
                    'body' => $body,
                    'detection_id' => (string) $detection->case_id,
                    'status' => $status,
                    'pest' => $pest,
                    'expert_comments' => $detection->expert_comments ?? '',
                ])
                ->withAndroidConfig(self::androidHighPriority());

            $messaging->send($message);

            Log::info("FCM sent to topic '{$topic}': {$status} for detection #{$detection->case_id}");

        } catch (\Exception $e) {
            Log::error("FCM send failed: " . $e->getMessage());
        }
    }

    /**
     * Send FCM notification to a specific device token (data-only)
     */
    public static function send(
        string $fcmToken,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            $messaging = app('firebase.messaging');

            $data['title'] = $title;
            $data['body'] = $body;

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withData($data)
                ->withAndroidConfig(self::androidHighPriority());

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
     * Send FCM notification to a topic (data-only)
     */
    public static function sendToTopic(
        string $topic,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            $messaging = app('firebase.messaging');

            $data['title'] = $title;
            $data['body'] = $body;

            $message = CloudMessage::withTarget('topic', $topic)
                ->withData($data)
                ->withAndroidConfig(self::androidHighPriority());

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
     * Send FCM notification to multiple tokens (data-only)
     */
    public static function sendToMultiple(
        array $fcmTokens,
        string $title,
        string $body,
        array $data = []
    ): array {
        try {
            $messaging = app('firebase.messaging');

            $data['title'] = $title;
            $data['body'] = $body;

            $message = CloudMessage::new()
                ->withData($data)
                ->withAndroidConfig(self::androidHighPriority());

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

    /**
     * When a High/Severe pest detection is approved, notify nearby farmers via FCM.
     * Finds all farms within $radiusKm of the detection and sends alerts to each farmer's topic.
     */
    public static function sendNearbySevereAlert($detection, float $radiusKm = 10.0): void
    {
        try {
            $lat = $detection->latitude;
            $lng = $detection->longitude;

            if (!$lat || !$lng || $lat == 0 || $lng == 0) {
                Log::warning("FCM nearby alert: No valid coordinates for detection #{$detection->case_id}");
                return;
            }

            // Haversine formula to find nearby farms
            $haversine = "(6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longtitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            ))";

            $nearbyFarms = Farm::select('farms.*')
                ->selectRaw("{$haversine} AS distance_km", [$lat, $lng, $lat])
                ->whereNotNull('latitude')
                ->whereNotNull('longtitude')
                ->where('latitude', '!=', 0)
                ->where('longtitude', '!=', 0)
                ->having('distance_km', '<=', $radiusKm)
                ->get();

            // Exclude the farm that reported the detection
            $nearbyFarms = $nearbyFarms->filter(function ($farm) use ($detection) {
                return $farm->farmer_id != $detection->farmer_id;
            });

            if ($nearbyFarms->isEmpty()) {
                Log::info("FCM nearby alert: No nearby farms within {$radiusKm}km for detection #{$detection->case_id}");
                return;
            }

            $messaging = app('firebase.messaging');
            $pest = $detection->pest ?? 'Unknown pest';
            $severity = $detection->severity ?? 'High';
            $area = $detection->area ?? 'your area';
            $farmName = $detection->farm?->farm_name ?? 'a nearby farm';
            $sentCount = 0;

            foreach ($nearbyFarms as $farm) {
                $farmer = $farm->farmer;
                if (!$farmer || empty($farmer->app_no)) continue;

                $topic = 'app_' . str_replace('-', '_', $farmer->app_no);
                $distKm = round($farm->distance_km, 1);

                $title = "⚠️ Pest Outbreak Alert Near You";
                $body = "\"{$pest}\" ({$severity} severity) detected at {$farmName}, ~{$distKm}km from your farm. Take preventive action.";

                // Data-only message for reliable background delivery
                $message = CloudMessage::withTarget('topic', $topic)
                    ->withData([
                        'type'         => 'nearby_severe_alert',
                        'title'        => $title,
                        'body'         => $body,
                        'detection_id' => (string) $detection->case_id,
                        'pest'         => $pest,
                        'severity'     => $severity,
                        'area'         => $area,
                        'distance_km'  => (string) $distKm,
                        'farm_name'    => $farmName,
                        'date'         => $detection->date_detected ?? now()->toIso8601String(),
                        'lat'          => (string) $lat,
                        'lng'          => (string) $lng,
                    ])
                    ->withAndroidConfig(self::androidHighPriority());

                $messaging->send($message);
                $sentCount++;

                Log::info("FCM nearby alert sent to topic '{$topic}' (farm: {$farm->farm_name}, {$distKm}km away)");
            }

            Log::info("FCM nearby alert: Sent {$sentCount} alerts for detection #{$detection->case_id} ({$pest}, {$severity})");

        } catch (\Exception $e) {
            Log::error("FCM nearby alert failed: " . $e->getMessage());
        }
    }
}
