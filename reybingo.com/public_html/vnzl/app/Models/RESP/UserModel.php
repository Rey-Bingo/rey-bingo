<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['name', 'username', 'password', 'email', 'phone', 'remember_token', 'created_at', 'updated_at', 'status', 'deleted'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = false;

    // Función para obtener el usuario por email o username
    public function getUserByUsername($username) {
        return $this->where('username', $username)
                    ->orWhere('email', $username)
                    ->first();
    }
}