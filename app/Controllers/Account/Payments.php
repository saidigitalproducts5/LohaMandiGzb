<?php

namespace App\Controllers\Account;

use App\Libraries\Razorpay;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class Payments extends AccountBase
{
    /**
     * Payment page for an order (buyer only).
     */
    public function pay($orderId)
    {
        if ($this->uRole() === 'seller') {
            $this->flash('error', 'Only buyers can pay for orders.');
            return redirect()->to('account/orders');
        }

        $order = (new OrderModel())->find((int) $orderId);
        if ($order === null || (int) $order['buyer_id'] !== $this->uid()) {
            $this->flash('error', 'Order not found.');
            return redirect()->to('account/orders');
        }
        if ($order['payment_status'] === 'paid') {
            $this->flash('info', 'This order is already paid.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }
        if ($order['order_status'] === 'cancelled') {
            $this->flash('error', 'Cancelled orders cannot be paid.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        $seller = db_connect()->table('sellers')->where('id', $order['seller_id'])->get()->getRowArray();
        $existing = (new PaymentModel())->forOrder((int) $orderId);

        return $this->render('account/pay', [
            'order'    => $order,
            'seller'   => $seller,
            'existing' => $existing,
            'activeNav'=> 'orders',
        ], 'Pay — Order ' . $order['order_number']);
    }

    /**
     * Initiate a payment: record a pending payment + create a Razorpay order,
     * then show the checkout view (live checkout.js or sandbox simulate).
     */
    public function initiate($orderId)
    {
        if ($this->uRole() === 'seller') {
            $this->flash('error', 'Only buyers can pay for orders.');
            return redirect()->to('account/orders');
        }

        $order = (new OrderModel())->find((int) $orderId);
        if ($order === null || (int) $order['buyer_id'] !== $this->uid()) {
            $this->flash('error', 'Order not found.');
            return redirect()->to('account/orders');
        }
        if ($order['payment_status'] === 'paid') {
            $this->flash('info', 'This order is already paid.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        $method = trim((string) $this->request->getPost('method'));
        $allowed = ['upi', 'net_banking', 'debit_card', 'credit_card', 'wallet', 'razorpay'];
        if (!in_array($method, $allowed, true)) {
            $this->flash('error', 'Please choose a payment method.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        $amountPaise = (int) round(((float) $order['total']) * 100);
        if ($amountPaise <= 0) {
            $this->flash('error', 'Invalid amount.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        $gateway = new Razorpay();

        // Fail closed when the gateway is unavailable (no keys configured).
        if (!$gateway->isLive() && !$gateway->simulationAllowed()) {
            $this->flash('error', 'Payment gateway is not configured. Please try again later.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        try {
            $rzpOrder = $gateway->createOrder([
                'amount'   => $amountPaise,
                'currency' => 'INR',
                'receipt'  => $order['order_number'],
                'notes'    => [
                    'order_id' => (int) $orderId,
                    'buyer_id' => (int) $this->uid(),
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Razorpay createOrder failed: ' . $e->getMessage());
            $this->flash('error', 'Payment gateway error. Please try again.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        $payments = new PaymentModel();
        $paymentId = (int) $payments->insert([
            'order_id'        => (int) $orderId,
            'user_id'         => $this->uid(),
            'amount'          => (float) $order['total'],
            'method'          => $method,
            'txn_id'          => $rzpOrder['id'],
            'status'          => 'pending',
            'gateway_response'=> json_encode([
                'rzp_order_id' => $rzpOrder['id'],
                'mode'         => $gateway->isLive() ? 'live' : 'sandbox',
            ]),
        ]);

        if ($paymentId === 0) {
            $this->flash('error', 'Could not record the payment. Please try again.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        $this->session->set('rzp_payment_id', $paymentId);

        return $this->render('account/checkout', [
            'order'       => $order,
            'amount'      => (float) $order['total'],
            'amountPaise' => $amountPaise,
            'method'      => $method,
            'rzpOrderId'  => $rzpOrder['id'],
            'rzpKeyId'    => $gateway->keyId(),
            'isLive'      => $gateway->isLive(),
            'sandboxAllowed' => $gateway->simulationAllowed(),
            'activeNav'   => 'orders',
        ], 'Checkout — ' . $order['order_number']);
    }

    /**
     * Verify the payment and mark the order paid.
     */
    public function verify($orderId)
    {
        $order = (new OrderModel())->find((int) $orderId);
        if ($order === null || (int) $order['buyer_id'] !== $this->uid()) {
            $this->flash('error', 'Order not found.');
            return redirect()->to('account/orders');
        }
        if ($order['payment_status'] === 'paid') {
            $this->flash('info', 'This order is already paid.');
            return redirect()->to('account/orders/' . (int) $orderId);
        }

        $payments = new PaymentModel();
        $payment = $payments->where('order_id', (int) $orderId)
            ->where('status', 'pending')
            ->orderBy('id', 'DESC')
            ->first();
        if ($payment === null) {
            $this->flash('error', 'No pending payment found for this order.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        $gateway = new Razorpay();

        // Fail closed when the gateway is unavailable (no keys configured).
        if (!$gateway->isLive() && !$gateway->simulationAllowed()) {
            $this->flash('error', 'Payment gateway is not configured. Please try again later.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        $success = false;

        if ($gateway->isLive()) {
            $success = $gateway->verifySignature([
                'razorpay_order_id'   => (string) $this->request->getPost('razorpay_order_id'),
                'razorpay_payment_id' => (string) $this->request->getPost('razorpay_payment_id'),
                'razorpay_signature'  => (string) $this->request->getPost('razorpay_signature'),
            ]);
        } else {
            // Sandbox mode: accept the pre-signed simulate POST.
            $success = $gateway->verifySignature([
                'razorpay_order_id'   => (string) $this->request->getPost('razorpay_order_id'),
                'razorpay_payment_id' => (string) $this->request->getPost('razorpay_payment_id'),
                'razorpay_signature'  => (string) $this->request->getPost('razorpay_signature'),
            ]);
        }

        $razorpayPaymentId = (string) $this->request->getPost('razorpay_payment_id');

        if (!$success) {
            $payments->update((int) $payment['id'], [
                'status'           => 'failed',
                'gateway_response' => json_encode([
                    'error'                => 'Signature verification failed.',
                    'razorpay_payment_id'  => $razorpayPaymentId,
                ]),
            ]);
            $this->flash('error', 'Payment verification failed. Please try again.');
            return redirect()->to('account/orders/' . (int) $orderId . '/pay');
        }

        $payments->update((int) $payment['id'], [
            'status'           => 'success',
            'txn_id'           => $razorpayPaymentId !== '' ? $razorpayPaymentId : $payment['txn_id'],
            'gateway_response' => json_encode([
                'rzp_order_id'         => (string) $this->request->getPost('razorpay_order_id'),
                'rzp_payment_id'       => $razorpayPaymentId,
                'verification'         => $gateway->isLive() ? 'live' : 'sandbox',
            ]),
        ]);

        (new OrderModel())->update((int) $orderId, [
            'payment_status' => 'paid',
            'payment_method' => $payment['method'] ?? 'upi',
        ]);

        // Notify the seller of the received payment.
        $sellerUser = db_connect()->table('sellers')->select('user_id')->where('id', $order['seller_id'])->get()->getRow();
        if ($sellerUser && $sellerUser->user_id) {
            (new \App\Models\NotificationModel())->push(
                (int) $sellerUser->user_id,
                'Payment received',
                '₹' . number_format((float)$order['total'], 2) . ' paid for order ' . $order['order_number'] . '.',
                'payment',
                (int) $orderId
            );
        }

        $this->flash('success', 'Payment successful! Your order is confirmed.');
        return redirect()->to('account/orders/' . (int) $orderId);
    }

    /**
     * Buyer payment history.
     */
    public function history()
    {
        if ($this->uRole() === 'seller') {
            $this->flash('error', 'Only buyers can view payment history.');
            return redirect()->to('account/orders');
        }

        $rows = (new PaymentModel())->forUser($this->uid());

        return $this->render('account/payments', [
            'rows'      => $rows,
            'activeNav' => 'payments',
        ], 'My Payments');
    }
}