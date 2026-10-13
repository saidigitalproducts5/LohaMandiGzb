<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\SellerModel;

/**
 * Public landing pages for the "Built for the Steel Ecosystem" role cards:
 *   GET /suppliers — Browse Suppliers directory
 *   GET /post-rfq   — Post an RFQ (buyers)
 *   GET /transport  — Transporter "Get Listed"
 */
class Ecosystem extends BaseController
{
    public function suppliers()
    {
        $db = \Config\Database::connect();

        $rows = $db->table('sellers s')
            ->select('s.id, s.business_name, s.slug, s.city, s.state, s.gstin, s.logo,
                      s.gst_verified, s.is_verified, s.is_premium, s.rating_avg, s.rating_count, s.about,
                      (SELECT COUNT(*) FROM products p WHERE p.seller_id = s.id AND p.status = "approved" AND p.is_active = 1) AS product_count')
            ->where('s.status', 'active')
            ->orderBy('s.is_premium', 'DESC')
            ->orderBy('s.rating_avg', 'DESC')
            ->get()->getResultArray();

        foreach ($rows as &$r) {
            $r['storefront_url'] = base_url('seller/' . $r['slug']);
        }
        unset($r);

        return view('ecosystem/suppliers', [
            'suppliers' => $rows,
            'stats'     => [
                'sellers'  => count($rows),
                'products' => (int) $db->table('products')->where('status', 'approved')->countAllResults(),
                'city'     => 'Ghaziabad',
            ],
            'auth'      => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
            'meta'      => [
                'title'       => 'Browse Verified Steel Suppliers — LOHA Mandi',
                'description' => 'Browse verified steel traders, TMT dealers, pipe & plate suppliers in Ghaziabad. Compare ratings, listings and contact suppliers directly.',
            ],
        ]);
    }

    public function rfq()
    {
        $db = \Config\Database::connect();

        $categories = (new CategoryModel())->activeCategories();
        $counts = [
            'open_rfqs' => (int) $db->table('rfqs')->where('status', 'open')->countAllResults(),
            'sellers'   => (int) $db->table('sellers')->where('status', 'active')->countAllResults(),
        ];

        return view('ecosystem/rfq', [
            'categories' => $categories,
            'counts'     => $counts,
            'auth'       => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
            'meta'       => [
                'title'       => 'Post an RFQ & Get Multiple Quotes — LOHA Mandi',
                'description' => 'Post a steel requirement on Loha Mandi and receive quotations from multiple verified sellers. Compare prices and buy in bulk.',
            ],
        ]);
    }

    public function transport()
    {
        return view('ecosystem/transport', [
            'auth' => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
            'meta' => [
                'title'       => 'Transport & Freight for Steel Movement — LOHA Mandi',
                'description' => 'List your fleet, quote freight for steel movement and track deliveries across the Loha Mandi steel ecosystem.',
            ],
        ]);
    }

    public function faq()
    {
        return view('faq', [
            'auth' => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
        ]);
    }

    public function promotion()
    {
        return view('promotion', [
            'auth' => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
        ]);
    }

    public function privacyPolicy()
    {
        return view('privacy_policy', [
            'auth' => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
            'meta' => [
                'title'       => 'Privacy Policy — LOHA Mandi',
                'description' => 'Learn how LOHA Mandi collects, uses, and protects your personal information.',
            ],
        ]);
    }

    public function termsOfService()
    {
        return view('terms_of_service', [
            'auth' => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
            'meta' => [
                'title'       => 'Terms of Service — LOHA Mandi',
                'description' => 'Terms and conditions for using LOHA Mandi marketplace.',
            ],
        ]);
    }

    /**
     * Shared suppliers data loader for SEO pages.
     */
    private function loadSuppliers(string $title, string $description, string $heading, string $intro): string
    {
        $db = \Config\Database::connect();

        $rows = $db->table('sellers s')
            ->select('s.id, s.business_name, s.slug, s.city, s.state, s.gstin, s.logo,
                      s.gst_verified, s.is_verified, s.is_premium, s.rating_avg, s.rating_count, s.about,
                      (SELECT COUNT(*) FROM products p WHERE p.seller_id = s.id AND p.status = "approved" AND p.is_active = 1) AS product_count')
            ->where('s.status', 'active')
            ->orderBy('s.is_premium', 'DESC')
            ->orderBy('s.rating_avg', 'DESC')
            ->get()->getResultArray();

        foreach ($rows as &$r) {
            $r['storefront_url'] = base_url('seller/' . $r['slug']);
        }
        unset($r);

        return view('ecosystem/seo_suppliers', [
            'suppliers' => $rows,
            'stats'     => [
                'sellers'  => count($rows),
                'products' => (int) $db->table('products')->where('status', 'approved')->countAllResults(),
                'city'     => 'Ghaziabad',
            ],
            'auth'      => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
            'meta'      => ['title' => $title, 'description' => $description],
            'seo_heading' => $heading,
            'seo_intro'   => $intro,
        ]);
    }

    public function seoSteelSuppliers()
    {
        return $this->loadSuppliers(
            'Steel Suppliers in Loha Mandi Ghaziabad — LOHA Mandi',
            'Browse verified steel suppliers in Loha Mandi, Ghaziabad. Find TMT bars, MS plates, pipes, sheets and structural steel from trusted sellers.',
            'Steel Suppliers in Loha Mandi, Ghaziabad',
            'Browse verified steel suppliers, traders and dealers in Loha Mandi, Ghaziabad. Compare ratings, product listings and contact suppliers directly.'
        );
    }

    public function seoIronDealers()
    {
        return $this->loadSuppliers(
            'Iron Dealers in Loha Mandi Ghaziabad — LOHA Mandi',
            'Find iron dealers and traders in Loha Mandi, Ghaziabad. Browse iron products, compare prices and connect with sellers.',
            'Iron Dealers in Loha Mandi, Ghaziabad',
            'Find iron dealers, traders and suppliers in Loha Mandi, Ghaziabad. Browse iron products and connect with verified sellers.'
        );
    }

    public function seoMsSteelSuppliers()
    {
        return $this->loadSuppliers(
            'MS Steel Suppliers in Ghaziabad — LOHA Mandi',
            'Browse MS steel suppliers in Ghaziabad. Find MS plates, sheets, angles, channels and structural steel from Loha Mandi sellers.',
            'MS Steel Suppliers in Ghaziabad',
            'Browse MS steel suppliers in Loha Mandi, Ghaziabad. Find MS plates, sheets, angles, channels and structural steel from verified sellers.'
        );
    }

    public function seoSteelTraders()
    {
        return $this->loadSuppliers(
            'Steel Traders in Ghaziabad — LOHA Mandi',
            'Find steel traders in Ghaziabad. Browse steel products from Loha Mandi sellers including TMT bars, pipes, plates and more.',
            'Steel Traders in Ghaziabad',
            'Find steel traders in Loha Mandi, Ghaziabad. Browse steel products from verified sellers and connect directly.'
        );
    }

    public function seoTmtDealers()
    {
        return $this->loadSuppliers(
            'TMT Dealers in Ghaziabad — LOHA Mandi',
            'Find TMT bar dealers in Ghaziabad. Browse TMT products from Loha Mandi sellers, compare prices and contact dealers.',
            'TMT Dealers in Ghaziabad',
            'Find TMT bar dealers in Loha Mandi, Ghaziabad. Browse TMT products from verified sellers and get the best prices.'
        );
    }

    public function seoMsPipeSuppliers()
    {
        return $this->loadSuppliers(
            'MS Pipe Suppliers in Ghaziabad — LOHA Mandi',
            'Find MS pipe suppliers in Ghaziabad. Browse MS pipes, GI pipes and steel pipes from Loha Mandi sellers.',
            'MS Pipe Suppliers in Ghaziabad',
            'Find MS pipe suppliers in Loha Mandi, Ghaziabad. Browse MS pipes, GI pipes and steel pipes from verified sellers.'
        );
    }

    public function seoSteelSheetSuppliers()
    {
        return $this->loadSuppliers(
            'Steel Sheet Suppliers in Ghaziabad — LOHA Mandi',
            'Find steel sheet suppliers in Ghaziabad. Browse MS sheets, HR sheets, CR sheets and chequered plates from Loha Mandi sellers.',
            'Steel Sheet Suppliers in Ghaziabad',
            'Find steel sheet suppliers in Loha Mandi, Ghaziabad. Browse MS sheets, HR sheets, CR sheets and chequered plates from verified sellers.'
        );
    }
}