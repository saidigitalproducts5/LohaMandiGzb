<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'order_id', 'user_id', 'amount', 'method', 'txn_id', 'status', 'gateway_response',
    ];

    /**
     * Latest payment attempt (pending or paid) for an order.
     */
    public function forOrder(int $orderId): ?array
    {
        return $this->where('order_id', $orderId)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * All payments for an order.
     */
    public function allForOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Payments made by a user.
     */
    public function forUser(int $userId): array
    {
        return $this->select('payments.*, o.order_number, s.business_name')
            ->join('orders o', 'o.id = payments.order_id', 'left')
            ->join('sellers s', 's.id = o.seller_id', 'left')
            ->where('payments.user_id', $userId)
            ->orderBy('payments.id', 'DESC')
            ->findAll();
    }
}