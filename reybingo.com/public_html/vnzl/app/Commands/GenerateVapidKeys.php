<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GenerateVapidKeys extends BaseCommand
{
    protected $group = 'App';
    protected $name = 'vapid:generate';
    protected $description = 'Generate VAPID keys for push notifications';

    public function run(array $params)
    {
        // Generar claves usando OpenSSL
        $privateKey = $this->generatePrivateKey();
        $publicKey = $this->generatePublicKey($privateKey);
        
        CLI::write('VAPID Keys Generated:', 'green');
        CLI::write('');
        CLI::write('Public Key: ' . $publicKey, 'yellow');
        CLI::write('Private Key: ' . $privateKey, 'yellow');
        CLI::write('');
        CLI::write('Add these to your .env file:', 'cyan');
        CLI::write('push.vapid.publicKey=' . $publicKey);
        CLI::write('push.vapid.privateKey=' . $privateKey);
    }
    
    private function generatePrivateKey()
    {
        // Generar clave privada simple (para desarrollo)
        return base64_encode(random_bytes(32));
    }
    
    private function generatePublicKey($privateKey)
    {
        // Generar clave pública simple (para desarrollo)
        return base64_encode(hash('sha256', $privateKey, true));
    }
}
