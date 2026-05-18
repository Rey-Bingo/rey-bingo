<?php

namespace App\Models;

use CodeIgniter\Model;

class NumbersCartonsModel extends Model {
    protected $table = 'numbers'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['carton', 'number', 'position', 'created_at', 'updated_at', 'status'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = true;

    // Método para obtener números por usuario, juego y número (opcional)
    public function getNumbersByUserAndGame($userId, $gameId, $number) {
        return $this->select('numbers.*')
                    ->join('cartons', 'cartons.id = numbers.carton')
                    ->where('cartons.user', $userId)
                    ->where('cartons.game', $gameId)
                    ->where('numbers.number', $number)
                    ->findAll();
    }

    public function getMarkedNumbersByCarton($cartonId) {
        return $this->where('carton', $cartonId)
                    ->where('status', 1)  // Solo números marcados
                    ->findAll();
    }

    public function getMarkedNumberByUserAndGame($userId, $gameId, $number) {
        return $this->select('numbers.*')
                    ->join('cartons', 'cartons.id = numbers.carton')
                    ->where('cartons.user', $userId)
                    ->where('cartons.game', $gameId)
                    ->where('numbers.number', $number)
                    ->where('numbers.status', 1)  // Solo números marcados
                    ->first();
    }
}