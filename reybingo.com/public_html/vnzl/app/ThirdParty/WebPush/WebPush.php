<?php

namespace App\ThirdParty\WebPush;

class WebPush {
    private $vapidKeys;
    private $notifications = [];
    
    public function __construct($config = [])
    {
        $this->vapidKeys = $config['VAPID'] ?? [];
    }
    
    public function queueNotification($subscription, $payload)
    {
        $this->notifications[] = [
            'subscription' => $subscription,
            'payload' => $payload
        ];
    }
    
    public function flush()
    {
        $results = [];
        
        foreach ($this->notifications as $notification) {
            $result = $this->sendNotification(
                $notification['subscription'], 
                $notification['payload']
            );
            $results[] = $result;
        }
        
        $this->notifications = []; // Limpiar cola
        return $results;
    }
    
    private function sendNotification($subscription, $payload)
    {
        $subscriptionData = $subscription->getSubscription();
        
        // Preparar headers
        $headers = $this->getHeaders($subscriptionData, $payload);
        
        // Realizar petición cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $subscriptionData['endpoint'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return new NotificationResult($httpCode, $response, $error, $subscriptionData['endpoint']);
    }
    
    private function getHeaders($subscription, $payload)
    {
        $headers = [
            'Content-Type: application/octet-stream',
            'Content-Length: ' . strlen($payload),
            'TTL: 86400'
        ];
        
        // Agregar headers VAPID si están configurados
        if (!empty($this->vapidKeys)) {
            $vapidHeader = $this->generateVapidHeader($subscription['endpoint']);
            $headers[] = 'Authorization: ' . $vapidHeader;
        }
        
        return $headers;
    }
    
    private function generateVapidHeader($endpoint)
    {
        // Implementación simplificada de VAPID
        $publicKey = $this->vapidKeys['publicKey'];
        $privateKey = $this->vapidKeys['privateKey'];
        $subject = $this->vapidKeys['subject'];
        
        // Para simplicidad, usaremos una implementación básica
        // En producción, necesitarías una implementación completa de JWT
        return "vapid t=eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9, k=" . $publicKey;
    }
}
