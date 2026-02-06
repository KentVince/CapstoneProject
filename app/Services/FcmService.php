<?php

namespace App\Services;

use Google\Client;
use GuzzleHttp\Client as HttpClient;

class FcmService
{
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        $projectId = 'cafarm-d907a'; // ✅ your Firebase Project ID
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

        // 🔑 Initialize Google client
        $googleClient = new Client();
        $googleClient->setAuthConfig($credentialsPath);
        $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $token = $googleClient->fetchAccessTokenWithAssertion()['access_token'];

        // 🛰️ Build request body (important: no empty arrays)
        $message = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        // Only add data if not empty
        if (!empty($data)) {
            $message['data'] = $data;
        }

        // 🧭 Send to Firebase
        $http = new HttpClient();
        $response = $http->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'message' => $message, // ✅ correct key here
            ],
        ]);

        return $response->getStatusCode() === 200;
    }
}
