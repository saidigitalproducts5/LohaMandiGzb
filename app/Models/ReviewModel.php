<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table         = 'reviews';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'buyer_id', 'order_id', 'rating', 'review', 'photo_path',
        'seller_reply', 'status',
    ];

    /**
     * Has this buyer already reviewed the seller in a given order?
     */
    public function hasReviewed(int $buyerId, int $orderId): bool
    {
        return $this->where('buyer_id', $buyerId)->where('order_id', $orderId)->countAllResults() > 0;
    }

    /**
     * Has this user already left any review for this seller?
     */
    public function hasReviewedSeller(int $sellerId, int $userId): bool
    {
        return $this->where('seller_id', $sellerId)
            ->where('buyer_id', $userId)
            ->countAllResults() > 0;
    }

    public function forSeller(int $sellerId, int $limit = 100): array
    {
        return $this->select('reviews.*, u.name AS buyer_name')
            ->join('users u', 'u.id = reviews.buyer_id', 'left')
            ->where('reviews.seller_id', $sellerId)
            ->whereIn('reviews.status', ['approved', 'pending'])
            ->orderBy('reviews.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function allForAdmin(?string $status = null, int $limit = 200): array
    {
        $q = $this->select('reviews.*, s.business_name, u.name AS buyer_name')
            ->join('sellers s', 's.id = reviews.seller_id', 'left')
            ->join('users u', 'u.id = reviews.buyer_id', 'left');
        if ($status !== null && $status !== '') {
            $q->where('reviews.status', $status);
        }

        return $q->orderBy('reviews.id', 'DESC')->limit($limit)->findAll();
    }

    /**
     * Recompute seller rating aggregates after review changes.
     */
    public function recomputeSellerRating(int $sellerId): void
    {
        $row = db_connect()->table('reviews')
            ->select('AVG(rating) AS avg_r, COUNT(*) AS cnt')
            ->where('seller_id', $sellerId)
            ->where('status', 'approved')
            ->get()->getRow();

        db_connect()->table('sellers')->where('id', $sellerId)->update([
            'rating_avg'   => number_format((float) ($row->avg_r ?? 0), 1, '.', ''),
            'rating_count' => (int) ($row->cnt ?? 0),
        ]);
    }
}