<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SellerModel;
use App\Services\ProductService;

/**
 * Storefront — public seller profile page with product catalog.
 * GET /seller/{slug}
 */
class Storefront extends BaseController
{
    public function index(string $slug)
    {
        $seller = (new SellerModel())->findBySlug($slug);
        if ($seller === null || $seller['status'] !== 'active') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Seller not found.');
        }

        $products = (new ProductModel())
            ->where('seller_id', $seller['id'])
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->orderBy('is_featured', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
        $products = (new ProductService())->enrichMany($products);

        $db = \Config\Database::connect();
        $stats = [
            'products' => count($products),
            'leads'    => (int) $db->table('leads')->where('seller_id', $seller['id'])->countAllResults(),
            'orders'   => (int) $db->table('orders')->where('seller_id', $seller['id'])->countAllResults(),
        ];

        $reviews = (new \App\Models\ReviewModel())->forSeller((int) $seller['id'], 6);
        $reviews = array_values(array_filter($reviews, fn ($r) => $r['status'] === 'approved'));

        // Can the current visitor leave a store review?
        $reviewState = 'guest';
        $uid = (int) session()->get('uid');
        if ($uid > 0) {
            $role = (string) session()->get('u_role');
            if ($role === 'seller') {
                $reviewState = 'seller';
            } elseif ((new \App\Models\ReviewModel())->hasReviewedSeller((int) $seller['id'], $uid)) {
                $reviewState = 'done';
            } else {
                $reviewState = 'ok';
            }
        }

        return view('storefront', [
            'seller'   => $seller,
            'products' => $products,
            'stats'    => $stats,
            'reviews'  => $reviews,
            'reviewState' => $reviewState,
            'auth'     => [
                'logged_in' => (bool) session()->get('uid'),
                'role'      => (string) session()->get('u_role'),
            ],
            'meta'     => [
                'title'       => $seller['business_name'] . ' — LOHA MART',
                'description' => 'Verified ' . ($seller['gst_verified'] ? 'GST-verified ' : '') . 'steel seller in ' . ($seller['city'] ?: 'Ghaziabad') . '. ' . $seller['about'],
            ],
        ]);
    }

    /**
     * GET /seller/{slug}/review — the form lives inline on the storefront page.
     */
    public function reviewForm(string $slug)
    {
        return redirect()->to('seller/' . $slug . '#reviews');
    }

    /**
     * POST /seller/{slug}/review — submit a store rating/review.
     */
    public function submitReview(string $slug)
    {
        $seller = (new SellerModel())->findBySlug($slug);
        if ($seller === null || $seller['status'] !== 'active') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Seller not found.');
        }

        $uid = (int) session()->get('uid');
        if ($uid <= 0) {
            $this->setFlash('info', 'Please sign in to write a review.');
            return redirect()->to('login');
        }

        $role = (string) session()->get('u_role');
        if ($role === 'seller') {
            $this->setFlash('error', 'Sellers cannot leave reviews.');
            return redirect()->to('seller/' . $slug . '#reviews');
        }

        $reviews = new \App\Models\ReviewModel();
        if ($reviews->hasReviewedSeller((int) $seller['id'], $uid)) {
            $this->setFlash('info', 'You have already reviewed this seller. Thank you!');
            return redirect()->to('seller/' . $slug . '#reviews');
        }

        if ($this->request->getMethod() === 'POST') {
            $rating = (int) $this->request->getPost('rating');
            $text   = trim((string) $this->request->getPost('review'));

            if ($rating < 1 || $rating > 5) {
                $this->setFlash('error', 'Please select a rating from 1 to 5 stars.');
                return redirect()->to('seller/' . $slug . '#reviews');
            }

            $reviewId = $reviews->insert([
                'seller_id' => (int) $seller['id'],
                'buyer_id'  => $uid,
                'rating'    => $rating,
                'review'    => $text !== '' ? mb_substr($text, 0, 1000) : null,
                'status'    => 'pending',
            ]);

            // Notify the seller.
            $sellerUser = db_connect()->table('sellers')->where('id', (int) $seller['id'])->get()->getRow();
            if ($sellerUser && $sellerUser->user_id) {
                (new \App\Models\NotificationModel())->push(
                    (int) $sellerUser->user_id,
                    'New review received',
                    $rating . '-star review awaiting moderation.',
                    'review',
                    (int) $reviewId
                );
            }

            $this->setFlash('success', 'Thanks! Your review has been submitted and will appear once approved.');
            return redirect()->to('seller/' . $slug . '#reviews');
        }

        return redirect()->to('seller/' . $slug . '#reviews');
    }

    private function setFlash(string $type, string $message): void
    {
        session()->setFlashdata('flash_' . $type, $message);
    }
}
