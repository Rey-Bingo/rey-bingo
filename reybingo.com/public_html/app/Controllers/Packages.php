<?php

namespace App\Controllers;

use App\Models\PackagesModel;
use App\Models\UserPackagesModel;
use App\Models\PaymentsModel;
use App\Models\UsersModel;
use App\Models\PointsModel;
use App\Models\NotificationTemplatesModel;
use App\Models\ContactsModel;
use CodeIgniter\Controller;

class Packages extends Controller
{
    protected $packagesModel;
    protected $userPackagesModel;
    protected $paymentsModel;
    protected $usersModel;
    protected $pointsModel;
    protected $notificationTemplatesModel;
    
    public function __construct()
    {
        $this->packagesModel = new PackagesModel();
        $this->userPackagesModel = new UserPackagesModel();
        $this->paymentsModel = new PaymentsModel();
        $this->usersModel = new UsersModel();
        $this->pointsModel = new PointsModel();
        $this->notificationTemplatesModel = new NotificationTemplatesModel();

        helper(['form', 'url', 'cookie', 'text']);
        session();
    }
    
    /**
     * Display packages page
     */
    public function index() {
        if (!session()->get('logged_in') || session()->get('group') != 1) {
            return redirect()->to('/signin');
        }

        $modelUsers = new UsersModel();
        $modelContacts = new ContactsModel();

        $contacts = $modelContacts->findAll();

        $user = $modelUsers->find(session()->get('id'));

        $imagePath = !empty($user['image']) ? site_url('uploads/users/' . $user['image']) : site_url('assets/img/avatar.jpg');
        
        $userId = session()->get('id');
        $packages = $this->packagesModel->getActivePackages();
        $activePackage = $this->userPackagesModel->getActivePackage($userId);
        $packageHistory = $this->userPackagesModel->getUserPackages($userId);

        $data = [
            'page' => [
                'title' => translate('pro packages')
            ],
            'validation' => \Config\Services::validation(),
            'contentPage' => view('packages/index', ['contacts' => $contacts, 'packages' => $packages, 'activePackage' => $activePackage, 'packageHistory' => $packageHistory, 'user' => $user, 'imagePath' => $imagePath]) 
        ];

        if ($this->request->isAJAX()) {
            return $this->response->setBody($data['contentPage']);
        } else {
            return view('layout/index', $data);
        }
    }
    
    /**
     * Display package details
     */
    public function view($id = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if ($id === null) {
            return redirect()->to('/packages');
        }

        $modelUsers = new UsersModel();
        $modelContacts = new ContactsModel();

        $contacts = $modelContacts->findAll();

        $user = $modelUsers->find(session()->get('id'));

        $imagePath = !empty($user['image']) ? site_url('uploads/users/' . $user['image']) : site_url('assets/img/avatar.jpg');
        
        $package = $this->packagesModel->getPackageById($id);
        
        if (!$package) {
            return redirect()->to('/packages')->with('error', 'Paquete no encontrado');
        }

        $data = [
            'page' => [
                'title' => translate('package details')
            ],
            'validation' => \Config\Services::validation(),
            'contentPage' => view('packages/view', ['contacts' => $contacts, 'package' => $package, 'user' => $user, 'imagePath' => $imagePath]) 
        ];

        if ($this->request->isAJAX()) {
            return $this->response->setBody($data['contentPage']);
        } else {
            return view('layout/index', $data);
        }
    }
    
    /**
     * Process package purchase
     */
    public function purchase($id = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if ($id === null) {
            return redirect()->to('/packages');
        }
        
        $package = $this->packagesModel->getPackageById($id);
        
        if (!$package) {
            return redirect()->to('/packages')->with('error', 'Paquete no encontrado');
        }
        
        $userId = session()->get('id');
        $user = $this->usersModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/packages')->with('error', 'Usuario no encontrado');
        }
        
        // Check if user has enough balance
        if ($user['wallet'] < $package['price']) {
            return redirect()->to('/packages')->with('error', 'Saldo insuficiente para adquirir este paquete');
        }
        
        // Create payment record
        $paymentData = [
            'user' => $userId,
            'amount' => $package['price'],
            'type' => 'package',
            'description' => 'Compra de paquete: ' . $package['name'],
            'status' => 1
        ];
        
        $paymentId = $this->paymentsModel->insert($paymentData);
        
        if (!$paymentId) {
            return redirect()->to('/packages')->with('error', 'Error al procesar el pago');
        }
        
        // Deduct from user's wallet
        $newBalance = $user['wallet'] - $package['price'];
        $this->usersModel->update($userId, ['wallet' => $newBalance]);
        
        // Subscribe user to package
        $subscribed = $this->userPackagesModel->subscribeUser($userId, $id, $paymentId);
        
        if (!$subscribed) {
            // Rollback payment
            $this->usersModel->update($userId, ['wallet' => $user['wallet']]);
            $this->paymentsModel->delete($paymentId);
            
            return redirect()->to('/packages')->with('error', 'Error al activar el paquete');
        }
        
