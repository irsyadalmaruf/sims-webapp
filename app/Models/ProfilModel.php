<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfilModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'password', 'name', 'position', 'profile_pic'];

    public function getUserById($id)
    {
        return $this->where('id', $id)->first();
    }
}