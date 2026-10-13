<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table         = 'leads';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'buyer_id', 'product_id', 'rfq_id', 'name', 'mobile', 'email',
        'company', 'message', 'lead_type', 'status', 'notes', 'call_history', 'reminder_at',
    ];

    public function sellerLeads(int $sellerId, ?string $status = null): array
    {
        $builder = $this->where('seller_id', $sellerId);

        if ($status && $status !== 'all') {
            $builder->where('status', $status);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function findForSeller(int $sellerId, int $leadId): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $leadId)->first();
    }
}
