<?php

namespace App\Controllers\Account;

use App\Models\OrderModel;
use App\Models\QuotationModel;
use App\Models\SellerModel;

class Orders extends AccountBase
{
    public function index()
    {
        $db = \Config\Database::connect();

        if ($this->uRole() === 'seller') {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller === null) {
                $this->flash('error', 'Seller profile not found.');
                return redirect()->to('account');
            }
            $rows = db_connect()->table('orders o')
                ->select('o.*, u.name AS buyer_name, u.mobile AS buyer_mobile, q.quote_number')
                ->join('users u', 'u.id = o.buyer_id', 'left')
                ->join('quotations q', 'q.id = o.quote_id', 'left')
                ->where('o.seller_id', $seller['id'])
                ->orderBy('o.created_at', 'DESC')
                ->get()->getResultArray();
            $counts = $this->statusCounts((int) $seller['id'], 'seller');

            return $this->render('account/orders', [
                'rows'      => $rows,
                'isSeller'  => true,
                'counts'    => $counts,
                'activeNav' => 'orders',
            ], 'Orders');
        }

        $rows = db_connect()->table('orders o')
            ->select('o.*, s.business_name, q.quote_number')
            ->join('sellers s', 's.id = o.seller_id', 'left')
            ->join('quotations q', 'q.id = o.quote_id', 'left')
            ->where('o.buyer_id', $this->uid())
            ->orderBy('o.created_at', 'DESC')
            ->get()->getResultArray();
        $counts = $this->statusCounts($this->uid(), 'buyer');

