<?php

namespace App\Libraries;

use App\ThirdParty\WebPush\WebPush;
use App\ThirdParty\WebPush\Subscription;
use App\Models\PushSubscriptionModel;

class PushNotificationService {
    private $webPush;
    private $subscriptionModel;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => env('app.baseURL'),
                'publicKey' => env('push.vapid.publicKey'),
                'privateKey' => env('push.vapid.privateKey')
            ]
        ]);
        
        $this->subscriptionModel = new PushSubscriptionModel();
    }

    public function sendToUser($userId, $payload)
    {
        $subscriptions = $this->subscriptionModel->getSubscriptionsByUser($userId);
        
        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'keys' => [
                    'p256dh' => $sub['p256dh_key'],
                    'auth' => $sub['auth_key']
                ]
            ]);

            $this->webPush->queueNotification($subscription, json_encode($payload));
        }

        return $this->sendQueued();
    }

    public function sendToAll($payload, $excludeUsers = [])
    {
        $subscriptions = $this->subscriptionModel->getAllActiveSubscriptions();
        
        foreach ($subscriptions as $sub) {
            if (in_array($sub['user'], $excludeUsers)) {
                continue;
            }

            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'keys' => [
                    'p256dh' => $sub['p256dh_key'],
                    'auth' => $sub['auth_key']
                ]
            ]);

            $this->webPush->queueNotification($subscription, json_encode($payload));
        }

        return $this->sendQueued();
    }

    private function sendQueued()
    {
        $results = [];
        foreach ($this->webPush->flush() as $report) {
            $results[] = [
                'success' => $report->isSuccess(),
                'endpoint' => $report->getRequest()->getUri()->__toString(),
                'reason' => $report->getReason()
            ];

            // Remover suscripciones inválidas
            if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                $this->subscriptionModel->removeSubscription(
                    $report->getRequest()->getUri()->__toString()
                );
            }
        }

        return $results;
    }
}
