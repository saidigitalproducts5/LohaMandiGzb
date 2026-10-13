<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table         = 'product_images';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['product_id', 'file_path', 'is_primary', 'sort_order'];

    public function forProduct(int $productId): array
    {
        return $this->where('product_id', $productId)->orderBy('is_primary', 'DESC')->orderBy('sort_order', 'ASC')->findAll();
    }

    public function deleteForProduct(int $productId): void
    {
        $this->where('product_id', $productId)->delete();
    }
}
