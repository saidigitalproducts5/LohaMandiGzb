<?php

namespace App\Models;

use CodeIgniter\Model;

class BulkPriceModel extends Model
{
    protected $table         = 'bulk_prices';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['product_id', 'min_qty', 'max_qty', 'unit_price'];

    public function forProduct(int $productId): array
    {
        return $this->where('product_id', $productId)->orderBy('min_qty', 'ASC')->findAll();
    }

    public function deleteForProduct(int $productId): void
    {
        $this->where('product_id', $productId)->delete();
    }
}
