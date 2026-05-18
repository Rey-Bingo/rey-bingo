<?php

namespace App\Controllers;

use App\Models\GamesModel;
use App\Models\CartonsModel;
use CodeIgniter\Controller;

class Dashboard extends Controller {
    public function __construct() {
        helper(['form', 'url', 'cookie', 'text']);
        session();
    }

    // Método para mostrar el Dashboard
    public function index() {
        $modelGames = new GamesModel();
        $modelCartons = new CartonsModel();
        
        // Verificar si el usuario está logueado
        if (!session()->get('logged_in')) {
            return redirect()->to('/signin');
        }
        
        // Obtener el juego activo para la fecha de hoy
        $game = $modelGames->getGameByDate(date('Y-m-d'));
    
        if ($game) {
            // Obtener los cartones del usuario para el juego activo
            $cartons = $modelCartons->getCartonsByUser(session()->get('id'), $game['id']);
            
            // Si hay cartones para el usuario en el juego activo, redirigir al módulo de juegos
            if (!empty($cartons)) {
                return redirect()->to('/games');
            }
        }

        // Pasar los datos del usuario a la vista del Dashboard
        $data = [
            'page' => [
                'title' => 'Inicio'
            ],
            'validation' => \Config\Services::validation(),
            'contentPage' => view('dashboard/index') // Cargar el contenido de inicio de sesión
        ];

        // Manejar solicitud AJAX
        if ($this->request->isAJAX()) {
            // Enviar solo el contenido de la página si es una solicitud AJAX
            return $this->response->setBody($data['contentPage']);
        } else {
            // Si no es AJAX, renderizar la vista principal con el layout completo
            return view('layout/index', $data);
        }
    }
}