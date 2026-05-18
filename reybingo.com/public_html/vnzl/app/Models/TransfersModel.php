<?php

namespace App\Models;

use CodeIgniter\Model;

class TransfersModel extends Model {
    protected $table = 'transfers'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['user', 'from', 'amount', 'note', 'status', 'created_at', 'updated_at'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = true;
}