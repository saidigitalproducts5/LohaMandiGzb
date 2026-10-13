<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'order_number', 'buyer_id', 'seller_id', 'quote_id', 'subtotal', 'gst_amount',
        'discount', 'shipping', 'total', 'payment_method', 'payment_status',
        'order_status', 'lr_number', 'transport_details', 'shipping_address',
    ];

    public function nextOrderNumber(): string
    {
        $row = db_connect()->query('SELECT COUNT(*) AS c FROM orders')->getRow();

        return 'ORD-' . date('ymd') . '-' . str_pad(((int) $row->c) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function buyerOrders(int $buyerId): array
    {
        return $this->select('orders.*, s.business_name')
            ->join('sellers s', 's.id = orders.seller_id', 'left')
            ->where('orders.buyer_id', $buyerId)
            ->orderBy('orders.created_at', 'DESC')
            ->findAll();
    }

    public function sellerOrders(int $sellerId): array
    {
        return $this->select('orders.*, u.name AS buyer_name, u.mobile AS buyer_mobile')
            ->join('users u', 'u.id = orders.buyer_id', 'left')
            ->where('orders.seller_id', $sellerId)
            ->orderBy('orders.created_at', 'DESC')
            ->findAll();
    }
}
