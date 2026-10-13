<?php

namespace App\Models;

use CodeIgniter\Model;

class RfqModel extends Model
{
    protected $table         = 'rfqs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'buyer_id', 'product_name', 'category_id', 'quantity', 'unit', 'specification',
        'required_date', 'delivery_location', 'city', 'state', 'budget_min', 'budget_max',
        'document_path', 'photo_path', 'status', 'source',
    ];

    public function buyerRfqs(int $buyerId): array
    {
        return $this->where('buyer_id', $buyerId)->orderBy('created_at', 'DESC')->findAll();
    }

    public function findForBuyer(int $buyerId, int $rfqId): ?array
    {
        return $this->where('buyer_id', $buyerId)->where('id', $rfqId)->first();
    }
}