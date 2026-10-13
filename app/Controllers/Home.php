<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\AdvertisementModel;
use App\Models\ProductModel;
use App\Services\ProductService;

class Home extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        $session = session();

        $categories = (new CategoryModel())->activeCategories();
        $adModel = new AdvertisementModel();

        $heroBanners   = $adModel->forSlot('homepage_banner', 3);
        $sponsored     = $adModel->sponsoredProducts(4);
        $featuredSellers = $adModel->featuredSellers(3);

        $featured = (new ProductModel())
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->where('offer_price IS NOT NULL')
            ->orderBy('is_featured', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit(8)
            ->findAll();
        $featured = (new ProductService())->enrichMany($featured);

        $stats = [
            'products'  => (int) $db->table('products')->where('status', 'approved')->countAllResults(),
            'sellers'   => (int) $db->table('sellers')->where('status', 'active')->countAllResults(),
            'categories' => count($categories),
            'rfqs'      => (int) $db->table('rfqs')->countAllResults(),
        ];

        return view('home', [
            'categories' => $categories,
            'featured'   => $featured,
            'heroBanners' => $heroBanners,
            'sponsored'  => $sponsored,
            'featuredSellers' => $featuredSellers,
            'stats'      => $stats,
            'auth'       => [
                'logged_in' => (bool) $session->get('uid'),
                'name'      => (string) $session->get('u_name'),
                'role'      => (string) $session->get('u_role'),
            ],
        ]);
    }
}