        return $this->render('account/orders', [
            'rows'      => $rows,
            'isSeller'  => false,
            'counts'    => $counts,
            'activeNav' => 'orders',
        ], 'Orders');
    }

    public function show($id)
    {
        $order = (new OrderModel())->find((int) $id);
        if ($order === null) {
            $this->flash('error', 'Order not found.');
            return redirect()->to('account/orders');
        }

        $isSeller = $this->uRole() === 'seller';
        if ($isSeller) {
            $seller = (new SellerModel())->findByUser($this->uid());
            if ($seller === null || (int) $order['seller_id'] !== (int) $seller['id']) {
                $this->flash('error', 'You cannot view this order.');
                return redirect()->to('account/orders');
            }
        } elseif ((int) $order['buyer_id'] !== $this->uid()) {
            $this->flash('error', 'You cannot view this order.');
            return redirect()->to('account/orders');
        }

        $db = db_connect();
        $items = $db->table('order_items')->where('order_id', (int) $id)->get()->getResultArray();
        $quote = $order['quote_id'] ? $db->table('quotations')->where('id', $order['quote_id'])->get()->getRowArray() : null;
        $seller = $db->table('sellers')->where('id', $order['seller_id'])->get()->getRowArray();
        $buyer = $db->table('users')->where('id', $order['buyer_id'])->get()->getRowArray();

        return $this->render('account/order_view', [
            'order'    => $order,
            'items'    => $items,
            'quote'    => $quote,
            'seller'   => $seller,
            'buyer'    => $buyer,
            'isSeller' => $isSeller,
            'activeNav'=> 'orders',
        ], 'Order ' . $order['order_number']);
    }

    /**
     * Buyer accepts a quotation -> creates an order.
     */
    public function acceptQuote($quoteId)
    {
        if ($this->uRole() === 'seller') {
            $this->flash('error', 'Only buyers can accept quotations.');
            return redirect()->to('account/quotations');
        }

        $quote = (new QuotationModel())->find((int) $quoteId);
        if ($quote === null) {
            $this->flash('error', 'Quotation not found.');
            return redirect()->to('account/quotations');
        }
        if ((int) $quote['buyer_id'] !== $this->uid()) {
            $this->flash('error', 'You cannot accept this quotation.');
            return redirect()->to('account/quotations');
        }
        if ($quote['status'] !== 'sent') {
            $this->flash('error', 'This quotation cannot be accepted (status: ' . $quote['status'] . ').');
            return redirect()->to('account/quotations');
        }

        $shippingAddress = trim((string) $this->request->getPost('shipping_address'));

        // Create the order from the quotation.
        $orderId = (int) (new OrderModel())->insert([
            'order_number'    => (new OrderModel())->nextOrderNumber(),
            'buyer_id'        => (int) $quote['buyer_id'],
            'seller_id'       => (int) $quote['seller_id'],
            'quote_id'        => (int) $quote['id'],
            'subtotal'        => (float) $quote['base_amount'],
            'gst_amount'      => (float) $quote['gst_amount'],
            'discount'        => (float) $quote['discount'],
            'shipping'        => ((float) $quote['transport_charge']) + ((float) $quote['other_charges']),
            'total'           => (float) $quote['total_amount'],
            'payment_status'  => 'pending',
            'order_status'    => 'confirmed',
            'shipping_address'=> $shippingAddress !== '' ? $shippingAddress : null,
        ]);

        db_connect()->table('order_items')->insert([
            'order_id'     => $orderId,
            'product_id'   => null,
            'product_name' => $quote['product_name'],
            'quantity'     => (float) $quote['quantity'],
            'unit'         => $quote['unit'],
            'unit_price'   => (float) $quote['unit_price'],
            'total'        => (float) $quote['base_amount'],
        ]);

        // Mark the quotation accepted + RFQ accepted.
        (new QuotationModel())->update((int) $quote['id'], ['status' => 'accepted']);
        if ($quote['rfq_id']) {
            db_connect()->table('rfqs')->where('id', $quote['rfq_id'])->update(['status' => 'accepted']);
        }

        // Notify the seller.
        $sellerUser = db_connect()->table('sellers')->select('user_id')->where('id', $quote['seller_id'])->get()->getRow();
        if ($sellerUser && $sellerUser->user_id) {
            (new \App\Models\NotificationModel())->push(
                (int) $sellerUser->user_id,
                'New order placed',
                'Order for ₹' . number_format((float)$quote['total_amount'], 2) . '.',
                'order',
                $orderId
            );
        }

        $this->flash('success', 'Order created. Track it on your Orders page.');

        return redirect()->to('account/orders/' . $orderId);
    }

    /**
     * Seller updates order status (dispatched, in_transit, delivered).
     */
    public function updateStatus($id)
    {
        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return redirect()->to('account');
        }

        $order = (new OrderModel())->find((int) $id);
        if ($order === null || (int) $order['seller_id'] !== (int) $seller['id']) {
            $this->flash('error', 'Order not found.');
            return redirect()->to('account/orders');
        }

        $valid = ['confirmed', 'dispatched', 'in_transit', 'delivered', 'cancelled'];
        $new = trim((string) $this->request->getPost('order_status'));
        if (!in_array($new, $valid, true)) {
            $this->flash('error', 'Invalid status.');
            return redirect()->back();
        }

        $patch = ['order_status' => $new];
        if ($this->request->getPost('lr_number') !== null) {
            $patch['lr_number'] = trim((string) $this->request->getPost('lr_number')) !== '' ? trim((string) $this->request->getPost('lr_number')) : null;
        }
        if ($this->request->getPost('transport_details') !== null) {
            $patch['transport_details'] = trim((string) $this->request->getPost('transport_details')) !== '' ? trim((string) $this->request->getPost('transport_details')) : null;
        }

        (new OrderModel())->update((int) $id, $patch);
        $this->flash('success', 'Order status updated to ' . $new . '.');

        return redirect()->to('account/orders/' . (int) $id);
    }

    private function statusCounts(int $id, string $who): array
    {
        $db = db_connect();
        $where = $who === 'seller' ? ['seller_id' => $id] : ['buyer_id' => $id];
        $statuses = ['confirmed', 'dispatched', 'in_transit', 'delivered', 'cancelled'];
        $counts = [];
        foreach ($statuses as $s) {
            $counts[$s] = (int) $db->table('orders')->where($where)->where('order_status', $s)->countAllResults();
        }
        $counts['total'] = (int) $db->table('orders')->where($where)->countAllResults();

        return $counts;
    }
}