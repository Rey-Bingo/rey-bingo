<?php

namespace App\Models;

use CodeIgniter\Model;

class PointsModel extends Model
{
    protected $table = 'points';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'amount', 'type', 'source', 'source_id', 
        'description', 'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    /**
     * Add points to a user
     *
     * @param int $userId
     * @param int $amount
     * @param string $source
     * @param int|null $sourceId
     * @param string $description
     * @return bool
     */
    public function addPoints($userId, $amount, $source, $sourceId = null, $description = '')
    {
        if ($amount <= 0) {
            return false;
        }
        
        $usersModel = new \App\Models\UsersModel();
        $levelsModel = new \App\Models\LevelsModel();
        
        $user = $usersModel->find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Insert points record
        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'earned',
            'source' => $source,
            'source_id' => $sourceId,
            'description' => $description
        ];
        
        $result = $this->insert($data);
        
        if ($result) {
            // Update user's points
            $newTotalPoints = $user['total_points'] + $amount;
            $newCurrentPoints = $user['current_points'] + $amount;
            
            $usersModel->update($userId, [
                'total_points' => $newTotalPoints,
                'current_points' => $newCurrentPoints
            ]);
            
            // Check for level up
            $levelsModel->checkLevelUp($userId, $newTotalPoints);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Spend points from a user
     *
     * @param int $userId
     * @param int $amount
     * @param string $source
     * @param int|null $sourceId
     * @param string $description
     * @return bool
     */
    public function spendPoints($userId, $amount, $source, $sourceId = null, $description = '')
    {
        if ($amount <= 0) {
            return false;
        }
        
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->find($userId);
        
        if (!$user || $user['current_points'] < $amount) {
            return false;
        }
        
        // Insert points record
        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'spent',
            'source' => $source,
            'source_id' => $sourceId,
            'description' => $description
        ];
        
        $result = $this->insert($data);
        
        if ($result) {
            // Update user's current points
            $newCurrentPoints = $user['current_points'] - $amount;
            
            $usersModel->update($userId, [
                'current_points' => $newCurrentPoints
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get points history for a user
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUserPointsHistory($userId, $limit = 20, $offset = 0)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }
    
    /**
     * Get points summary for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserPointsSummary($userId)
    {
        $earned = $this->selectSum('amount')
                       ->where('user_id', $userId)
                       ->where('type', 'earned')
                       ->first()['amount'] ?? 0;
        
        $spent = $this->selectSum('amount')
                      ->where('user_id', $userId)
                      ->where('type', 'spent')
                      ->first()['amount'] ?? 0;
        
        $expired = $this->selectSum('amount')
                        ->where('user_id', $userId)
                        ->where('type', 'expired')
                        ->first()['amount'] ?? 0;
        
        return [
            'earned' => $earned,
            'spent' => $spent,
            'expired' => $expired,
            'current' => $earned - $spent - $expired
        ];
    }
    
    /**
     * Award daily points to a user
     *
     * @param int $userId
     * @return bool|array Returns false if failed, or array with points info
     */
    public function awardDailyPoints($userId)
    {
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->find($userId);
        
        if (!$user) {
            return false;
        }
        
        $today = date('Y-m-d');
        
        // Check if user already received daily points today
        if ($user['last_daily_check'] == $today) {
            return false;
        }
        
        // Calculate consecutive days
        $consecutiveDays = $user['consecutive_days'];
        $lastCheck = $user['last_daily_check'];
        
        if ($lastCheck) {
            $dayDiff = (strtotime($today) - strtotime($lastCheck)) / (60 * 60 * 24);
            
            if ($dayDiff == 1) {
                // User checked in consecutive day
                $consecutiveDays++;
            } else if ($dayDiff > 1) {
                // User broke the streak
                $consecutiveDays = 1;
            }
        } else {
            // First time check-in
            $consecutiveDays = 1;
        }
        
        // Calculate points to award
        $basePoints = 10;
        $bonusPoints = min(5, $consecutiveDays) * 2; // Max 5 days streak bonus
        $totalPoints = $basePoints + $bonusPoints;
        
        // Add pro user bonus if applicable
        if ($user['is_pro']) {
            $userPackagesModel = new \App\Models\UserPackagesModel();
            $activePackage = $userPackagesModel->getActivePackage($userId);
            
            if ($activePackage) {
                $totalPoints += $activePackage['daily_points'];
            }
        }
        
        // Add level bonus
        $levelsModel = new \App\Models\LevelsModel();
        $userLevel = $levelsModel->find($user['level_id']);
        
        if ($userLevel && $userLevel['free_cartons_per_day'] > 0) {
            $totalPoints += $userLevel['free_cartons_per_day'] * 5;
        }
        
        // Update user's consecutive days and last check
        $usersModel->update($userId, [
            'consecutive_days' => $consecutiveDays,
            'last_daily_check' => $today
        ]);
        
        // Add the points
        $description = "Bono diario - Día consecutivo {$consecutiveDays}";
        $this->addPoints($userId, $totalPoints, 'daily', null, $description);
        
        return [
            'points' => $totalPoints,
            'consecutive_days' => $consecutiveDays,
            'base_points' => $basePoints,
            'streak_bonus' => $bonusPoints,
            'pro_bonus' => $user['is_pro'] ? ($activePackage['daily_points'] ?? 0) : 0,
            'level_bonus' => $userLevel['free_cartons_per_day'] * 5
        ];
    }
}