<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends Controller {
    public function __construct() {
        helper(['url', 'form']);
        session();
    }

    // Método para mostrar el Dashboard
    public function index() {
        // Verificar si el usuario está logueado
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Pasar los datos del usuario a la vista del Dashboard
        $data = [
            'name' => session()->get('name'),
            'username' => session()->get('username')
        ];

        return view('dashboard', $data);
    }
}