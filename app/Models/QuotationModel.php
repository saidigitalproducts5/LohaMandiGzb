<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationModel extends Model
{
    protected $table         = 'quotations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'rfq_id', 'seller_id', 'buyer_id', 'quote_number', 'product_name', 'quantity',
        'unit', 'unit_price', 'base_amount', 'gst_rate', 'gst_amount', 'discount',
        'transport_charge', 'other_charges', 'total_amount', 'payment_terms',
        'valid_until', 'notes', 'status',
    ];

    public function nextQuoteNumber(): string
    {
        $row = db_connect()->query('SELECT COUNT(*) AS c FROM quotations')->getRow();

        return 'QT-' . date('ymd') . '-' . str_pad(((int) $row->c) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function sellerQuotations(int $sellerId): array
    {
        return $this->where('seller_id', $sellerId)->orderBy('created_at', 'DESC')->findAll();
    }
}
