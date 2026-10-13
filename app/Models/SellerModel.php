<?php

namespace App\Models;

use CodeIgniter\Model;

class SellerModel extends Model
{
    protected $table         = 'sellers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'user_id', 'business_name', 'slug', 'gstin', 'owner_name', 'owner_phone',
        'business_category_id', 'address_line1', 'address_line2', 'city', 'state',
        'pincode', 'latitude', 'longitude', 'business_hours', 'years_in_business',
        'employees_count', 'logo', 'banner', 'about', 'brands', 'infrastructure',
        'website', 'gst_verified', 'is_verified', 'is_premium', 'rating_avg',
        'rating_count', 'status',
    ];

    public function findByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Active sellers for chat/pickup selection (optionally excluding one seller).
     */
    public function activeSellersForPickup(int $excludeSellerId = 0): array
    {
        $q = $this->select('sellers.id, sellers.business_name, sellers.city')
            ->where('status', 'active')
            ->orderBy('business_name', 'ASC');
        if ($excludeSellerId > 0) {
            $q->where('sellers.id !=', $excludeSellerId);
        }

        return $q->findAll();
    }
}
