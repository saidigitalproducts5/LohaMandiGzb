<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'seller_id', 'category_id', 'brand_id', 'name', 'slug', 'description',
        'specifications', 'weight', 'dimensions', 'grade', 'hsn_code', 'gst_rate',
        'moq', 'unit', 'delivery_time', 'price', 'offer_price', 'available_stock',
        'is_bulk', 'location', 'is_featured', 'is_sponsored', 'is_active', 'status',
        'view_count', 'inquiry_count',
    ];

    protected $beforeInsert = ['timestampSlug'];
    protected $beforeUpdate = ['timestampSlug'];

    protected function timestampSlug(array $data): array
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true)
                . '-' . substr(md5(uniqid('', true)), 0, 6);
        }

        if (isset($data['data']['specifications']) && is_array($data['data']['specifications'])) {
            $data['data']['specifications'] = json_encode($data['data']['specifications']);
        }

        return $data;
    }

    public function findForSeller(int $sellerId, int $productId): ?array
    {
        return $this->where('seller_id', $sellerId)->where('id', $productId)->first();
    }

    public function sellerProducts(int $sellerId, int $limit = 50, int $offset = 0): array
    {
        return $this->where('seller_id', $sellerId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Increment view/inquiry counters atomically.
     */
    public function bumpCounter(int $id, string $field): void
    {
        $this->builder()->set($field, $field . ' + 1', false)->where('id', $id)->update();
    }
}
