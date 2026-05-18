<?php

namespace App\Models;

use CodeIgniter\Model;

class LevelsModel extends Model
{
    protected $table = 'levels';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'description', 'required_points', 'icon', 'benefits', 
        'discount_percentage', 'free_cartons_per_day', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all levels ordered by required points
     *
     * @return array
     */
    public function getAllLevels()
    {
        return $this->orderBy('required_points', 'ASC')->findAll();
    }
    
    /**
     * Get level by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getLevelById($id)
    {
        return $this->find($id);
    }
    
    /**
     * Get level by points
     *
     * @param int $points
     * @return array
     */
    public function getLevelByPoints($points)
    {
        return $this->where('required_points <=', $points)
                    ->orderBy('required_points', 'DESC')
                    ->first();
    }
    
    /**
     * Get next level based on current level ID
     *
     * @param int $currentLevelId
     * @return array|null
     */
    public function getNextLevel($currentLevelId)
    {
        $currentLevel = $this->find($currentLevelId);
        
        if (!$currentLevel) {
            return null;
        }
        
        return $this->where('required_points >', $currentLevel['required_points'])
                    ->orderBy('required_points', 'ASC')
                    ->first();
    }
    
    /**
     * Calculate points needed for next level
     *
     * @param int $currentPoints
     * @return array with next level info and points needed
     */
    public function getPointsForNextLevel($currentPoints)
    {
        $currentLevel = $this->getLevelByPoints($currentPoints);
        $nextLevel = $this->getNextLevel($currentLevel['id']);
        
        if (!$nextLevel) {
            return [
                'current_level' => $currentLevel,
                'next_level' => null,
                'points_needed' => 0,
                'progress_percentage' => 100
            ];
        }
        
        $pointsNeeded = $nextLevel['required_points'] - $currentPoints;
        $totalPointsForLevel = $nextLevel['required_points'] - $currentLevel['required_points'];
        $progressPercentage = min(100, round(($currentPoints - $currentLevel['required_points']) / $totalPointsForLevel * 100));
        
        return [
            'current_level' => $currentLevel,
            'next_level' => $nextLevel,
            'points_needed' => $pointsNeeded,
            'progress_percentage' => $progressPercentage
        ];
    }
    
    /**
     * Check if user should level up based on points
     *
     * @param int $userId
     * @param int $currentPoints
     * @return bool|array Returns false if no level up, or array with new level info
     */
    public function checkLevelUp($userId, $currentPoints)
    {
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->find($userId);
        
        if (!$user) {
            return false;
        }
        
        $currentLevel = $this->find($user['level_id']);
        $newLevel = $this->getLevelByPoints($currentPoints);
        
        if (!$newLevel || $currentLevel['id'] == $newLevel['id']) {
            return false;
        }
        
        // User should level up
        $usersModel->update($userId, ['level_id' => $newLevel['id']]);
        
        return $newLevel;
    }
}