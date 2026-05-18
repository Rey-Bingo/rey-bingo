<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller {
    // Cargar helper de sesión y URL
    public function __construct() {
        helper(['form', 'url']);
        session();
    }

    // Mostrar formulario de inicio de sesión
    public function login() {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        
        // Asegúrate de pasar la instancia del validador a la vista
        return view('auth/login', ['validation' => \Config\Services::validation()]);
    }

    // Procesar inicio de sesión
    public function loginSubmit() {
        $model = new UserModel();
        
        // Reglas de validación
        $validationRules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]'
        ];
    
        // Validar datos del formulario
        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $response = [
                'success' => false,
                'error' => 'Por favor, corrige los errores a continuación.',
                'errors' => $errors
            ];
            return $this->response->setJSON($response);
        }
    
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
    
        // Buscar usuario en la base de datos
        $user = $model->getUserByUsername($username);
    
        if (!$user) {
            // Usuario no registrado
            $response = [
                'success' => false,
                'error' => 'Usuario no registrado.'
            ];
            return $this->response->setJSON($response);
        }
    
        // Verificar si el usuario ha sido eliminado
        if ($user['deleted'] == 1) {
            // Usuario eliminado
            $response = [
                'success' => false,
                'error' => 'Tu cuenta ha sido eliminada.'
            ];
            return $this->response->setJSON($response);
        }
    
        // Verificar la contraseña
        if (!password_verify($password, $user['password'])) {
            // Contraseña incorrecta
            $response = [
                'success' => false,
                'error' => 'Contraseña incorrecta.'
            ];
            return $this->response->setJSON($response);
        }
    
        // Aquí ya sabemos que el usuario y la contraseña son correctos
        // Ahora verificamos si el usuario está inactivo
        if ($user['status'] == 0) {
            // Usuario inactivo
            $response = [
                'success' => false,
                'error' => 'Tu cuenta está inactiva. Por favor, contacta con el administrador.'
            ];
            return $this->response->setJSON($response);
        }
    
        // Configurar sesión del usuario
        $sessionData = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'logged_in' => true
        ];
        session()->set($sessionData);
    
        // Respuesta exitosa
        $response = [
            'success' => true,
            'redirect' => site_url('/dashboard')
        ];
        return $this->response->setJSON($response);
    }

    // Mostrar formulario de registro
    public function register() {
        // Verificar si el usuario ya está logueado
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard'); // Si está logueado, redirigir al dashboard
        }
    
        return view('auth/register'); // Si no está logueado, mostrar el formulario de login
    }

    // Procesar registro de nuevo usuario
    public function registerSubmit() {
        $model = new UserModel();
    
        // Reglas de validación
        $validationRules = [
            'name' => 'required|min_length[3]',
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone' => 'required|numeric',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'matches[password]',
        ];
    
        // Validar datos del formulario
        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $response = [
                'success' => false,
                'error' => 'Por favor, corrige los errores a continuación.',
                'errors' => $errors
            ];
            return $this->response->setJSON($response);
        }
    
        // Guardar el nuevo usuario
        $data = [
            'name' => $this->request->getVar('name'),
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT), // Encriptar la contraseña
            'created_at' => time(),
            'updated_at' => time(),
            'status' => 1,
            'deleted' => 0,
        ];
    
        $model->insert($data);
    
        $response = [
            'success' => true,
            'redirect' => site_url('/login')
        ];
        return $this->response->setJSON($response);
    }
    
    // Cerrar sesión
    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}