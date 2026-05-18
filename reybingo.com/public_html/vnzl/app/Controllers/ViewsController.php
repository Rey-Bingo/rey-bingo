<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class ViewsController extends Controller
{
    public function admin(string $gameId)
    {
        return view('admin', ['gameId' => $gameId]);
    }

    public function play(string $gameId)
    {
        return view('play', ['gameId' => $gameId]);
    }
}
