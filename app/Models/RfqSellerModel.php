<?php

namespace App\Models;

use CodeIgniter\Model;

class RfqSellerModel extends Model
{
    protected $table         = 'rfq_sellers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['rfq_id', 'seller_id', 'matched_by', 'score', 'status', 'viewed_at'];

    public function sellersForRfq(int $rfqId): array
    {
        return $this->select('rfq_sellers.*, s.business_name, s.slug, s.gst_verified, s.is_verified, s.is_premium, s.rating_avg, s.rating_count, s.city, s.state')
            ->join('sellers s', 's.id = rfq_sellers.seller_id')
            ->where('rfq_sellers.rfq_id', $rfqId)
            ->orderBy('rfq_sellers.score', 'DESC')
            ->findAll();
    }

    public function rfqsForSeller(int $sellerId): array
    {
        return $this->select('rfq_sellers.*, r.id AS rfq_id, r.status AS rfq_status, r.product_name, r.quantity, r.unit, r.specification, r.city, r.state, r.required_date, r.created_at AS rfq_created_at, u.name AS buyer_name, u.mobile AS buyer_mobile')
            ->join('rfqs r', 'r.id = rfq_sellers.rfq_id')
            ->join('users u', 'u.id = r.buyer_id')
            ->where('rfq_sellers.seller_id', $sellerId)
            ->orderBy('r.created_at', 'DESC')
            ->findAll();
    }

    public function matchingSellerIds(int $rfqId): array
    {
        return array_column($this->where('rfq_id', $rfqId)->findAll(), 'seller_id');
    }
}
