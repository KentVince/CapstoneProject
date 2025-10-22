<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotification
{
    /**
     * Send a push notification to all users subscribed to the 'all_users' topic.
     *
     * @param  string  $title
     * @param  string  $body
     * @return void
     */
    public static function send(string $title, string $body): void
    {
        try {
            $serverKey = config('services.firebase.server_key');

            if (!$serverKey) {
                Log::warning('⚠️ Firebase server key missing. Check config/services.php');
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => '/topics/all_users',
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                    'icon'  => 'ic_launcher',
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'route' => '/announcement', // mobile app route
                ],
            ]);

            if ($response->failed()) {
                Log::error('❌ Firebase push failed: ' . $response->body());
            } else {
                Log::info('✅ Firebase push sent successfully: ' . $title);
            }
        } catch (\Throwable $e) {
            Log::error('🚨 Firebase push error: ' . $e->getMessage());
        }
    }
}
