<?php

namespace App\Models;

use CodeIgniter\Model;

class CrmTaskModel extends Model
{
    protected $table         = 'crm_tasks';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'customer_id', 'deal_id', 'title', 'category', 'due_at', 'priority', 'status',
    ];

    public const CATEGORIES = ['task', 'follow_up', 'call', 'quotation', 'delivery'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    public function forSeller(int $sellerId, ?string $status = null): array
    {
        $b = $this->select('crm_tasks.*, c.name AS customer_name, c.company AS customer_company, d.title AS deal_title')
            ->join('crm_customers c', 'c.id = crm_tasks.customer_id', 'left')
            ->join('crm_deals d', 'd.id = crm_tasks.deal_id', 'left')
            ->where('crm_tasks.seller_id', $sellerId);
        if ($status !== null && $status !== 'all' && $status !== '') {
            $b->where('crm_tasks.status', $status);
        }

        return $b->orderBy('crm_tasks.due_at', 'ASC')
            ->orderBy('FIELD(crm_tasks.priority, "high","medium","low")', 'ASC', false)
            ->findAll();
    }

    public function openCount(int $sellerId): int
    {
        return (int) $this->where('seller_id', $sellerId)->where('status', 'open')->countAllResults();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $id)->first();
    }
}