<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Bingo extends Controller
{
    // Almacena los números generados y las tarjetas de jugadores
    private $generatedNumbers = [];

    public function index()
    {
        return view('bingo');
    }

    // Método para generar números aleatorios del bingo
    public function generateNumber()
    {
        if (count($this->generatedNumbers) >= 75) {
            return $this->response->setJSON(['error' => 'All numbers have been drawn']);
        }

        do {
            $number = rand(1, 75);
        } while (in_array($number, $this->generatedNumbers));

        $this->generatedNumbers[] = $number;

        return $this->response->setJSON(['number' => $number]);
    }

    // Método para verificar si un jugador ha ganado
    public function checkWin($card)
    {
        // Lógica de verificación de tarjeta ganadora (lo harás más adelante)
    }
}
