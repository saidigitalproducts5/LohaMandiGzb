<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table         = 'brands';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $allowedFields = ['name', 'slug', 'logo', 'status'];

    public function activeBrands(): array
    {
        return $this->where('status', 1)->orderBy('name', 'ASC')->findAll();
    }
}
