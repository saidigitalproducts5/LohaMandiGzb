<?php

namespace App\Models;

use CodeIgniter\Model;

class CrmDealModel extends Model
{
    protected $table         = 'crm_deals';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'customer_id', 'title', 'value', 'stage', 'expected_close', 'notes',
    ];

    public const STAGES = [
        'prospecting' => 'Prospecting',
        'qualified'   => 'Qualified',
        'quotation'   => 'Quotation',
        'negotiation' => 'Negotiation',
        'won'         => 'Won',
        'lost'        => 'Lost',
    ];

    public function byStage(int $sellerId): array
    {
        $db = db_connect();
        $rows = $db->table('crm_deals d')
            ->select('d.*, c.name AS customer_name, c.company AS customer_company')
            ->join('crm_customers c', 'c.id = d.customer_id', 'left')
            ->where('d.seller_id', $sellerId)
            ->orderBy('d.expected_close', 'ASC')
            ->get()->getResultArray();

        $board = [];
        foreach (array_keys(self::STAGES) as $s) {
            $board[$s] = [];
        }
        foreach ($rows as $r) {
            $board[$r['stage']][] = $r;
        }

        return $board;
    }

    public function forSeller(int $sellerId, ?string $status = null): array
    {
        $b = $this->select('crm_deals.*, c.name AS customer_name, c.company AS customer_company')
            ->join('crm_customers c', 'c.id = crm_deals.customer_id', 'left')
            ->where('crm_deals.seller_id', $sellerId);
        if ($status !== null && $status !== '') {
            $b->where('crm_deals.stage', $status);
        }

        return $b->orderBy('crm_deals.created_at', 'DESC')->findAll();
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->select('crm_deals.*, c.name AS customer_name, c.company AS customer_company')
            ->join('crm_customers c', 'c.id = crm_deals.customer_id', 'left')
            ->where('crm_deals.seller_id', $sellerId)
            ->where('crm_deals.id', $id)
            ->first();
    }
}