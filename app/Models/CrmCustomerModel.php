<?php

namespace App\Models;

use CodeIgniter\Model;

class CrmCustomerModel extends Model
{
    protected $table         = 'crm_customers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'name', 'company', 'email', 'mobile', 'address', 'city', 'source', 'notes',
    ];

    public const SOURCES = ['manual', 'lead', 'rfq', 'order', 'walkin', 'referral'];

    public function forSeller(int $sellerId, ?string $q = null): array
    {
        $b = $this->where('seller_id', $sellerId);
        if ($q !== null && $q !== '') {
            $b->groupStart()
                ->like('name', $q)->orLike('company', $q)->orLike('email', $q)->orLike('mobile', $q)
                ->groupEnd();
        }

        return $b->orderBy('created_at', 'DESC')->findAll(200);
    }

    public function findForSeller(int $sellerId, int $id): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $id)->first();
    }

/**
     * Customer record combined with count totals (deals + open tasks).
     */
    public function summary(int $sellerId, ?string $q = null): array
    {
        $builder = db_connect()->table('crm_customers c')
            ->select('c.*, (SELECT COUNT(*) FROM crm_deals d WHERE d.customer_id = c.id) AS deal_count,
                      (SELECT COUNT(*) FROM crm_tasks t WHERE t.customer_id = c.id AND t.status = "open") AS open_tasks')
            ->where('c.seller_id', $sellerId);

        if ($q !== null && $q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)->orLike('c.company', $q)->orLike('c.email', $q)->orLike('c.mobile', $q)
                ->groupEnd();
        }

        return $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();
    }
}