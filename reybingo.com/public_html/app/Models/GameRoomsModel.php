<?php

namespace App\Models;

use CodeIgniter\Model;

class GameRoomsModel extends Model {
    protected $table = 'game_rooms';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at', 'status'];

    protected $useTimestamps = true;
}