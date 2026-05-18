<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferralsModel extends Model 
{
    protected $table      = 'referrals';
    protected $primaryKey = 'id';

    protected $allowedFields = ['id_referred', 'id_referrer', 'amount', 'created_at', 'updated_at', 'status'];

    protected $useTimestamps = true; 
}

