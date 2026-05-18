<?php

namespace App\Models;

use CodeIgniter\Model;

class PushSubscriptionModel extends Model
{
    protected $table = 'push_subscriptions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'endpoint', 'p256dh_key', 'auth_key', 'user_agent'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getSubscriptionsByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function getAllActiveSubscriptions()
    {
        return $this->findAll();
    }

    public function removeSubscription($endpoint)
    {
        return $this->where('endpoint', $endpoint)->delete();
    }
}
