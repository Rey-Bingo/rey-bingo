<?php

namespace App\Models;

use CodeIgniter\Model;

class BoardsModel extends Model {
    protected $table = 'boards'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['user', 'game', 'number', 'created_at', 'updated_at', 'status', 'isCRON'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = true;
    
    // Función para obtener los cartones por jugador y juego
    public function getBoardsByUser($userId, $gameId) {
        return $this->where('user', $userId)
                    ->where('game', $gameId)
                    ->where('status', 1)  // Solo cartones activos
                    ->findAll();  // Obtener todos los cartones en lugar de solo el primero
    }

    public function getNumberByBoard($gameId, $Number) {
        return $this->where('game', $gameId)
                    ->where('number', $Number)
                    ->where('status', 1)  // Solo cartones activos
                    ->findAll();  // Obtener todos los cartones en lugar de solo el primero
    }

    public function getNumbersByBoard($gameId) {
        return $this->where('game', $gameId)
                    ->where('status', 1)  // Solo cartones activos
                    ->findAll();  // Obtener todos los cartones en lugar de solo el primero
    }
}