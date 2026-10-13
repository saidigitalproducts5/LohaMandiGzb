<?php

namespace App\Controllers;

use App\Models\AdvertisementModel;

class Ad extends BaseController
{
    /**
     * GET /ad/view/{id} — landing page showing the ad image and seller info.
     */
    public function view($id)
    {
        $model = new AdvertisementModel();
        $ad = $model->where('id', (int) $id)->where('status', 1)->first();

        if ($ad === null) {
            return redirect()->to('/');
        }

        $model->incrementClicks((int) $ad['id']);

        $db = db_connect();
        $seller = null;
        if (!empty($ad['seller_id'])) {
            $seller = $db->table('sellers')
                ->select('id, business_name, slug, city, state, about, logo, rating_avg, rating_count, gst_verified, is_verified, is_premium')
                ->where('id', (int) $ad['seller_id'])
                ->where('status', 'active')
                ->get()->getRowArray();
        }

        $product = null;
        if (!empty($ad['product_id'])) {
            $product = $db->table('products')
                ->select('id, name, slug, price, offer_price, unit')
                ->where('id', (int) $ad['product_id'])
                ->get()->getRowArray();
        }

        return view('ad_landing', [
            'ad'     => $ad,
            'seller' => $seller,
            'product'=> $product,
            'auth'   => ['logged_in' => (bool) session()->get('uid'), 'role' => (string) session()->get('u_role')],
        ]);
    }

    /**
     * GET /ad/go/{id} — increments clicks and redirects to the ad link.
     */
    public function go($id)
    {
        $model = new AdvertisementModel();
        $ad = $model->where('id', (int) $id)->where('status', 1)->first();

        if ($ad === null) {
            return redirect()->to('/');
        }

        $model->incrementClicks((int) $ad['id']);

        if (!empty($ad['link'])) {
            return redirect()->to($ad['link']);
        }

        if ($ad['type'] === 'featured_seller' && !empty($ad['seller_id'])) {
            return redirect()->to('seller/' . $this->sellerSlug((int) $ad['seller_id']));
        }

        if ($ad['type'] === 'sponsored_product' && !empty($ad['product_id'])) {
            return redirect()->to('product/' . $this->productSlug((int) $ad['product_id']));
        }

        if ($ad['type'] === 'category_banner' && !empty($ad['category_id'])) {
            return redirect()->to('catalog/category/' . $this->categorySlug((int) $ad['category_id']));
        }

        return redirect()->to('/');
    }

    private function sellerSlug(int $id): string
    {
        $row = db_connect()->table('sellers')->select('slug')->where('id', $id)->get()->getRowArray();
        return $row['slug'] ?? (string) $id;
    }

    private function productSlug(int $id): string
    {
        $row = db_connect()->table('products')->select('slug')->where('id', $id)->get()->getRowArray();
        return $row['slug'] ?? (string) $id;
    }

    private function categorySlug(int $id): string
    {
        $row = db_connect()->table('categories')->select('slug')->where('id', $id)->get()->getRowArray();
        return $row['slug'] ?? (string) $id;
    }
}
