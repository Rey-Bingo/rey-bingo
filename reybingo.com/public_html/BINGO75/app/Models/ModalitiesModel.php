<?php

namespace App\Models;

use CodeIgniter\Model;

class ModalitiesModel extends Model {
    protected $table = 'modalities'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria

    // Campos permitidos para insert/update
    protected $allowedFields = ['name', 'positions', 'observations', 'created_at', 'updated_at', 'status'];

    // Desactivar timestamps automáticos
    protected $useTimestamps = true;

    public function getModalitiesByIds($modalityIds) {
        return $this->whereIn('id', $modalityIds)
                    ->where('status', 1)  // Solo modalidades activas
                    ->findAll();
    }
}