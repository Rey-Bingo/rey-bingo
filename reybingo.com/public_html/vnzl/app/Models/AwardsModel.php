<?php

namespace App\Models;

use CodeIgniter\Model;

class AwardsModel extends Model {
    protected $table = 'awards';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'game', 'modality', 'observation', 'amount', 'status'
    ];
    
    protected $useTimestamps = true;
}