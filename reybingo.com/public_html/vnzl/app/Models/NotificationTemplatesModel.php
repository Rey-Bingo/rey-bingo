<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationTemplatesModel extends Model
{
    protected $table = 'notification_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'title_template', 'message_template', 'image_url', 
        'action_url_template', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all templates
     *
     * @return array
     */
    public function getAllTemplates()
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
    
    /**
     * Get template by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getTemplateById($id)
    {
        return $this->find($id);
    }
    
    /**
     * Get template by name
     *
     * @param string $name
     * @return array|null
     */
    public function getTemplateByName($name)
    {
        return $this->where('name', $name)->first();
    }
    
    /**
     * Create a notification from a template
     *
     * @param string $templateName
     * @param array $data
     * @param int $userId
     * @param int $fromId
     * @return bool|int
     */
    public function createNotificationFromTemplate($templateName, $data, $userId, $fromId = 0)
    {
        $template = $this->getTemplateByName($templateName);
        
        if (!$template) {
            return false;
        }
        
        $notificationsModel = new \App\Models\NotificationsModel();
        
        // Replace placeholders in template
        $title = $this->replacePlaceholders($template['title_template'], $data);
        $message = $this->replacePlaceholders($template['message_template'], $data);
        $actionUrl = $this->replacePlaceholders($template['action_url_template'], $data);
        
        $notificationData = [
            'user' => $userId,
            'from' => $fromId,
            'type' => $data['type'] ?? '',
            'type_id' => $data['type_id'] ?? 0,
            'game' => $data['game'] ?? 0,
            'carton' => $data['carton'] ?? 0,
            'modality' => $data['modality'] ?? 0,
            'numbers' => $data['numbers'] ?? '',
            'title' => $title,
            'message' => $message,
            'template_id' => $template['id'],
            'action_url' => $actionUrl,
            'image_url' => $template['image_url'],
            'status' => 0
        ];
        
        // Add scheduled_at if provided
        if (isset($data['scheduled_at'])) {
            $notificationData['scheduled_at'] = $data['scheduled_at'];
        }
        
        return $notificationsModel->insert($notificationData);
    }
    
    /**
     * Create notifications for multiple users from a template
     *
     * @param string $templateName
     * @param array $data
     * @param array $userIds
     * @param int $fromId
     * @return bool
     */
    public function createBulkNotificationsFromTemplate($templateName, $data, $userIds, $fromId = 0)
    {
        $template = $this->getTemplateByName($templateName);
        
        if (!$template) {
            return false;
        }
        
        $notificationsModel = new \App\Models\NotificationsModel();
        
        // Replace placeholders in template
        $title = $this->replacePlaceholders($template['title_template'], $data);
        $message = $this->replacePlaceholders($template['message_template'], $data);
        $actionUrl = $this->replacePlaceholders($template['action_url_template'], $data);
        
        $notificationsData = [];
        
        foreach ($userIds as $userId) {
            $notificationsData[] = [
                'user' => $userId,
                'from' => $fromId,
                'type' => $data['type'] ?? '',
                'type_id' => $data['type_id'] ?? 0,
                'game' => $data['game'] ?? 0,
                'carton' => $data['carton'] ?? 0,
                'modality' => $data['modality'] ?? 0,
                'numbers' => $data['numbers'] ?? '',
                'title' => $title,
                'message' => $message,
                'template_id' => $template['id'],
                'action_url' => $actionUrl,
                'image_url' => $template['image_url'],
                'status' => 0,
                'scheduled_at' => $data['scheduled_at'] ?? null
            ];
        }
        
        if (empty($notificationsData)) {
            return false;
        }
        
        return $notificationsModel->insertBatch($notificationsData);
    }
    
    /**
     * Replace placeholders in a template string
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    private function replacePlaceholders($template, $data)
    {
        if (empty($template)) {
            return '';
        }
        
        $result = $template;
        
        foreach ($data as $key => $value) {
            $result = str_replace('{{' . $key . '}}', $value, $result);
        }
        
        return $result;
    }
}