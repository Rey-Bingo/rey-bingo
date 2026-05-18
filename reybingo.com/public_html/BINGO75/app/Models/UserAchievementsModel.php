<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAchievementsModel extends Model
{
    protected $table = 'user_achievements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'achievement_id', 'progress', 'completed', 
        'completed_at', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all achievements for a user with details
     *
     * @param int $userId
     * @return array
     */
    public function getUserAchievements($userId)
    {
        return $this->select('user_achievements.*, achievements.name, achievements.description, achievements.icon, achievements.points, achievements.requirement_type, achievements.requirement_value')
                    ->join('achievements', 'achievements.id = user_achievements.achievement_id')
                    ->where('user_achievements.user_id', $userId)
                    ->orderBy('achievements.requirement_type', 'ASC')
                    ->orderBy('achievements.requirement_value', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get completed achievements for a user
     *
     * @param int $userId
     * @return array
     */
    public function getCompletedAchievements($userId)
    {
        return $this->select('user_achievements.*, achievements.name, achievements.description, achievements.icon, achievements.points, achievements.requirement_type, achievements.requirement_value')
                    ->join('achievements', 'achievements.id = user_achievements.achievement_id')
                    ->where('user_achievements.user_id', $userId)
                    ->where('user_achievements.completed', 1)
                    ->orderBy('user_achievements.completed_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get incomplete achievements for a user
     *
     * @param int $userId
     * @return array
     */
    public function getIncompleteAchievements($userId)
    {
        return $this->select('user_achievements.*, achievements.name, achievements.description, achievements.icon, achievements.points, achievements.requirement_type, achievements.requirement_value')
                    ->join('achievements', 'achievements.id = user_achievements.achievement_id')
                    ->where('user_achievements.user_id', $userId)
                    ->where('user_achievements.completed', 0)
                    ->orderBy('achievements.requirement_type', 'ASC')
                    ->orderBy('achievements.requirement_value', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get achievement progress for a user
     *
     * @param int $userId
     * @param int $achievementId
     * @return array|null
     */
    public function getUserAchievementProgress($userId, $achievementId)
    {
        return $this->select('user_achievements.*, achievements.name, achievements.description, achievements.icon, achievements.points, achievements.requirement_type, achievements.requirement_value')
                    ->join('achievements', 'achievements.id = user_achievements.achievement_id')
                    ->where('user_achievements.user_id', $userId)
                    ->where('user_achievements.achievement_id', $achievementId)
                    ->first();
    }
    
    /**
     * Get achievement progress percentage for a user
     *
     * @param int $userId
     * @param int $achievementId
     * @return int
     */
    public function getProgressPercentage($userId, $achievementId)
    {
        $progress = $this->getUserAchievementProgress($userId, $achievementId);
        
        if (!$progress) {
            return 0;
        }
        
        $percentage = min(100, round(($progress['progress'] / $progress['requirement_value']) * 100));
        
        return $percentage;
    }
    
    /**
     * Get user achievement statistics
     *
     * @param int $userId
     * @return array
     */
    public function getUserAchievementStats($userId)
    {
        $total = $this->where('user_id', $userId)->countAllResults();
        $completed = $this->where('user_id', $userId)->where('completed', 1)->countAllResults();
        $percentage = ($total > 0) ? round(($completed / $total) * 100) : 0;
        
        // Get total points from achievements
        $points = $this->selectSum('achievements.points')
                       ->join('achievements', 'achievements.id = user_achievements.achievement_id')
                       ->where('user_achievements.user_id', $userId)
                       ->where('user_achievements.completed', 1)
                       ->first();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $total - $completed,
            'percentage' => $percentage,
            'points' => $points['points'] ?? 0
        ];
    }
    
    /**
     * Get recent achievements for a user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentAchievements($userId, $limit = 5)
    {
        return $this->select('user_achievements.*, achievements.name, achievements.description, achievements.icon, achievements.points')
                    ->join('achievements', 'achievements.id = user_achievements.achievement_id')
                    ->where('user_achievements.user_id', $userId)
                    ->where('user_achievements.completed', 1)
                    ->orderBy('user_achievements.completed_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}