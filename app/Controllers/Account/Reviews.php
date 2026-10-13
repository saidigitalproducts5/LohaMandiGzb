<?php

namespace App\Controllers\Account;

use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\ReviewModel;
use App\Models\SellerModel;

class Reviews extends AccountBase
{
    /**
     * Buyer: review a delivered order. GET shows form, POST saves.
     */
    public function create($orderId)
    {
        if ($this->uRole() !== 'buyer') {
            $this->flash('error', 'Only buyers can write reviews.');
            return redirect()->to('account/orders');
        }

        $order = (new OrderModel())->find((int) $orderId);
        if ($order === null || (int) $order['buyer_id'] !== $this->uid()) {
            $this->flash('error', 'Order not found.');
            return redirect()->to('account/orders');
        }
        if ($order['order_status'] !== 'delivered') {
            $this->flash('error', 'You can only review delivered orders.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        $reviews = new ReviewModel();
        if ($reviews->hasReviewed($this->uid(), (int) $orderId)) {
            $this->flash('info', 'You already reviewed this order.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        $seller = db_connect()->table('sellers')->where('id', $order['seller_id'])->get()->getRowArray();

        if ($this->request->getMethod() === 'POST') {
            $rating = (int) $this->request->getPost('rating');
            $review = trim((string) $this->request->getPost('review'));

            if ($rating < 1 || $rating > 5) {
                $this->flash('error', 'Please select a rating from 1 to 5 stars.');
                return redirect()->back();
            }

            $reviews->insert([
                'seller_id' => (int) $order['seller_id'],
                'buyer_id'  => $this->uid(),
                'order_id'  => (int) $orderId,
                'rating'    => $rating,
                'review'    => $review !== '' ? $review : null,
                'status'    => 'pending',
            ]);

            // Notify the seller's user.
            $sellerUser = db_connect()->table('sellers')->where('id', $order['seller_id'])->get()->getRow();
            if ($sellerUser && $sellerUser->user_id) {
                (new NotificationModel())->push(
                    (int) $sellerUser->user_id,
                    'New review received',
                    $rating . '-star review awaiting moderation.',
                    'review',
                    (int) $orderId
                );
            }

            $this->flash('success', 'Thanks! Your review is submitted and will appear once approved.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        return $this->render('account/review_form', [
            'order'  => $order,
            'seller' => $seller,
            'activeNav' => 'orders',
        ], 'Review Order ' . $order['order_number']);
    }

    /**
     * Seller: reply to a review on their profile.
     */
    public function reply($id)
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $review = (new ReviewModel())->find((int) $id);
        if ($review === null || (int) $review['seller_id'] !== (int) $seller['id']) {
            $this->flash('error', 'Review not found.');
            return redirect()->to('account/chats');
        }

        $reply = trim((string) $this->request->getPost('seller_reply'));
        if ($reply !== '') {
            (new ReviewModel())->update((int) $review['id'], ['seller_reply' => $reply]);

            (new NotificationModel())->push(
                (int) $review['buyer_id'],
                $seller['business_name'] . ' replied to your review',
                mb_substr($reply, 0, 120),
                'review',
                (int) $review['id']
            );
            $this->flash('success', 'Reply posted.');
        }

        return redirect()->to('account/seller-reviews');
    }

    /**
     * Seller: list reviews for their business + reply box.
     */
    public function sellerReviews()
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $rows = (new ReviewModel())->forSeller((int) $seller['id']);

        return $this->render('account/seller_reviews', [
            'rows' => $rows,
            'activeNav' => 'seller-reviews',
        ], 'Reviews');
    }
}