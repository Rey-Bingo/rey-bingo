<?php

namespace App\Models;

use CodeIgniter\Model;

class FirebaseTokenModel extends Model
{
    protected $table = 'firebase_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'token', 'device', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getTokensByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function getAllTokens()
    {
        return $this->findAll();
    }

    public function removeToken($token)
    {
        return $this->where('token', $token)->delete();
    }
}
