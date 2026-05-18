<?php

namespace Minishlink\WebPush;

class JWT
{
    public static function encode(array $payload, string $key, string $alg = 'HS256'): string
    {
        $header = ['typ' => 'JWT', 'alg' => $alg];
        
        $segments = [
            self::urlsafeB64Encode(json_encode($header)),
            self::urlsafeB64Encode(json_encode($payload))
        ];
        
        $signing_input = implode('.', $segments);
        $signature = self::sign($signing_input, $key, $alg);
        $segments[] = self::urlsafeB64Encode($signature);
        
        return implode('.', $segments);
    }
    
    private static function sign(string $input, string $key, string $alg): string
    {
        switch ($alg) {
            case 'HS256':
                return hash_hmac('sha256', $input, $key, true);
            case 'ES256':
                // Implementación simplificada
                return hash('sha256', $input . $key, true);
            default:
                throw new \Exception('Algorithm not supported');
        }
    }
    
    private static function urlsafeB64Encode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}
