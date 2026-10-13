<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\AdvertisementModel;
use App\Models\ProductModel;
use App\Services\ProductService;

/**
 * Public Catalog — browsing & search of approved products.
 * Routes: /catalog, /catalog/category/{slug}, /catalog/search, /product/{slug}
 */
class Catalog extends BaseController
{
    protected ProductModel $products;
    protected CategoryModel $categories;
    protected ProductService $service;

    public function __construct()
    {
        $this->products   = new ProductModel();
        $this->categories = new CategoryModel();
        $this->service    = new ProductService();
    }

    /**
     * GET /catalog — browsable listing with filters/sort/search.
     */
    public function index()
    {
        $q     = trim((string) $this->request->getGet('q'));
        $catId = (int) $this->request->getGet('category');
        $per   = min((int) ($this->request->getGet('per') ?? 24), 48);
        $page  = max((int) ($this->request->getGet('page') ?? 1), 1);
        $sort  = $this->request->getGet('sort') ?? 'featured';

        $db = db_connect();
        $builder = $db->table('products p')
            ->select('p.*, c.name AS category_name, c.slug AS category_slug,
                      b.name AS brand_name, s.business_name, s.slug AS seller_slug')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left')
            ->where('p.status', 'approved')
            ->where('p.is_active', 1)
            ->where('s.status', 'active');

        if ($catId > 0) {
            $builder->where('p.category_id', $catId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('p.name', $q)->orLike('p.description', $q)
                ->orLike('b.name', $q)->orLike('s.business_name', $q)
                ->groupEnd();
        }

        switch ($sort) {
            case 'price_asc':
                $builder->orderBy('COALESCE(NULLIF(p.offer_price, 0), p.price)', 'ASC', false);
                break;
            case 'price_desc':
                $builder->orderBy('COALESCE(NULLIF(p.offer_price, 0), p.price)', 'DESC', false);
                break;
            case 'newest':
                $builder->orderBy('p.created_at', 'DESC');
                break;
            default:
                $builder->orderBy('p.is_featured', 'DESC')->orderBy('p.created_at', 'DESC');
        }

        $total = (clone $builder)->countAllResults(false);
        $rows  = $builder->limit($per, ($page - 1) * $per)->get()->getResultArray();
        $products = $this->service->enrichMany($rows);

        $totalPages = max((int) ceil($total / $per), 1);

        foreach ($products as &$p) {
            $p['category_url'] = base_url('catalog/category/' . $p['category_slug']);
            $p['product_url']  = base_url('product/' . $p['slug']);
            $p['seller_url']   = base_url('seller/' . $p['seller_slug']);
        }
        unset($p);

        $selected = ($catId > 0) ? (new CategoryModel())->find($catId) : null;

        $banners = (new AdvertisementModel())->forSlot('category_banner', 2, $catId > 0 ? $catId : null);

        return view('catalog', [
            'products'     => $products,
            'categories'   => $this->categories->activeCategories(),
            'selected'     => $selected,
            'banners'      => $banners,
            'q'            => $q,
            'category_id'  => $catId,
            'sort'         => $sort,
            'page'         => $page,
            'total'        => $total,
            'totalPages'   => $totalPages,
            'per'          => $per,
            'meta'         => [
                'title'       => $q !== '' ? 'Search: "' . $q . '" — LOHA Mandi' : 'Browse Iron & Steel Products — LOHA Mandi',
                'description' => 'Browse TMT bars, MS plates, pipes, angles, channels and structural steel from verified sellers at Loha Mandi Ghaziabad. Compare prices and buy in bulk.',
            ],
        ]);
    }

    /**
     * GET /catalog/category/{slug}
     */
    public function category(string $slug)
    {
        $cat = $this->categories->findBySlug($slug);
        if ($cat === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Category not found.');
        }

        $page = max((int) ($this->request->getGet('page') ?? 1), 1);
        $per  = 24;

        $db = db_connect();
        $builder = $db->table('products p')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left')
            ->select('p.*, c.name AS category_name, c.slug AS category_slug,
                      b.name AS brand_name, s.business_name, s.slug AS seller_slug')
            ->where('p.status', 'approved')
            ->where('p.is_active', 1)
            ->where('s.status', 'active')
            ->where('p.category_id', $cat['id'])
            ->orderBy('p.is_featured', 'DESC')
            ->orderBy('p.created_at', 'DESC');

        $total = (clone $builder)->countAllResults(true);
        $rows  = $builder->limit($per, ($page - 1) * $per)->get()->getResultArray();
        $products = $this->service->enrichMany($rows);

        foreach ($products as &$p) {
            $p['product_url'] = base_url('product/' . $p['slug']);
            $p['seller_url']  = base_url('seller/' . $p['seller_slug']);
        }
        unset($p);

        $banners = (new AdvertisementModel())->forSlot('category_banner', 2, (int) $cat['id']);

        return view('catalog', [
            'products'     => $products,
            'categories'   => $this->categories->activeCategories(),
            'selected'     => $cat,
            'banners'      => $banners,
            'q'            => '',
            'category_id'  => (int) $cat['id'],
            'sort'         => 'featured',
            'page'         => $page,
            'total'        => $total,
            'totalPages'   => max((int) ceil($total / $per), 1),
            'per'          => $per,
            'meta'         => [
                'title'       => $cat['name'] . ' in Ghaziabad — LOHA Mandi',
                'description' => $cat['description'] ?: 'Buy ' . $cat['name'] . ' from verified steel sellers in Loha Mandi Ghaziabad. Compare prices and buy in bulk.',
            ],
        ]);
    }

    /**
     * GET /product/{slug} — detail page.
     */
    public function product(string $slug)
    {
        $db = db_connect();
        $row = $db->table('products p')
            ->select('p.*, c.name AS category_name, c.slug AS category_slug,
                      b.name AS brand_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->where('p.slug', $slug)
            ->where('p.status', 'approved')
            ->where('p.is_active', 1)
            ->get()->getRowArray();

        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Product not found.');
        }

        $this->products->bumpCounter((int) $row['id'], 'view_count');

        $product = $this->service->enrich($row);

        $seller = $product['seller'] ?? [];
        if (!$seller || $seller['status'] !== 'active') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Seller unavailable.');
        }

        $specs = [];
        if (!empty($product['specifications'])) {
            $decoded = is_array($product['specifications']) ? $product['specifications'] : json_decode($product['specifications'], true);
            $specs = is_array($decoded) ? $decoded : [];
        }

        $product['category_url'] = base_url('catalog/category/' . $product['category_slug']);
        $product['seller_url']   = base_url('seller/' . $seller['slug']);

        // AI-powered recommendations (similarity scoring over category/brand/spec/price).
        $related = (new \App\Services\AiService())->recommendFor((int) $product['id'], 4);
        $related = $this->service->enrichMany($related);

        return view('product_detail', [
            'product'     => $product,
            'specs'       => $specs,
            'seller'      => $seller,
            'related'     => $related,
            'auth'        => [
                'logged_in' => (bool) session()->get('uid'),
                'role'      => (string) session()->get('u_role'),
            ],
            'meta'        => [
                'title'       => $product['name'] . ' — ' . ($seller['business_name'] ?? '') . ' · LOHA Mandi',
                'description' => $product['description'] ?: ($product['name'] . ' priced at ₹' . number_format((float) $product['price']) . ' per ' . $product['unit'] . '. Buy in bulk from verified steel sellers.'),
            ],
        ]);
    }
}