<?php

namespace App\Models;

use CodeIgniter\Model;

class CrmNoteModel extends Model
{
    protected $table         = 'crm_notes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'customer_id', 'deal_id', 'body',
    ];

    public function forSeller(int $sellerId, ?int $customerId = null, ?int $dealId = null): array
    {
        $b = $this->where('seller_id', $sellerId);
        if ($customerId !== null) {
            $b->where('customer_id', $customerId);
        }
        if ($dealId !== null) {
            $b->where('deal_id', $dealId);
        }

        return $b->orderBy('created_at', 'DESC')->findAll();
    }
}