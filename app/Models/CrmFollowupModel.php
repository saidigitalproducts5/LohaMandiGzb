<?php

namespace App\Models;

use CodeIgniter\Model;

class CrmFollowupModel extends Model
{
    protected $table         = 'crm_followups';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'customer_id', 'deal_id', 'channel', 'scheduled_at', 'note', 'status', 'done_at',
    ];

    public const CHANNELS = ['call', 'email', 'whatsapp', 'visit'];
    public const STATUSES = ['pending', 'done'];

    public function forSeller(int $sellerId, ?string $status = null): array
    {
        $b = $this->select('crm_followups.*, c.name AS customer_name, c.company AS customer_company, d.title AS deal_title')
            ->join('crm_customers c', 'c.id = crm_followups.customer_id', 'left')
            ->join('crm_deals d', 'd.id = crm_followups.deal_id', 'left')
            ->where('crm_followups.seller_id', $sellerId);
        if ($status !== null && $status !== 'all' && $status !== '') {
            $b->where('crm_followups.status', $status);
        }

        return $b->orderBy('crm_followups.scheduled_at', 'ASC')->findAll();
    }

    public function pendingCount(int $sellerId): int
    {
        return (int) $this->where('seller_id', $sellerId)->where('status', 'pending')
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $id)->first();
    }
}