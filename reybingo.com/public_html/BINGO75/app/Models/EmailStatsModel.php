<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailStatsModel extends Model
{
    protected $table = 'email_stats';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'campaign_id', 'user_id', 'email', 'status', 'sent_at', 
        'opened_at', 'clicked_at', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get stats for a campaign
     *
     * @param int $campaignId
     * @return array
     */
    public function getCampaignStats($campaignId)
    {
        return $this->where('campaign_id', $campaignId)
                    ->findAll();
    }
    
    /**
     * Get stats for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserStats($userId)
    {
        return $this->select('email_stats.*, email_campaigns.name as campaign_name, email_campaigns.subject')
                    ->join('email_campaigns', 'email_campaigns.id = email_stats.campaign_id')
                    ->where('email_stats.user_id', $userId)
                    ->orderBy('email_stats.created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Create initial stats for a campaign
     *
     * @param int $campaignId
     * @param array $users
     * @return bool
     */
    public function createInitialStats($campaignId, $users)
    {
        $data = [];
        
        foreach ($users as $user) {
            $data[] = [
                'campaign_id' => $campaignId,
                'user_id' => $user['id'],
                'email' => $user['email'],
                'status' => 'pending'
            ];
        }
        
        if (empty($data)) {
            return false;
        }
        
        return $this->insertBatch($data);
    }
    
    /**
     * Update email status
     *
     * @param int $id
     * @param string $status
     * @param string|null $timestamp
     * @return bool
     */
    public function updateStatus($id, $status, $timestamp = null)
    {
        $data = ['status' => $status];
        
        switch ($status) {
            case 'sent':
                $data['sent_at'] = $timestamp ?? date('Y-m-d H:i:s');
                break;
            
            case 'opened':
                $data['opened_at'] = $timestamp ?? date('Y-m-d H:i:s');
                break;
            
            case 'clicked':
                $data['clicked_at'] = $timestamp ?? date('Y-m-d H:i:s');
                break;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Update email status by tracking data
     *
     * @param int $campaignId
     * @param string $email
     * @param string $status
     * @return bool
     */
    public function updateStatusByTracking($campaignId, $email, $status)
    {
        $stat = $this->where('campaign_id', $campaignId)
                     ->where('email', $email)
                     ->first();
        
        if (!$stat) {
            return false;
        }
        
        return $this->updateStatus($stat['id'], $status);
    }
    
    /**
     * Get pending emails for a campaign
     *
     * @param int $campaignId
     * @param int $limit
     * @return array
     */
    public function getPendingEmails($campaignId, $limit = 50)
    {
        return $this->select('email_stats.*, users.firstname, users.lastname')
                    ->join('users', 'users.id = email_stats.user_id')
                    ->where('email_stats.campaign_id', $campaignId)
                    ->where('email_stats.status', 'pending')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get email engagement metrics
     *
     * @param int $userId
     * @return array
     */
    public function getUserEngagementMetrics($userId)
    {
        $total = $this->where('user_id', $userId)->countAllResults();
        $opened = $this->where('user_id', $userId)->where('status', 'opened')->countAllResults();
        $clicked = $this->where('user_id', $userId)->where('status', 'clicked')->countAllResults();
        
        $openRate = ($total > 0) ? round(($opened / $total) * 100, 2) : 0;
        $clickRate = ($opened > 0) ? round(($clicked / $opened) * 100, 2) : 0;
        
        return [
            'total_emails' => $total,
            'opened' => $opened,
            'clicked' => $clicked,
            'open_rate' => $openRate,
            'click_rate' => $clickRate
        ];
    }
}