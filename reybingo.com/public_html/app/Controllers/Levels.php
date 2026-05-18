<?php

namespace App\Controllers;

use App\Models\LevelsModel;
use App\Models\UsersModel;
use App\Models\PointsModel;
use App\Models\AchievementsModel;
use App\Models\UserAchievementsModel;
use App\Models\NotificationTemplatesModel;

class Levels extends BaseController
{
    protected $levelsModel;
    protected $usersModel;
    protected $pointsModel;
    protected $achievementsModel;
    protected $userAchievementsModel;
    protected $notificationTemplatesModel;
    
    public function __construct()
    {
        $this->levelsModel = new LevelsModel();
        $this->usersModel = new UsersModel();
        $this->pointsModel = new PointsModel();
        $this->achievementsModel = new AchievementsModel();
        $this->userAchievementsModel = new UserAchievementsModel();
        $this->notificationTemplatesModel = new NotificationTemplatesModel();
    }
    
    /**
     * Display user level page
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('id');
        $user = $this->usersModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/signin');
        }
        
        $levels = $this->levelsModel->getAllLevels();
        $currentLevel = $this->levelsModel->find($user['level_id']);
        $nextLevelInfo = $this->levelsModel->getPointsForNextLevel($user['total_points']);
        $pointsHistory = $this->pointsModel->getUserPointsHistory($userId, 10);
        $pointsSummary = $this->pointsModel->getUserPointsSummary($userId);
        
        $data = [
            'title' => 'Mi Nivel',
            'user' => $user,
            'levels' => $levels,
            'currentLevel' => $currentLevel,
            'nextLevelInfo' => $nextLevelInfo,
            'pointsHistory' => $pointsHistory,
            'pointsSummary' => $pointsSummary
        ];
        
        return view('levels/index', $data);
    }
    
    /**
     * Display points history
     */
    public function points()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('id');
        $user = $this->usersModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/signin');
        }
        
        $pointsHistory = $this->pointsModel->getUserPointsHistory($userId, 50);
        $pointsSummary = $this->pointsModel->getUserPointsSummary($userId);
        
        $data = [
            'title' => 'Historial de Puntos',
            'user' => $user,
            'pointsHistory' => $pointsHistory,
            'pointsSummary' => $pointsSummary
        ];
        
        return view('levels/points', $data);
    }
    
    /**
     * Display achievements page
     */
    public function achievements()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('id');
        $user = $this->usersModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/signin');
        }
        
        $userAchievements = $this->userAchievementsModel->getUserAchievements($userId);
        $completedAchievements = $this->userAchievementsModel->getCompletedAchievements($userId);
        $incompleteAchievements = $this->userAchievementsModel->getIncompleteAchievements($userId);
        $achievementStats = $this->userAchievementsModel->getUserAchievementStats($userId);
        
        $data = [
            'title' => 'Mis Logros',
            'user' => $user,
            'userAchievements' => $userAchievements,
            'completedAchievements' => $completedAchievements,
            'incompleteAchievements' => $incompleteAchievements,
            'achievementStats' => $achievementStats
        ];
        
        return view('levels/achievements', $data);
    }
    
    /**
     * Claim daily bonus
     */
    public function dailyBonus()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ]);
        }
        
        $userId = session()->get('id');
        $result = $this->pointsModel->awardDailyPoints($userId);
        
        if (!$result) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ya has reclamado tu bono diario hoy'
            ]);
        }
        
        // Update achievements
        $this->achievementsModel->updateUserProgress($userId, 'consecutive_days', 0); // Just check, don't increment
        
        // Create notification
        $this->notificationTemplatesModel->createNotificationFromTemplate(
            'Bono Diario',
            [
                'type' => 'daily_bonus',
                'daily_bonus' => $result['points']
            ],
            $userId
        );
        
        return $this->response->setJSON([
            'success' => true,
            'message' => '¡Bono diario reclamado!',
            'data' => $result
        ]);
    }
    
    /**
     * Admin: Manage levels
     */
    public function manage()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        $levels = $this->levelsModel->getAllLevels();
        
        $data = [
            'title' => 'Administrar Niveles',
            'levels' => $levels
        ];
        
        return view('levels/manage', $data);
    }
    
    /**
     * Admin: Create level
     */
    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'description' => 'required',
                'required_points' => 'required|integer',
                'discount_percentage' => 'required|numeric',
                'free_cartons_per_day' => 'required|integer'
            ];
            
            if ($this->validate($rules)) {
                // Handle icon upload
                $icon = $this->request->getFile('icon');
                $iconName = 'level-default.png';
                
                if ($icon->isValid() && !$icon->hasMoved()) {
                    $newName = 'level-' . time() . '.' . $icon->getExtension();
                    $icon->move(ROOTPATH . 'public/uploads/levels', $newName);
                    $iconName = $newName;
                }
                
                $data = [
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'required_points' => $this->request->getPost('required_points'),
                    'icon' => $iconName,
                    'benefits' => $this->request->getPost('benefits'),
                    'discount_percentage' => $this->request->getPost('discount_percentage'),
                    'free_cartons_per_day' => $this->request->getPost('free_cartons_per_day')
                ];
                
                $this->levelsModel->insert($data);
                
                return redirect()->to('/levels/manage')->with('success', 'Nivel creado exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $data = [
            'title' => 'Crear Nivel'
        ];
        
        return view('levels/create', $data);
    }
    
    /**
     * Admin: Edit level
     */
    public function edit($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/levels/manage');
        }
        
        $level = $this->levelsModel->find($id);
        
        if (!$level) {
            return redirect()->to('/levels/manage')->with('error', 'Nivel no encontrado');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'description' => 'required',
                'required_points' => 'required|integer',
                'discount_percentage' => 'required|numeric',
                'free_cartons_per_day' => 'required|integer'
            ];
            
            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'required_points' => $this->request->getPost('required_points'),
                    'benefits' => $this->request->getPost('benefits'),
                    'discount_percentage' => $this->request->getPost('discount_percentage'),
                    'free_cartons_per_day' => $this->request->getPost('free_cartons_per_day')
                ];
                
                // Handle icon upload
                $icon = $this->request->getFile('icon');
                
                if ($icon->isValid() && !$icon->hasMoved()) {
                    $newName = 'level-' . time() . '.' . $icon->getExtension();
                    $icon->move(ROOTPATH . 'public/uploads/levels', $newName);
                    $data['icon'] = $newName;
                }
                
                $this->levelsModel->update($id, $data);
                
                return redirect()->to('/levels/manage')->with('success', 'Nivel actualizado exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $data = [
            'title' => 'Editar Nivel',
            'level' => $level
        ];
        
        return view('levels/edit', $data);
    }
    
    /**
     * Admin: Delete level
     */
    public function delete($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/levels/manage');
        }
        
        // Check if it's the base level (ID 1)
        if ($id == 1) {
            return redirect()->to('/levels/manage')->with('error', 'No se puede eliminar el nivel base');
        }
        
        $level = $this->levelsModel->find($id);
        
        if (!$level) {
            return redirect()->to('/levels/manage')->with('error', 'Nivel no encontrado');
        }
        
        // Check if any users are at this level
        $usersAtLevel = $this->usersModel->where('level_id', $id)->countAllResults();
        
        if ($usersAtLevel > 0) {
            return redirect()->to('/levels/manage')->with('error', 'No se puede eliminar un nivel que tiene usuarios asignados');
        }
        
        $this->levelsModel->delete($id);
        
        return redirect()->to('/levels/manage')->with('success', 'Nivel eliminado exitosamente');
    }
    
    /**
     * Admin: Manage achievements
     */
    public function manageAchievements()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        $achievements = $this->achievementsModel->getAllAchievements();
        
        $data = [
            'title' => 'Administrar Logros',
            'achievements' => $achievements
        ];
        
        return view('levels/manage_achievements', $data);
    }
    
    /**
     * Admin: Create achievement
     */
    public function createAchievement()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'description' => 'required',
                'points' => 'required|integer',
                'requirement_type' => 'required',
                'requirement_value' => 'required|integer'
            ];
            
            if ($this->validate($rules)) {
                // Handle icon upload
                $icon = $this->request->getFile('icon');
                $iconName = 'achievement-default.png';
                
                if ($icon->isValid() && !$icon->hasMoved()) {
                    $newName = 'achievement-' . time() . '.' . $icon->getExtension();
                    $icon->move(ROOTPATH . 'public/uploads/achievements', $newName);
                    $iconName = $newName;
                }
                
                $data = [
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'icon' => $iconName,
                    'points' => $this->request->getPost('points'),
                    'requirement_type' => $this->request->getPost('requirement_type'),
                    'requirement_value' => $this->request->getPost('requirement_value')
                ];
                
                $achievementId = $this->achievementsModel->insert($data);
                
                // Initialize achievement for all users
                $users = $this->usersModel->where('status', 1)->findAll();
                
                foreach ($users as $user) {
                    $this->userAchievementsModel->insert([
                        'user_id' => $user['id'],
                        'achievement_id' => $achievementId,
                        'progress' => 0,
                        'completed' => 0
                    ]);
                }
                
                return redirect()->to('/levels/manageAchievements')->with('success', 'Logro creado exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $data = [
            'title' => 'Crear Logro'
        ];
        
        return view('levels/create_achievement', $data);
    }
    
    /**
     * Admin: Edit achievement
     */
    public function editAchievement($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/levels/manageAchievements');
        }
        
        $achievement = $this->achievementsModel->find($id);
        
        if (!$achievement) {
            return redirect()->to('/levels/manageAchievements')->with('error', 'Logro no encontrado');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'description' => 'required',
                'points' => 'required|integer',
                'requirement_type' => 'required',
                'requirement_value' => 'required|integer'
            ];
            
            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'points' => $this->request->getPost('points'),
                    'requirement_type' => $this->request->getPost('requirement_type'),
                    'requirement_value' => $this->request->getPost('requirement_value')
                ];
                
                // Handle icon upload
                $icon = $this->request->getFile('icon');
                
                if ($icon->isValid() && !$icon->hasMoved()) {
                    $newName = 'achievement-' . time() . '.' . $icon->getExtension();
                    $icon->move(ROOTPATH . 'public/uploads/achievements', $newName);
                    $data['icon'] = $newName;
                }
                
                $this->achievementsModel->update($id, $data);
                
                return redirect()->to('/levels/manageAchievements')->with('success', 'Logro actualizado exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $data = [
            'title' => 'Editar Logro',
            'achievement' => $achievement
        ];
        
        return view('levels/edit_achievement', $data);
    }
    
    /**
     * Admin: Delete achievement
     */
    public function deleteAchievement($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/levels/manageAchievements');
        }
        
        $achievement = $this->achievementsModel->find($id);
        
        if (!$achievement) {
            return redirect()->to('/levels/manageAchievements')->with('error', 'Logro no encontrado');
        }
        
        // Delete user achievements first
        $this->userAchievementsModel->where('achievement_id', $id)->delete();
        
        // Then delete the achievement
        $this->achievementsModel->delete($id);
        
        return redirect()->to('/levels/manageAchievements')->with('success', 'Logro eliminado exitosamente');
    }
    
    /**
     * Admin: Award points to user
     */
    public function awardPoints()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'user_id' => 'required|integer',
                'amount' => 'required|integer',
                'description' => 'required'
            ];
            
            if ($this->validate($rules)) {
                $userId = $this->request->getPost('user_id');
                $amount = $this->request->getPost('amount');
                $description = $this->request->getPost('description');
                
                $user = $this->usersModel->find($userId);
                
                if (!$user) {
                    return redirect()->back()->with('error', 'Usuario no encontrado');
                }
                
                $this->pointsModel->addPoints($userId, $amount, 'admin', session()->get('id'), $description);
                
                return redirect()->to('/users/view/' . $userId)->with('success', 'Puntos otorgados exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $users = $this->usersModel->where('status', 1)->findAll();
        
        $data = [
            'title' => 'Otorgar Puntos',
            'users' => $users
        ];
        
        return view('levels/award_points', $data);
    }
    
    /**
     * Admin: Reset user achievements
     */
    public function resetAchievements($userId = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($userId === null) {
            return redirect()->to('/users');
        }
        
        $user = $this->usersModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/users')->with('error', 'Usuario no encontrado');
        }
        
        // Delete existing user achievements
        $this->userAchievementsModel->where('user_id', $userId)->delete();
        
        // Initialize achievements for user
        $this->achievementsModel->initializeUserAchievements($userId);
        
        return redirect()->to('/users/view/' . $userId)->with('success', 'Logros del usuario reiniciados exitosamente');
    }
}