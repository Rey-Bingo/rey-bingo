<?php

namespace App\Models;

use CodeIgniter\Model;

class MessagesModel extends Model {
    protected $table = 'messages'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['user', 'game', 'message', 'created_at', 'updated_at', 'status'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = true;
}