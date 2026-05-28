<?php

if (! function_exists('systemGet')) {
    function systemGet(string $key): ?string {
        $db = \Config\Database::connect();
        $system = $db->table('system');

        $result = $system->select('value')->where('key', $key)->get()->getRow();

        return $result->value ?? null;
    }
}

if (! function_exists('lastGame')) {
    function lastGame(string $field): ?string {
        $db = \Config\Database::connect();

        $lastGame = $db->table('games')->orderBy('date', 'DESC')->get()->getRowArray();

        if (! $lastGame) {
            return null; 
        }

        if ($field === 'total') {
            $cartons = $db->table('cartons')->where('game', $lastGame['id'])->where('user !=', 0)->countAllResults();

            $accumulated = $cartons * $lastGame['price'];
            $total = $accumulated - ($accumulated * systemGet('rateEarnings'));

            return (string) $total;
        }

        return $lastGame[$field] ?? null;
    }
}

if (! function_exists('getLogo')) {
    function getLogo(): string {
        $db = \Config\Database::connect();
        $system = $db->table('system');

        $result = $system->select('value')->where('key', 'logo')->get()->getRow();

        if ($result && !empty($result->value)) {
            return site_url('uploads/system/' . $result->value);
        }

        return site_url('uploads/system/logo.png');
    }
}

if (!function_exists('translate_day')) {
    function translate_day($date) {
        $dateTime = new DateTime($date);

        $dayOfWeek = $dateTime->format('l');
        $month = $dateTime->format('F');
        $day = $dateTime->format('d');
        $hour = $dateTime->format('g');
        $minutes = $dateTime->format('i');
        $ampm = $dateTime->format('a') == 'am' ? 'AM' : 'PM';

        $dayOfWeekTranslation = translate(strtolower($dayOfWeek));
        $monthTranslation = translate(strtolower($month));
        $ampmTranslation = translate($ampm);

        $formattedDate = "{$dayOfWeekTranslation} - {$hour}:{$minutes} {$ampmTranslation}";

        return ucfirst($formattedDate);
    }
}

if (!function_exists('translate_date')) {
    function translate_date($date) {
        $dateTime = new DateTime($date);

        $dayOfWeek = $dateTime->format('l');
        $month = $dateTime->format('F');
        $day = $dateTime->format('d');
        $hour = $dateTime->format('g');
        $minutes = $dateTime->format('i');
        $ampm = $dateTime->format('a') == 'am' ? 'AM' : 'PM';

        $dayOfWeekTranslation = translate(strtolower($dayOfWeek));
        $monthTranslation = translate(strtolower($month));
        $ampmTranslation = translate($ampm);

        $formattedDate = "{$day} {$monthTranslation} {$dateTime->format('Y')}";

        return ucfirst($formattedDate);
    }
}

if (!function_exists('translate_time')) {
    function translate_time($date) {
        $dateTime = new DateTime($date);

        $dayOfWeek = $dateTime->format('l');
        $month = $dateTime->format('F');
        $day = $dateTime->format('d');
        $hour = $dateTime->format('g');
        $minutes = $dateTime->format('i');
        $ampm = $dateTime->format('a') == 'am' ? 'AM' : 'PM';

        $dayOfWeekTranslation = translate(strtolower($dayOfWeek));
        $monthTranslation = translate(strtolower($month));
        $ampmTranslation = translate($ampm);

        $formattedDate = "{$hour}:{$minutes} {$ampmTranslation}";

        return ucfirst($formattedDate);
    }
}

if (! function_exists('paypalCredentials')) {
    /**
     * Credenciales PayPal alineadas con el modo sandbox/production.
     *
     * @return array{env:string,sandbox_client_id:string,production_client_id:string,client_id:string,secret:string}
     */
    function paypalCredentials(): array
    {
        $paypalConfig = json_decode((string) systemGet('paypal'), true);
        $config = is_array($paypalConfig[0] ?? null) ? $paypalConfig[0] : [];

        $env = (string) ($config['mode'] ?? 'production');
        if (! in_array($env, ['sandbox', 'production'], true)) {
            $env = 'production';
        }

        $sandboxClientId = (string) ($config['sandbox_client_id'] ?? systemGet('idPayPal') ?? '');
        $productionClientId = (string) ($config['production_client_id'] ?? systemGet('idPayPal') ?? '');
        $sandboxSecret = (string) ($config['sandbox_secret_key'] ?? systemGet('secretPayPal') ?? '');
        $productionSecret = (string) ($config['production_secret_key'] ?? systemGet('secretPayPal') ?? '');

        $host = (string) (parse_url((string) base_url(), PHP_URL_HOST) ?: '');
        $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1'], true);
        if (ENVIRONMENT === 'development' && $isLocalHost && $sandboxClientId !== '') {
            $env = 'sandbox';
        }

        return [
            'env' => $env,
            'sandbox_client_id' => $sandboxClientId,
            'production_client_id' => $productionClientId,
            'client_id' => $env === 'sandbox' ? $sandboxClientId : $productionClientId,
            'secret' => $env === 'sandbox' ? $sandboxSecret : $productionSecret,
        ];
    }
}
