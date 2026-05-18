<?php

namespace App\Models;

use CodeIgniter\Model;

class SingsModel extends Model {
    protected $table = 'sings';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user', 'game', 'carton', 'modality', 'numbers', 
        'lastnumber', 'notified', 'status', 'processing'
    ];
    
    protected $useTimestamps = true;
}