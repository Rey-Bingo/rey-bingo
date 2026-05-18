<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPackagesModel extends Model
{
    protected $table = 'user_packages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'package_id', 'start_date', 'end_date', 
        'payment_id', 'is_active', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get active package for a user
     *
     * @param int $userId
     * @return array|null
     */
    public function getActivePackage($userId)
    {
        $now = date('Y-m-d H:i:s');
        
        return $this->select('user_packages.*, packages.name, packages.description, packages.benefits, packages.discount_percentage, packages.free_cartons, packages.daily_points')
                    ->join('packages', 'packages.id = user_packages.package_id')
                    ->where('user_packages.user_id', $userId)
                    ->where('user_packages.is_active', 1)
                    ->where('user_packages.end_date >', $now)
                    ->orderBy('user_packages.end_date', 'DESC')
                    ->first();
    }
    
    /**
     * Get all packages for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserPackages($userId)
    {
        return $this->select('user_packages.*, packages.name, packages.description')
                    ->join('packages', 'packages.id = user_packages.package_id')
                    ->where('user_packages.user_id', $userId)
                    ->orderBy('user_packages.created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Subscribe user to a package
     *
     * @param int $userId
     * @param int $packageId
     * @param int $paymentId
     * @return bool
     */
    public function subscribeUser($userId, $packageId, $paymentId)
    {
        $packagesModel = new \App\Models\PackagesModel();
        $usersModel = new \App\Models\UsersModel();
        
        $startDate = date('Y-m-d H:i:s');
        $endDate = $packagesModel->calculateEndDate($packageId, $startDate);
        
        if (!$endDate) {
            return false;
        }
        
        // Deactivate any existing active packages
        $this->where('user_id', $userId)
             ->where('is_active', 1)
             ->set(['is_active' => 0])
             ->update();
        
        // Create new subscription
        $data = [
            'user_id' => $userId,
            'package_id' => $packageId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_id' => $paymentId,
            'is_active' => 1
        ];
        
        $result = $this->insert($data);
        
        if ($result) {
            // Update user's pro status
            $usersModel->update($userId, [
                'is_pro' => 1,
                'pro_until' => $endDate
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user has an active subscription
     *
     * @param int $userId
     * @return bool
     */
    public function hasActiveSubscription($userId)
    {
        $now = date('Y-m-d H:i:s');
        
        $count = $this->where('user_id', $userId)
                      ->where('is_active', 1)
                      ->where('end_date >', $now)
                      ->countAllResults();
        
        return $count > 0;
    }
    
    /**
     * Check for expired subscriptions and update user status
     */
    public function processExpiredSubscriptions()
    {
        $now = date('Y-m-d H:i:s');
        $usersModel = new \App\Models\UsersModel();
        
        // Find expired subscriptions
        $expiredSubs = $this->where('is_active', 1)
                            ->where('end_date <', $now)
                            ->findAll();
        
        foreach ($expiredSubs as $sub) {
            // Deactivate subscription
            $this->update($sub['id'], ['is_active' => 0]);
            
            // Check if user has any other active subscription
            $hasOtherActive = $this->where('user_id', $sub['user_id'])
                                   ->where('id !=', $sub['id'])
                                   ->where('is_active', 1)
                                   ->where('end_date >', $now)
                                   ->countAllResults();
            
            if (!$hasOtherActive) {
                // Update user's pro status
                $usersModel->update($sub['user_id'], [
                    'is_pro' => 0,
                    'pro_until' => null
                ]);
            }
        }
    }
}