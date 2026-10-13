<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\SellerModel;
use App\Services\ProductService;

class SearchController extends ApiController
{
    /**
     * Unified search across products & sellers.
     * GET /api/search?q=&type=all|products|sellers&category_id=&city=&state=&
     *     gst_verified=&verified=&premium=&nearby_lat=&nearby_lng=&radius_km=&limit=
     */
    public function index()
    {
        $type  = $this->request->getGet('type') ?? 'all';
        $q     = trim((string) $this->request->getGet('q'));
        $limit = min((int) ($this->request->getGet('limit') ?? 20), 50);

        $result = ['products' => [], 'sellers' => []];

        if ($type === 'all' || $type === 'products') {
            $result['products'] = $this->searchProducts($q, $limit);
        }
        if ($type === 'all' || $type === 'sellers') {
            $result['sellers'] = $this->searchSellers($q, $limit);
        }

        return $this->respondOK($result);
    }

    private function searchProducts(string $q, int $limit): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, c.name AS category_name, b.name AS brand_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left')
            ->where('p.status', 'approved')
            ->where('p.is_active', 1);

        $categoryId = (int) ($this->request->getGet('category_id') ?? 0);
        if ($categoryId > 0) {
            $builder->where('p.category_id', $categoryId);
        }
        $brandId = (int) ($this->request->getGet('brand_id') ?? 0);
        if ($brandId > 0) {
            $builder->where('p.brand_id', $brandId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('p.name', $q)->orLike('p.description', $q)
                ->orLike('b.name', $q)
                ->groupEnd();
        }

        $city  = $this->request->getGet('city');
        $state = $this->request->getGet('state');
        if ($city) {
            $builder->like('p.location', $city);
        }
        if ($state) {
            $builder->like('p.location', $state);
        }

        $this->applyPriceStockFilters($builder);

        $rows = $builder->orderBy('p.is_featured', 'DESC')->orderBy('p.created_at', 'DESC')->limit($limit)->get()->getResultArray();

        return (new ProductService())->enrichMany($rows);
    }

    private function applyPriceStockFilters($builder): void
    {
        $priceMin = (float) ($this->request->getGet('price_min') ?? 0);
        $priceMax = (float) ($this->request->getGet('price_max') ?? 0);
        $ratingMin = (float) ($this->request->getGet('rating') ?? 0);
        $inStock  = (int) ($this->request->getGet('in_stock') ?? 0);

        if ($priceMin > 0) {
            $builder->where('p.price >=', $priceMin);
        }
        if ($priceMax > 0) {
            $builder->where('p.price <=', $priceMax);
        }
        if ($ratingMin > 0) {
            $builder->where('s.rating_avg >=', $ratingMin);
        }
        if ($inStock === 1) {
            $builder->where('p.available_stock >', 0);
        }
    }

    private function searchSellers(string $q, int $limit): array
    {
        $builder = (new SellerModel())->where('status', 'active');

        $qParam = $q !== '' ? $q : ($this->request->getGet('q') ?? '');
        if ($qParam !== '') {
            $builder->groupStart()
                ->like('business_name', $qParam)->orLike('city', $qParam)
                ->orLike('state', $qParam)->orLike('brands', $qParam)
                ->groupEnd();
        }

        $city  = $this->request->getGet('city');
        $state = $this->request->getGet('state');
        if ($city) {
            $builder->where('city', $city);
        }
        if ($state) {
            $builder->where('state', $state);
        }
        if ((int) ($this->request->getGet('gst_verified') ?? 0) === 1) {
            $builder->where('gst_verified', 1);
        }
        if ((int) ($this->request->getGet('verified') ?? 0) === 1) {
            $builder->where('is_verified', 1);
        }
        if ((int) ($this->request->getGet('premium') ?? 0) === 1) {
            $builder->where('is_premium', 1);
        }

        $this->applyDistanceFilter($builder);

        $sellers = $builder->orderBy('is_premium', 'DESC')->orderBy('rating_avg', 'DESC')->limit($limit)->findAll();

        // Attach product count per seller.
        $db = \Config\Database::connect();
        $ids = array_column($sellers, 'id');
        if ($ids !== []) {
            $counts = $db->table('products')
                ->select('seller_id, COUNT(*) AS cnt')
                ->whereIn('seller_id', $ids)
                ->where('status', 'approved')
                ->where('is_active', 1)
                ->groupBy('seller_id')
                ->get()->getResultArray();
            $map = array_column($counts, 'cnt', 'seller_id');
            foreach ($sellers as &$s) {
                $s['product_count'] = (int) ($map[$s['id']] ?? 0);
            }
        }

        return $sellers;
    }

    private function applyDistanceFilter($builder): void
    {
        $lat = $this->request->getGet('nearby_lat');
        $lng = $this->request->getGet('nearby_lng');
        $radius = (float) ($this->request->getGet('radius_km') ?? 25);

        if ($lat !== null && $lng !== null && is_numeric($lat) && is_numeric($lng)) {
            // Haversine approximation for local radius search.
            $builder->select("*, (6371 * ACOS(COS(RADIANS({$lat})) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS({$lng})) + SIN(RADIANS({$lat})) * SIN(RADIANS(latitude)))) AS distance_km")
                ->where('latitude IS NOT NULL')
                ->where('longitude IS NOT NULL')
                ->having('distance_km <=', $radius)
                ->orderBy('distance_km', 'ASC');
        }
    }

    /**
     * Suggested search keywords (typeahead).
     * GET /api/search/suggest?q=
     */
    public function suggest()
    {
        $q = trim((string) $this->request->getGet('q'));
        if ($q === '') {
            return $this->respondOK([]);
        }

        $db = \Config\Database::connect();
        $products = $db->table('products')
            ->select('name AS text, "product" AS type')
            ->like('name', $q)->where('status', 'approved')->where('is_active', 1)
            ->limit(8)->get()->getResultArray();

        $sellers = $db->table('sellers')
            ->select('business_name AS text, "seller" AS type')
            ->like('business_name', $q)->where('status', 'active')
            ->limit(8)->get()->getResultArray();

        // AI enhancement: if title matches are scarce, add keyword suggestions
        // derived from all approved product names (lightweight offline suggestion).
        if (count($products) < 4) {
            $ai = new \App\Services\AiService();
            $aiRows = $ai->searchSuggestions($q, 8);
            foreach ($aiRows as $r) {
                $hit = null;
                foreach ($products as $p) {
                    if ($p['name'] === $r['name']) {
                        $hit = $p;
                        break;
                    }
                }
                if ($hit === null) {
                    $products[] = ['name' => $r['name'], 'type' => 'product'];
                }
            }
        }

        return $this->respondOK(array_merge($products, $sellers));
    }
}