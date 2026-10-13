<?php

namespace App\Models;

use CodeIgniter\Model;

class CrmMeetingModel extends Model
{
    protected $table         = 'crm_meetings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'customer_id', 'deal_id', 'title', 'starts_at', 'ends_at', 'location', 'status', 'notes',
    ];

    public const STATUSES = ['scheduled', 'completed', 'cancelled'];

    public function forSeller(int $sellerId, ?string $status = null): array
    {
        $b = $this->select('crm_meetings.*, c.name AS customer_name, c.company AS customer_company, d.title AS deal_title')
            ->join('crm_customers c', 'c.id = crm_meetings.customer_id', 'left')
            ->join('crm_deals d', 'd.id = crm_meetings.deal_id', 'left')
            ->where('crm_meetings.seller_id', $sellerId);
        if ($status !== null && $status !== 'all' && $status !== '') {
            $b->where('crm_meetings.status', $status);
        }

        return $b->orderBy('crm_meetings.starts_at', 'ASC')->findAll();
    }

    public function upcomingCount(int $sellerId): int
    {
        return (int) $this->where('seller_id', $sellerId)->where('status', 'scheduled')
            ->where('starts_at >=', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $id)->first();
    }
}