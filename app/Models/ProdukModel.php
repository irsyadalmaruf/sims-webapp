<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'category_id', 'price_buy', 'price_sell', 'stock', 'image'];

    public function getProduk($limit = 10, $page = 1)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->orderBy('products.id', 'DESC')
                    ->paginate($limit, 'produk', $page);
    }

    public function getFilteredProdukCount($search = '', $category = 'semua')
    {
        $builder = $this->db->table($this->table)
                            ->select('products.*, categories.name as category_name')
                            ->join('categories', 'categories.id = products.category_id');
    
        if ($category !== 'semua') {
            $builder->where('products.category_id', $category);
        }
    
        if (!empty($search)) {
            $builder->like('LOWER(products.name)', strtolower($search));
        }
    
        return $builder->countAllResults(); 
    }

    public function getFilteredProduk($search = '', $category = 'semua', $limit = 10, $page = 1)
    {
        $builder = $this->db->table($this->table)
                            ->select('products.*, categories.name as category_name')
                            ->join('categories', 'categories.id = products.category_id');

        if ($category !== 'semua') {
            $builder->where('products.category_id', $category);
        }

        if (!empty($search)) {
            $builder->like('LOWER(products.name)', strtolower($search));
        }

        $builder->limit($limit, ($page - 1) * $limit);

        return $builder->get()->getResultArray();
    }  
         
}