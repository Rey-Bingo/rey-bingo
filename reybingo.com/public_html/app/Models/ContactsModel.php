<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactsModel extends Model {
    protected $table = 'contacts'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['user', 'name', 'phone', 'charge', 'created_at', 'updated_at', 'status'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = true;
}