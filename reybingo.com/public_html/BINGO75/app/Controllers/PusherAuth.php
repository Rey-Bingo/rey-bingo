<?php
namespace App\Controllers;

use App\Libraries\PusherFactory;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PusherAuth extends ResourceController
{
    protected $format = 'json';

    public function auth()
    {
        // Agregar logs para debug
        log_message('info', 'PusherAuth::auth called');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('info', 'Headers: ' . json_encode($this->request->getHeaders()));
        
        $request = $this->request->getPost();
        $channelName = $request['channel_name'] ?? '';
        $socketId    = $request['socket_id'] ?? '';

        if (empty($channelName) || empty($socketId)) {
            log_message('error', 'Missing channel_name or socket_id');
            return $this->respond(['message' => 'Faltan parámetros'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (strpos($channelName, 'private-game-') !== 0) {
            log_message('error', 'Invalid channel: ' . $channelName);
            return $this->respond(['message' => 'Canal no permitido'], ResponseInterface::HTTP_FORBIDDEN);
        }

        try {
            $pusher = PusherFactory::make();
            $auth = $pusher->authorizeChannel($channelName, $socketId);
            
            log_message('info', 'Auth successful for channel: ' . $channelName);
            log_message('info', 'Auth response: ' . json_encode($auth));
            
            return $this->respond($auth);
            
        } catch (\Exception $e) {
            log_message('error', 'Pusher auth error: ' . $e->getMessage());
            return $this->respond(['message' => 'Error de autenticación'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
