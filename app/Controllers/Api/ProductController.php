<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\BulkPriceModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\SellerModel;
use App\Services\ProductService;

class ProductController extends ApiController
{
    private ProductModel $products;
    private ProductService $productService;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->productService = new ProductService();
    }

    /**
     * Public product listing (approved + active).
     * GET /api/products?category_id=&brand_id=&seller_id=&price_min=&price_max=&q=&limit=&offset=
     */
    public function index()
    {
        $builder = $this->products->where('status', 'approved')->where('is_active', 1);

        $categoryId = (int) ($this->request->getGet('category_id') ?? 0);
        $brandId    = (int) ($this->request->getGet('brand_id') ?? 0);
        $sellerId   = (int) ($this->request->getGet('seller_id') ?? 0);
        $q          = trim((string) $this->request->getGet('q'));
        $priceMin   = (float) ($this->request->getGet('price_min') ?? 0);
        $priceMax   = (float) ($this->request->getGet('price_max') ?? 0);

        if ($categoryId > 0) {
            $builder->where('category_id', $categoryId);
        }
        if ($brandId > 0) {
            $builder->where('brand_id', $brandId);
        }
        if ($sellerId > 0) {
            $builder->where('seller_id', $sellerId);
        }
        if ($q !== '') {
            $builder->groupStart()->like('name', $q)->orLike('description', $q)->groupEnd();
        }
        if ($priceMin > 0) {
            $builder->where('price >=', $priceMin);
        }
        if ($priceMax > 0) {
            $builder->where('price <=', $priceMax);
        }

        $limit  = min((int) ($this->request->getGet('limit') ?? 20), 100);
        $offset = max((int) ($this->request->getGet('offset') ?? 0), 0);

        $total = (clone $builder)->countAllResults(false);
        $rows  = $builder->orderBy('is_featured', 'DESC')->orderBy('created_at', 'DESC')->limit($limit, $offset)->find();

        return $this->respondOK([
            'total'   => $total,
            'limit'   => $limit,
            'offset'  => $offset,
            'items'   => $this->productService->enrichMany($rows),
        ]);
    }

    public function show($id = null)
    {
        $product = $this->products->where('status', 'approved')->where('is_active', 1)->find($id);
        if ($product === null) {
            return $this->respondNotFound('Product not found.');
        }

        $this->products->bumpCounter((int) $id, 'view_count');

        $payload = $this->productService->enrich($product);
        $payload['recommendations'] = (new \App\Services\AiService())->recommendFor((int) $id, 4);

        return $this->respondOK($payload);
    }

    /**
     * Seller: create product. Auth: seller role.
     */
    public function create()
    {
        $seller = $this->requireSeller();
        if ($seller instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $seller;
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'        => 'required|max_length[190]',
            'category_id' => 'required|integer|is_not_unique[categories.id]',
            'price'       => 'permit_empty|decimal',
            'offer_price' => 'permit_empty|decimal',
            'gst_rate'    => 'permit_empty|decimal',
            'moq'         => 'permit_empty|decimal',
            'available_stock' => 'permit_empty|decimal',
            'hsn_code'    => 'permit_empty|max_length[16]',
        ]);
        if (!$validation->run($data)) {
            return $this->respondValidationErrors($validation->getErrors());
        }

        $productId = (int) $this->products->insert([
            'seller_id'       => (int) $seller['id'],
            'category_id'     => (int) $data['category_id'],
            'brand_id'        => !empty($data['brand_id']) ? (int) $data['brand_id'] : null,
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'specifications'  => $data['specifications'] ?? [],
            'weight'          => $data['weight'] ?? null,
            'dimensions'      => $data['dimensions'] ?? null,
            'grade'           => $data['grade'] ?? null,
            'hsn_code'        => $data['hsn_code'] ?? null,
            'gst_rate'        => $data['gst_rate'] ?? 18.00,
            'moq'             => $data['moq'] ?? 0,
            'unit'            => $data['unit'] ?? 'ton',
            'delivery_time'   => $data['delivery_time'] ?? null,
            'price'           => $data['price'] ?? null,
            'offer_price'     => $data['offer_price'] ?? null,
            'available_stock' => $data['available_stock'] ?? 0,
            'is_bulk'         => !empty($data['bulk_prices']) ? 1 : 0,
            'location'        => $data['location'] ?? null,
            'is_featured'     => 0,
            'is_sponsored'    => 0,
            'is_active'       => 1,
            'status'          => 'pending',
        ]);

        // Images
        $this->saveImages($productId, $data['images'] ?? []);

        // Bulk pricing slabs
        $this->saveBulkPrices($productId, $data['bulk_prices'] ?? []);

        return $this->respondCreated(['product_id' => $productId], 'Product submitted for review.');
    }

    /**
     * Seller: update own product.
     */
    public function update($id = null)
    {
        $seller = $this->requireSeller();
        if ($seller instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $seller;
        }

        $product = $this->products->findForSeller((int) $seller['id'], (int) $id);
        if ($product === null) {
            return $this->respondNotFound('Product not found.');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $patch = [];
        foreach (['name', 'description', 'weight', 'dimensions', 'grade', 'hsn_code', 'delivery_time', 'location'] as $f) {
            if (array_key_exists($f, $data)) {
                $patch[$f] = $data[$f];
            }
        }
        foreach (['category_id', 'brand_id'] as $f) {
            if (array_key_exists($f, $data)) {
                $patch[$f] = !empty($data[$f]) ? (int) $data[$f] : null;
            }
        }
        foreach (['price', 'offer_price', 'gst_rate', 'moq', 'available_stock'] as $f) {
            if (array_key_exists($f, $data)) {
                $patch[$f] = $data[$f] !== '' ? $data[$f] : null;
            }
        }
        if (array_key_exists('specifications', $data)) {
            $patch['specifications'] = $data['specifications'];
        }
        if (array_key_exists('is_active', $data)) {
            $patch['is_active'] = (int) (bool) $data['is_active'];
        }

        if ($patch !== []) {
            $this->products->update($product['id'], $patch);
        }

        if (isset($data['bulk_prices'])) {
            $this->saveBulkPrices($product['id'], $data['bulk_prices']);
            $this->products->update($product['id'], ['is_bulk' => !empty($data['bulk_prices']) ? 1 : 0]);
        }
        if (isset($data['images'])) {
            $this->saveImages($product['id'], $data['images']);
        }

        return $this->respondOK(['product_id' => (int) $product['id']], 'Product updated.');
    }

    public function delete($id = null)
    {
        $seller = $this->requireSeller();
        if ($seller instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $seller;
        }

        $product = $this->products->findForSeller((int) $seller['id'], (int) $id);
        if ($product === null) {
            return $this->respondNotFound('Product not found.');
        }

        $this->products->update($product['id'], ['is_active' => 0, 'status' => 'rejected']);

        return $this->respondOK(null, 'Product removed.');
    }

    /**
     * Seller's own products. Auth: seller.
     */
    public function mine()
    {
        $seller = $this->requireSeller();
        if ($seller instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $seller;
        }

        $limit  = min((int) ($this->request->getGet('limit') ?? 50), 100);
        $offset = max((int) ($this->request->getGet('offset') ?? 0), 0);

        return $this->respondOK([
            'items' => $this->productService->enrichMany($this->products->sellerProducts((int) $seller['id'], $limit, $offset)),
        ]);
    }

    /**
     * Bump inquiry count (called when a buyer sends an inquiry).
     */
    public function recordInquiry($id = null)
    {
        $product = $this->products->find($id);
        if ($product === null) {
            return $this->respondNotFound('Product not found.');
        }

        $this->products->bumpCounter((int) $id, 'inquiry_count');

        return $this->respondOK(null, 'Inquiry recorded.');
    }

    private function saveImages(int $productId, array $images): void
    {
        if ($images === []) {
            return;
        }

        (new ProductImageModel())->deleteForProduct($productId);
        $imageModel = new ProductImageModel();
        $sort = 0;
        foreach ($images as $i => $img) {
            $path = $img['file_path'] ?? null;
            if (!$path) {
                continue;
            }
            $imageModel->insert([
                'product_id' => $productId,
                'file_path'  => $path,
                'is_primary' => $i === 0 ? 1 : 0,
                'sort_order' => $sort++,
            ]);
        }
    }

    private function saveBulkPrices(int $productId, array $slabs): void
    {
        (new BulkPriceModel())->deleteForProduct($productId);
        $bulk = new BulkPriceModel();
        foreach ($slabs as $slab) {
            if (!isset($slab['min_qty'], $slab['unit_price'])) {
                continue;
            }
            $bulk->insert([
                'product_id' => $productId,
                'min_qty'    => $slab['min_qty'],
                'max_qty'    => $slab['max_qty'] ?? null,
                'unit_price' => $slab['unit_price'],
            ]);
        }
    }

    /**
     * @return array|ResponseInterface seller row or error response
     */
    private function requireSeller()
    {
        if ($this->userRole() !== 'seller') {
            return $this->respondForbidden('Only sellers can perform this action.');
        }

        $seller = (new SellerModel())->findByUser($this->userId());
        if ($seller === null) {
            return $this->respondNotFound('Seller business profile not found. Complete registration first.');
        }

        return $seller;
    }
}