<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipPlanModel extends Model
{
    protected $table         = 'membership_plans';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'name', 'code', 'price', 'duration_days', 'features', 'status',
    ];

    public function activePlans(): array
    {
        return $this->where('status', 1)->orderBy('price', 'ASC')->findAll();
    }

    public function findByCode(string $code): ?array
    {
        return $this->where('code', $code)->where('status', 1)->first();
    }

    /**
     * Ensure the yearly seller plan exists (idempotent).
     */
    public function ensureSellerPlan(): array
    {
        $plan = $this->findByCode('seller_yearly');
        if ($plan !== null) {
            return $plan;
        }

        $id = (int) $this->insert([
            'name'          => 'Seller — Yearly',
            'code'          => 'seller_yearly',
            'price'         => 9999.00,
            'duration_days' => 365,
            'features'      => json_encode([
                'business_listing' => 1,
                'products_limit'   => 200,
                'leads_limit'      => 2000,
                'verified_badge'   => 1,
            ]),
            'status' => 1,
        ]);

        return $this->find((int) $id);
    }
}