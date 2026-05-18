<?php

namespace App\Models;

use CodeIgniter\Model;

class TempCartonsModel extends Model {
    protected $table = 'temp_cartons';
    protected $primaryKey = 'id'; 

    protected $allowedFields = ['carton', 'user', 'game', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    
    public function cleanExpired($minutes = 5) {
        $expiredTime = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        return $this->where('created_at <', $expiredTime)->delete();
    }
    
    public function getExpiredByUser($userId, $gameId, $minutes = 5) {
        $expiredTime = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        return $this->select('carton')->where('user', $userId)->where('game', $gameId)->where('created_at <', $expiredTime)->findAll();
    }
}
