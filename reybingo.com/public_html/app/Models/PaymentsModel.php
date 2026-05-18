<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model {
    protected $table = 'payments';
    protected $primaryKey = 'id'; 

    protected $allowedFields = ['user', 'type', 'type_id', 'amount', 'created_at', 'updated_at', 'status'];

    protected $useTimestamps = true;
}