<?php

namespace App\Libraries\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\GameModel;
use App\Models\BoardModel;
use App\Models\SignsModel;

class GameWebSocket implements MessageComponentInterface
{
    protected $clients;
    protected $gameModel;
    protected $boardModel;
    protected $signsModel;
    
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->gameModel = new \App\Models\GameModel();
        $this->boardModel = new \App\Models\BoardModel();
        $this->signsModel = new \App\Models\SignsModel();
    }
    
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Nueva conexión: {$conn->resourceId}\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        
        if (!$data || !isset($data['type'])) {
            return;
        }
        
        switch ($data['type']) {
            case 'subscribe':
                $this->handleSubscribe($from, $data);
                break;
                
            case 'draw_number':
                $this->handleDrawNumber($from, $data);
                break;
                
            case 'check_winner':
                $this->handleCheckWinner($from, $data);
                break;
                
            case 'ping':
                $from->send(json_encode(['type' => 'pong']));
                break;
        }
    }
    
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Conexión cerrada: {$conn->resourceId}\n";
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
    
    private function handleSubscribe(ConnectionInterface $conn, $data)
    {
        if (!isset($data['game_id'])) {
            return;
        }
        
        $gameId = $data['game_id'];
        $conn->gameId = $gameId;
        
        // Enviar estado actual
        $this->sendGameState($conn, $gameId);
    }
    
    private function handleDrawNumber(ConnectionInterface $from, $data)
    {
        if (!isset($data['game_id'])) {
            return;
        }
        
        $gameId = $data['game_id'];
        
        // Verificar si el juego está activo
        if (!$this->gameModel->isGameActive($gameId)) {
            return;
        }
        
        // Sortear número
        $number = $this->boardModel->drawRandomNumber($gameId);
        if ($number === null) {
            return;
        }
        
        // Guardar en base de datos
        $this->boardModel->insert([
            'user' => 1, // ID del administrador
            'game' => $gameId,
            'number' => $number,
            'status' => 1
        ]);
        
        // Enviar actualización a todos los clientes del juego
        $this->broadcastToGame($gameId, [
            'type' => 'number_drawn',
            'number' => $number,
            'timestamp' => time()
        ]);
        
        // Verificar ganadores
        $this->checkWinners($gameId, $number);
    }
    
    private function handleCheckWinner(ConnectionInterface $from, $data)
    {
        // Implementar lógica de verificación de ganadores
        // Esto se manejará principalmente desde el cliente
    }
    
    private function checkWinners($gameId, $lastNumber)
    {
        // Obtener ganadores pendientes
        $winners = $this->signsModel->getPendingWinners();
        
        foreach ($winners as $winner) {
            if ($winner['game'] == $gameId && $winner['lastnumber'] == $lastNumber) {
                $this->broadcastToGame($gameId, [
                    'type' => 'winner',
                    'winner' => $winner
                ]);
                
                // Marcar como notificado
                $this->signsModel->markAsNotified($winner['id'], [
                    'notified_at' => date('Y-m-d H:i:s'),
                    'method' => 'websocket'
                ]);
            }
        }
    }
    
    private function sendGameState(ConnectionInterface $conn, $gameId)
    {
        $lastNumber = $this->boardModel->getLastDrawnNumber($gameId);
        $lastFive = $this->boardModel->getLastFiveNumbers($gameId);
        $drawnNumbers = $this->boardModel->getDrawnNumbers($gameId);
        
        $conn->send(json_encode([
            'type' => 'game_state',
            'lastNumber' => $lastNumber,
            'lastFive' => $lastFive,
            'drawnNumbers' => array_column($drawnNumbers, 'number'),
            'timestamp' => time()
        ]));
    }
    
    private function broadcastToGame($gameId, $data)
    {
        $message = json_encode($data);
        
        foreach ($this->clients as $client) {
            if (isset($client->gameId) && $client->gameId == $gameId) {
                $client->send($message);
            }
        }
    }
}