        // Award free cartons as points
        if ($package['free_cartons'] > 0) {
            $this->pointsModel->addPoints(
                $userId,
                $package['free_cartons'] * 100, // Convert cartons to points
                'package',
                $id,
                "Cartones gratis por compra de paquete {$package['name']}"
            );
        }
        
        // Create notification
        $this->notificationTemplatesModel->createNotificationFromTemplate(
            'Oferta Especial',
            [
                'type' => 'package',
                'type_id' => $id,
                'offer_description' => "¡Has activado el paquete {$package['name']}! Disfruta de todos los beneficios Pro",
                'offer_end_date' => date('d/m/Y', strtotime("+{$package['duration_days']} days"))
            ],
            $userId
        );
        
        return redirect()->to('/packages')->with('success', "¡Has adquirido el paquete {$package['name']} exitosamente!");
    }
    
    /**
     * Display package benefits
     */
    public function benefits()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        $userId = session()->get('id');
        $activePackage = $this->userPackagesModel->getActivePackage($userId);
        
        $data = [
            'title' => 'Beneficios Pro',
            'activePackage' => $activePackage
        ];
        
        return view('packages/benefits', $data);
    }
    
    /**
     * Admin: Manage packages
     */
    public function manage()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        $packages = $this->packagesModel->findAll();
        
        $data = [
            'title' => 'Administrar Paquetes',
            'packages' => $packages
        ];
        
        return view('packages/manage', $data);
    }
    
    /**
     * Admin: Create package
     */
    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'description' => 'required',
                'price' => 'required|numeric',
                'duration_days' => 'required|integer',
                'discount_percentage' => 'required|numeric',
                'free_cartons' => 'required|integer',
                'daily_points' => 'required|integer'
            ];
            
            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'price' => $this->request->getPost('price'),
                    'duration_days' => $this->request->getPost('duration_days'),
                    'benefits' => $this->request->getPost('benefits'),
                    'discount_percentage' => $this->request->getPost('discount_percentage'),
                    'free_cartons' => $this->request->getPost('free_cartons'),
                    'daily_points' => $this->request->getPost('daily_points'),
                    'status' => 1
                ];
                
                $this->packagesModel->insert($data);
                
                return redirect()->to('/packages/manage')->with('success', 'Paquete creado exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $data = [
            'title' => 'Crear Paquete'
        ];
        
        return view('packages/create', $data);
    }
    
    /**
     * Admin: Edit package
     */
    public function edit($id = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/packages/manage');
        }
        
        $package = $this->packagesModel->find($id);
        
        if (!$package) {
            return redirect()->to('/packages/manage')->with('error', 'Paquete no encontrado');
        }
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'description' => 'required',
                'price' => 'required|numeric',
                'duration_days' => 'required|integer',
                'discount_percentage' => 'required|numeric',
                'free_cartons' => 'required|integer',
                'daily_points' => 'required|integer'
            ];
            
            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'price' => $this->request->getPost('price'),
                    'duration_days' => $this->request->getPost('duration_days'),
                    'benefits' => $this->request->getPost('benefits'),
                    'discount_percentage' => $this->request->getPost('discount_percentage'),
                    'free_cartons' => $this->request->getPost('free_cartons'),
                    'daily_points' => $this->request->getPost('daily_points'),
                    'status' => $this->request->getPost('status')
                ];
                
                $this->packagesModel->update($id, $data);
                
                return redirect()->to('/packages/manage')->with('success', 'Paquete actualizado exitosamente');
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
        
        $data = [
            'title' => 'Editar Paquete',
            'package' => $package
        ];
        
        return view('packages/edit', $data);
    }
    
    /**
     * Admin: Delete package
     */
    public function delete($id = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/packages/manage');
        }
        
        $package = $this->packagesModel->find($id);
        
        if (!$package) {
            return redirect()->to('/packages/manage')->with('error', 'Paquete no encontrado');
        }
        
        // Instead of deleting, set status to inactive
        $this->packagesModel->update($id, ['status' => 0]);
        
        return redirect()->to('/packages/manage')->with('success', 'Paquete eliminado exitosamente');
    }
    
    /**
     * Admin: View package subscribers
     */
    public function subscribers($id = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        if ($id === null) {
            return redirect()->to('/packages/manage');
        }
        
        $package = $this->packagesModel->find($id);
        
        if (!$package) {
            return redirect()->to('/packages/manage')->with('error', 'Paquete no encontrado');
        }
        
        $subscribers = $this->userPackagesModel->select('user_packages.*, users.username, users.firstname, users.lastname, users.email')
                                              ->join('users', 'users.id = user_packages.user_id')
                                              ->where('user_packages.package_id', $id)
                                              ->orderBy('user_packages.created_at', 'DESC')
                                              ->findAll();
        
        $data = [
            'title' => 'Suscriptores del Paquete',
            'package' => $package,
            'subscribers' => $subscribers
        ];
        
        return view('packages/subscribers', $data);
    }
    
    /**
     * Admin: Process package expiration
     */
    public function processExpiration()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        if (session()->get('group') != 1) {
            return redirect()->to('/dashboard');
        }
        
        $this->userPackagesModel->processExpiredSubscriptions();
        
        return redirect()->to('/packages/manage')->with('success', 'Procesamiento de expiración de paquetes completado');
    }
}