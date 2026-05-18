<?php

namespace App\Models;

use CodeIgniter\Model;

class GamesModel extends Model {
    protected $table = 'games'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['name', 'observations', 'created_at', 'updated_at', 'status'];

    // Desactivar timestamps automáticos (si prefieres manejarlos manualmente)
    protected $useTimestamps = true;

    // Función para obtener el juego por fecha (YYYY-MM-DD)
    public function getGameByDate($fecha) {
        // Asegúrate de que $fecha tenga el formato Y-m-d
        return $this->where('DATE(created_at)', $fecha)  // Comparar solo la fecha, ignorando la hora
                    ->where('status', 1)          // Solo juegos activos
                    ->first();
    }
}