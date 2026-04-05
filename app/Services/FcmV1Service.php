<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FcmV1Service
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->messaging = $factory->createMessaging();
    }

    public function sendToTopic(string $topic, string $title, string $body): void
    {
        $message = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        $this->messaging->send($message);
    }
}
