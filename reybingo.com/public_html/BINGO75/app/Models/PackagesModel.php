<?php

namespace App\Models;

use CodeIgniter\Model;

class PackagesModel extends Model
{
    protected $table = 'packages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'description', 'price', 'duration_days', 'benefits', 
        'discount_percentage', 'free_cartons', 'daily_points', 
        'created_at', 'updated_at', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all active packages
     *
     * @return array
     */
    public function getActivePackages()
    {
        return $this->where('status', 1)->findAll();
    }
    
    /**
     * Get package by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getPackageById($id)
    {
        return $this->find($id);
    }
    
    /**
     * Calculate the end date based on package duration
     *
     * @param int $packageId
     * @param string $startDate
     * @return string
     */
    public function calculateEndDate($packageId, $startDate = null)
    {
        $package = $this->find($packageId);
        
        if (!$package) {
            return null;
        }
        
        if ($startDate === null) {
            $startDate = date('Y-m-d H:i:s');
        }
        
        return date('Y-m-d H:i:s', strtotime($startDate . ' + ' . $package['duration_days'] . ' days'));
    }
}