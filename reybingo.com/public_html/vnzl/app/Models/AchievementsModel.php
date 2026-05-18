<?php

namespace App\Models;

use CodeIgniter\Model;

class AchievementsModel extends Model
{
    protected $table = 'achievements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'description', 'icon', 'points', 'requirement_type', 
        'requirement_value', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all achievements
     *
     * @return array
     */
    public function getAllAchievements()
    {
        return $this->orderBy('requirement_value', 'ASC')->findAll();
    }
    
    /**
     * Get achievements by type
     *
     * @param string $type
     * @return array
     */
    public function getAchievementsByType($type)
    {
        return $this->where('requirement_type', $type)
                    ->orderBy('requirement_value', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get achievement by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getAchievementById($id)
    {
        return $this->find($id);
    }
    
    /**
     * Initialize achievements for a new user
     *
     * @param int $userId
     * @return bool
     */
    public function initializeUserAchievements($userId)
    {
        $userAchievementsModel = new \App\Models\UserAchievementsModel();
        $achievements = $this->findAll();
        
        foreach ($achievements as $achievement) {
            $userAchievementsModel->insert([
                'user_id' => $userId,
                'achievement_id' => $achievement['id'],
                'progress' => 0,
                'completed' => 0
            ]);
        }
        
        return true;
    }
    
    /**
     * Update user progress for a specific achievement type
     *
     * @param int $userId
     * @param string $type
     * @param int $value
     * @return array Array of completed achievements
     */
    public function updateUserProgress($userId, $type, $value = 1)
    {
        $userAchievementsModel = new \App\Models\UserAchievementsModel();
        $pointsModel = new \App\Models\PointsModel();
        $notificationsModel = new \App\Models\NotificationsModel();
        
        $achievements = $this->getAchievementsByType($type);
        $completedAchievements = [];
        
        foreach ($achievements as $achievement) {
            // Get user's current progress
            $userAchievement = $userAchievementsModel->where('user_id', $userId)
                                                    ->where('achievement_id', $achievement['id'])
                                                    ->first();
            
            if (!$userAchievement) {
                // Create if not exists
                $userAchievementsModel->insert([
                    'user_id' => $userId,
                    'achievement_id' => $achievement['id'],
                    'progress' => $value,
                    'completed' => ($value >= $achievement['requirement_value']) ? 1 : 0,
                    'completed_at' => ($value >= $achievement['requirement_value']) ? date('Y-m-d H:i:s') : null
                ]);
                
                if ($value >= $achievement['requirement_value']) {
                    $completedAchievements[] = $achievement;
                    
                    // Award points
                    $pointsModel->addPoints(
                        $userId, 
                        $achievement['points'], 
                        'achievement', 
                        $achievement['id'], 
                        "Logro desbloqueado: {$achievement['name']}"
                    );
                    
                    // Create notification
                    $notificationsModel->insert([
                        'user' => $userId,
                        'from' => 0,
                        'type' => 'achievement',
                        'type_id' => $achievement['id'],
                        'game' => 0,
                        'carton' => 0,
                        'modality' => 0,
                        'numbers' => '',
                        'title' => '🏅 ¡Nuevo logro desbloqueado!',
                        'message' => "Has desbloqueado el logro &quot;{$achievement['name']}&quot;. {$achievement['description']}",
                        'status' => 0
                    ]);
                }
            } else if (!$userAchievement['completed']) {
                // Update progress
                $newProgress = $userAchievement['progress'] + $value;
                $completed = ($newProgress >= $achievement['requirement_value']) ? 1 : 0;
                $completedAt = ($completed) ? date('Y-m-d H:i:s') : null;
                
                $userAchievementsModel->update($userAchievement['id'], [
                    'progress' => $newProgress,
                    'completed' => $completed,
                    'completed_at' => $completedAt
                ]);
                
                if ($completed) {
                    $completedAchievements[] = $achievement;
                    
                    // Award points
                    $pointsModel->addPoints(
                        $userId, 
                        $achievement['points'], 
                        'achievement', 
                        $achievement['id'], 
                        "Logro desbloqueado: {$achievement['name']}"
                    );
                    
                    // Create notification
                    $notificationsModel->insert([
                        'user' => $userId,
                        'from' => 0,
                        'type' => 'achievement',
                        'type_id' => $achievement['id'],
                        'game' => 0,
                        'carton' => 0,
                        'modality' => 0,
                        'numbers' => '',
                        'title' => '🏅 ¡Nuevo logro desbloqueado!',
                        'message' => "Has desbloqueado el logro &quot;{$achievement['name']}&quot;. {$achievement['description']}",
                        'status' => 0
                    ]);
                }
            }
        }
        
        return $completedAchievements;
    }
}