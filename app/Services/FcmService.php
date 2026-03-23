<?php

namespace App\Services;

use Google\Client;
use GuzzleHttp\Client as HttpClient;

class FcmService
{
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        $projectId = 'cafarm-d907a';
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));

        // Initialize Google client
        $googleClient = new Client();
        $googleClient->setAuthConfig($credentialsPath);
        $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $token = $googleClient->fetchAccessTokenWithAssertion()['access_token'];

        // Data-only message — title/body in data so Flutter background handler always runs
        $data['title'] = $title;
        $data['body']  = $body;

        // Ensure type is set for Flutter handler routing
        if (!isset($data['type'])) {
            $data['type'] = 'bulletin';
        }

        $message = [
            'topic' => $topic,
            'data'  => $data,
            'android' => [
                'priority' => 'high',
            ],
        ];

        // Send to Firebase v1 API
        $http = new HttpClient();
        $response = $http->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'message' => $message,
            ],
        ]);

        return $response->getStatusCode() === 200;
    }
}
