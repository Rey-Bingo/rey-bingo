<?php

namespace App\Libraries;

use App\Models\PushSubscriptionModel;
use App\Config\Push;

class SimplePushService
{
    private $subscriptionModel;
    private $vapidKeys;
    private $config;

    public function __construct()
    {
        $this->subscriptionModel = new PushSubscriptionModel();
        $this->config = new Push();
        
        $this->vapidKeys = [
            'publicKey' => $this->config->vapidPublicKey,
            'privateKey' => $this->config->vapidPrivateKey,
            'subject' => $this->config->vapidSubject
        ];
    }

    public function sendToUser($userId, $payload)
    {
        $subscriptions = $this->subscriptionModel->getSubscriptionsByUser($userId);
        $results = [];
        
        foreach ($subscriptions as $sub) {
            $result = $this->sendPushNotification($sub, $payload);
            $results[] = $result;
        }
        
        return $results;
    }

    public function sendToAll($payload, $excludeUsers = [])
    {
        $subscriptions = $this->subscriptionModel->getAllActiveSubscriptions();
        $results = [];
        
        foreach ($subscriptions as $sub) {
            if (in_array($sub['user_id'], $excludeUsers)) {
                continue;
            }
            
            $result = $this->sendPushNotification($sub, $payload);
            $results[] = $result;
        }
        
        return $results;
    }

    private function sendPushNotification($subscription, $payload)
    {
        $endpoint = $subscription['endpoint'];
        $payloadJson = json_encode($payload);
        
        // Detectar el tipo de servicio push
        $headers = $this->buildHeaders($endpoint, $payloadJson, $subscription);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payloadJson,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true, // Cambiado a true para mayor seguridad
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Log para debugging
        log_message('info', "Push notification sent to {$endpoint}. HTTP Code: {$httpCode}");
        
        // Limpiar suscripciones inválidas
        if ($httpCode == 410 || $httpCode == 404) {
            $this->subscriptionModel->removeSubscription($endpoint);
        }
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'httpCode' => $httpCode,
            'response' => $response,
            'error' => $error,
            'endpoint' => $endpoint
        ];
    }

    private function buildHeaders($endpoint, $payload, $subscription)
    {
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'TTL: 86400'
        ];

        // Agregar headers específicos según el servicio
        if (strpos($endpoint, 'fcm.googleapis.com') !== false) {
            // Google FCM
            $headers[] = 'Authorization: key=' . $this->vapidKeys['privateKey'];
        } elseif (strpos($endpoint, 'push.services.mozilla.com') !== false) {
            // Mozilla
            $headers[] = 'Authorization: vapid t=' . $this->generateVapidToken($endpoint) . ', k=' . $this->vapidKeys['publicKey'];
        } elseif (strpos($endpoint, 'wns.windows.com') !== false) {
            // Windows
            $headers[] = 'X-WNS-Type: wns/raw';
        } else {
            // Genérico - usar VAPID
            $headers[] = 'Authorization: vapid t=' . $this->generateVapidToken($endpoint) . ', k=' . $this->vapidKeys['publicKey'];
        }

        return $headers;
    }

    private function generateVapidToken($audience)
    {
        // Implementación simplificada de VAPID JWT
        $header = json_encode(['typ' => 'JWT', 'alg' => 'ES256']);
        $payload = json_encode([
            'aud' => $audience,
            'exp' => time() + 3600, // 1 hora
            'sub' => $this->vapidKeys['subject']
        ]);

        $headerEncoded = $this->base64UrlEncode($header);
        $payloadEncoded = $this->base64UrlEncode($payload);
        
        // Para simplicidad, retornamos un token básico
        // En producción deberías usar una librería JWT completa
        return $headerEncoded . '.' . $payloadEncoded . '.signature';
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function getVapidPublicKey()
    {
        return $this->vapidKeys['publicKey'];
    }
}
