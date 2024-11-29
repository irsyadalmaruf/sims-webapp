<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];

    // Mengambil semua kategori
    public function getAllCategories()
    {
        return $this->findAll();
    }
}