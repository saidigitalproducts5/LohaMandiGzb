<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table         = 'warehouses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'name', 'address', 'city', 'state', 'pincode',
        'contact_phone', 'is_default',
    ];

    public function forSeller(int $sellerId): array
    {
        return $this->where('seller_id', $sellerId)->orderBy('id', 'ASC')->findAll();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $id)->first();
    }

    public function firstWarehouseFor(int $sellerId): ?array
    {
        return $this->where('seller_id', $sellerId)->orderBy('is_default', 'DESC')->orderBy('id', 'ASC')->first();
    }
}