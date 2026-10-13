<?php

namespace App\Services;

use App\Models\BulkPriceModel;
use App\Models\ProductImageModel;
use App\Models\SellerModel;

/**
 * ProductService — assembles enriched product payloads (images, bulk
 * pricing, seller summary).
 */
class ProductService
{
    private ProductImageModel $images;
    private BulkPriceModel $bulk;
    private SellerModel $sellers;

    public function __construct()
    {
        $this->images  = new ProductImageModel();
        $this->bulk    = new BulkPriceModel();
        $this->sellers = new SellerModel();
    }

    public function enrich(array $product): array
    {
        $product['images']     = $this->images->forProduct($product['id']);
        $product['bulk_prices'] = $this->bulk->forProduct($product['id']);
        $product['primary_image'] = null;

        foreach ($product['images'] as $img) {
            if ((int) $img['is_primary'] === 1) {
                $product['primary_image'] = $img['file_path'];
                break;
            }
        }
        if ($product['primary_image'] === null && isset($product['images'][0])) {
            $product['primary_image'] = $product['images'][0]['file_path'];
        }

        $product['seller'] = $this->sellers->find($product['seller_id']);

        return $product;
    }

    public function enrichMany(array $products): array
    {
        foreach ($products as &$p) {
            $p = $this->enrich($p);
        }

        return $products;
    }
}