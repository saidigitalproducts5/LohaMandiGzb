<?php

namespace App\Models;

use CodeIgniter\Model;

class SellerMembershipModel extends Model
{
    protected $table         = 'seller_membership';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'plan_id', 'start_date', 'end_date', 'amount', 'status',
    ];

    public function activeForSeller(int $sellerId): ?array
    {
        return $this->where('seller_id', $sellerId)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Latest membership for a seller, joined with its plan.
     */
    public function latestWithPlan(int $sellerId): ?array
    {
        return $this->select('sm.*, p.name AS plan_name, p.code AS plan_code, p.price AS plan_price')
            ->from('seller_membership sm')
            ->join('membership_plans p', 'p.id = sm.plan_id', 'left')
            ->where('sm.seller_id', $sellerId)
            ->orderBy('sm.id', 'DESC')
            ->first();
    }

    /**
     * Whether the seller currently holds a paid, unexpired subscription.
     */
    public function isSubscribed(int $sellerId): bool
    {
        $m = $this->activeForSeller($sellerId);
        if ($m === null) {
            return false;
        }

        return $m['end_date'] >= date('Y-m-d');
    }

    public function grantYearly(int $sellerId, int $planId, float $amount): int
    {
        $today = new \DateTimeImmutable();

        return (int) $this->insert([
            'seller_id'  => $sellerId,
            'plan_id'    => $planId,
            'start_date' => $today->format('Y-m-d'),
            'end_date'   => $today->modify('+365 days')->format('Y-m-d'),
            'amount'     => $amount,
            'status'     => 'active',
        ]);
    }
}