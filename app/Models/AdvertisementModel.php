<?php

namespace App\Models;

use CodeIgniter\Model;

class AdvertisementModel extends Model
{
    protected $table         = 'advertisements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'title', 'type', 'image', 'link', 'category_id',
        'product_id', 'start_date', 'end_date', 'clicks', 'status',
    ];

    public const TYPES = [
        'homepage_banner'   => 'Homepage Banner',
        'sponsored_product' => 'Sponsored Product',
        'featured_seller'   => 'Featured Seller',
        'category_banner'   => 'Category Banner',
        'popup'             => 'Popup / Offer',
    ];

    /**
     * Active ads for a slot, in date range, ordered newest first.
     */
    public function forSlot(string $type, int $limit = 3, ?int $categoryId = null): array
    {
        $builder = $this->where('type', $type)
            ->where('status', 1)
            ->groupStart()
                ->where('start_date IS NULL')
                ->orWhere('start_date <=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->groupStart()
                ->where('end_date IS NULL')
                ->orWhere('end_date >=', date('Y-m-d H:i:s'))
            ->groupEnd();

        if ($categoryId !== null) {
            $builder->groupStart()
                ->where('category_id IS NULL')
                ->orWhere('category_id', $categoryId)
                ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')->limit($limit)->findAll();
    }

    /**
     * Active sponsored-product ads joined to their product.
     */
    public function sponsoredProducts(int $limit = 4): array
    {
        $db = db_connect();
        return $db->table('advertisements a')
            ->select('a.*, p.name AS product_name, p.slug AS product_slug, p.price, p.offer_price, p.unit,
                      s.business_name, s.slug AS seller_slug, pi.file_path AS primary_image_path')
            ->join('products p', 'p.id = a.product_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('a.type', 'sponsored_product')
            ->where('a.status', 1)
            ->where('p.status', 'approved')
            ->where('p.is_active', 1)
            ->groupStart()
                ->where('a.start_date IS NULL')
                ->orWhere('a.start_date <=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->groupStart()
                ->where('a.end_date IS NULL')
                ->orWhere('a.end_date >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Active featured-seller ads joined to seller profile.
     */
    public function featuredSellers(int $limit = 4): array
    {
        $db = db_connect();
        return $db->table('advertisements a')
            ->select('a.*, s.business_name, s.slug AS seller_slug, s.city, s.rating_avg,
                      s.rating_count, s.gst_verified, s.is_verified, s.logo')
            ->join('sellers s', 's.id = a.seller_id', 'left')
            ->where('a.type', 'featured_seller')
            ->where('a.status', 1)
            ->where('s.status', 'active')
            ->groupStart()
                ->where('a.start_date IS NULL')
                ->orWhere('a.start_date <=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->groupStart()
                ->where('a.end_date IS NULL')
                ->orWhere('a.end_date >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function incrementClicks(int $id): void
    {
        $this->builder()->set('clicks', 'clicks + 1', false)->where('id', $id)->update();
    }
}