
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuraci\u00f3n de Pusher para CodeIgniter 4.5
 */
class Pusher extends BaseConfig
{
    /**
     * Pusher App ID
     */
    public string $appId;

    /**
     * Pusher App Key
     */
    public string $appKey;

    /**
     * Pusher App Secret
     */
    public string $appSecret;

    /**
     * Pusher Cluster
     */
    public string $cluster;

    /**
     * Use TLS
     */
    public bool $useTLS = true;

    /**
     * Timeout
     */
    public int $timeout = 30;

    public function __construct()
    {
        parent::__construct();

        // Cargar desde variables de entorno
        $this->appId = getenv('PUSHER_APP_ID') ?: '';
        $this->appKey = getenv('PUSHER_APP_KEY') ?: '';
        $this->appSecret = getenv('PUSHER_APP_SECRET') ?: '';
        $this->cluster = getenv('PUSHER_APP_CLUSTER') ?: 'us2';
    }
}
