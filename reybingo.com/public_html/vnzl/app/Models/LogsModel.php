<?php

namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model 
{
    protected $table      = 'logs';
    protected $primaryKey = 'id';

    protected $allowedFields = ['id_user', 'action', 'details', 'data', 'ip_address', 'country', 'user_agent'];

    protected $useTimestamps = true;
}
