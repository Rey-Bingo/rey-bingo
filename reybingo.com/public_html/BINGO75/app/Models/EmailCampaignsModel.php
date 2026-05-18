<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailCampaignsModel extends Model
{
    protected $table = 'email_campaigns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'subject', 'content', 'template_id', 'segment', 
        'scheduled_at', 'sent_at', 'status', 'created_by', 
        'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all campaigns
     *
     * @return array
     */
    public function getAllCampaigns()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }
    
    /**
     * Get campaign by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getCampaignById($id)
    {
        return $this->find($id);
    }
    
    /**
     * Get campaigns by status
     *
     * @param string $status
     * @return array
     */
    public function getCampaignsByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get scheduled campaigns that are ready to send
     *
     * @return array
     */
    public function getReadyToSendCampaigns()
    {
        $now = date('Y-m-d H:i:s');
        
        return $this->where('status', 'scheduled')
                    ->where('scheduled_at <=', $now)
                    ->orderBy('scheduled_at', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get campaigns created by a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserCampaigns($userId)
    {
        return $this->where('created_by', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Create a new campaign
     *
     * @param array $data
     * @return int|false
     */
    public function createCampaign($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Update campaign status
     *
     * @param int $id
     * @param string $status
     * @param string|null $sentAt
     * @return bool
     */
    public function updateStatus($id, $status, $sentAt = null)
    {
        $data = ['status' => $status];
        
        if ($status == 'sent' && $sentAt === null) {
            $data['sent_at'] = date('Y-m-d H:i:s');
        } else if ($sentAt !== null) {
            $data['sent_at'] = $sentAt;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Get campaign statistics
     *
     * @param int $id
     * @return array
     */
    public function getCampaignStats($id)
    {
        $emailStatsModel = new \App\Models\EmailStatsModel();
        
        $campaign = $this->find($id);
        
        if (!$campaign) {
            return [
                'total' => 0,
                'sent' => 0,
                'delivered' => 0,
                'opened' => 0,
                'clicked' => 0,
                'bounced' => 0,
                'open_rate' => 0,
                'click_rate' => 0
            ];
        }
        
        $total = $emailStatsModel->where('campaign_id', $id)->countAllResults();
        $sent = $emailStatsModel->where('campaign_id', $id)->where('status !=', 'pending')->countAllResults();
        $delivered = $emailStatsModel->where('campaign_id', $id)->where('status', 'delivered')->countAllResults();
        $opened = $emailStatsModel->where('campaign_id', $id)->where('status', 'opened')->countAllResults();
        $clicked = $emailStatsModel->where('campaign_id', $id)->where('status', 'clicked')->countAllResults();
        $bounced = $emailStatsModel->where('campaign_id', $id)->where('status', 'bounced')->countAllResults();
        
        $openRate = ($delivered > 0) ? round(($opened / $delivered) * 100, 2) : 0;
        $clickRate = ($opened > 0) ? round(($clicked / $opened) * 100, 2) : 0;
        
        return [
            'total' => $total,
            'sent' => $sent,
            'delivered' => $delivered,
            'opened' => $opened,
            'clicked' => $clicked,
            'bounced' => $bounced,
            'open_rate' => $openRate,
            'click_rate' => $clickRate
        ];
    }
    
    /**
     * Get users for a campaign segment
     *
     * @param string $segment
     * @return array
     */
    public function getUsersForSegment($segment)
    {
        $usersModel = new \App\Models\UsersModel();
        $userPackagesModel = new \App\Models\UserPackagesModel();
        
        switch ($segment) {
            case 'all':
                return $usersModel->where('status', 1)
                                 ->where('verified_email', 1)
                                 ->findAll();
            
            case 'pro':
                return $usersModel->where('status', 1)
                                 ->where('verified_email', 1)
                                 ->where('is_pro', 1)
                                 ->findAll();
            
            case 'non_pro':
                return $usersModel->where('status', 1)
                                 ->where('verified_email', 1)
                                 ->where('is_pro', 0)
                                 ->findAll();
            
            case 'inactive':
                $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
                
                return $usersModel->select('users.*')
                                 ->join('cartons', 'cartons.user = users.id', 'left')
                                 ->where('users.status', 1)
                                 ->where('users.verified_email', 1)
                                 ->where('users.created_at <', $thirtyDaysAgo)
                                 ->groupBy('users.id')
                                 ->having('COUNT(cartons.id) = 0')
                                 ->findAll();
            
            default:
                // Check if segment is a level
                if (strpos($segment, 'level_') === 0) {
                    $levelId = substr($segment, 6);
                    
                    return $usersModel->where('status', 1)
                                     ->where('verified_email', 1)
                                     ->where('level_id', $levelId)
                                     ->findAll();
                }
                
                return [];
        }
    }
}