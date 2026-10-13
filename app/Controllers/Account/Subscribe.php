<?php

namespace App\Controllers\Account;

use App\Models\MembershipPlanModel;
use App\Models\PaymentModel;
use App\Models\SellerMembershipModel;
use App\Models\SellerModel;
use App\Libraries\Razorpay;

/**
 * Seller subscription — pay/verify flow for an existing (logged-in) seller
 * who has not yet paid. Routes: /account/subscribe, .../pay, .../verify
 */
class Subscribe extends AccountBase
{
    private function requireSeller(): ?array
    {
        if ($this->uRole() !== 'seller') {
            $this->flash('error', 'Subscriptions are for seller accounts.');
            return null;
        }

        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return null;
        }

        return $seller;
    }

    /**
     * GET /account/subscribe — show the plan + pay button.
     */
    public function index()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }

        $membership = new SellerMembershipModel();
        if ($membership->isSubscribed((int) $seller['id'])) {
            $this->flash('info', 'Your subscription is already active.');
            return redirect()->to('account/profile');
        }

        $plan = (new MembershipPlanModel())->ensureSellerPlan();

        return $this->render('account/subscribe', [
            'plan'      => $plan,
            'seller'    => $seller,
            'activeNav' => 'profile',
        ], 'Seller Subscription');
    }

    /**
     * GET /account/subscribe/pay — create a Razorpay order, show checkout.
     */
    public function pay()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }

        $membership = new SellerMembershipModel();
        if ($membership->isSubscribed((int) $seller['id'])) {
            $this->flash('info', 'Your subscription is already active.');
            return redirect()->to('account/profile');
        }

        $plan = (new MembershipPlanModel())->ensureSellerPlan();
        $amountPaise = (int) round(((float) $plan['price']) * 100);

        $gateway = new Razorpay();

        // Fail closed when the gateway is unavailable (no keys configured).
        if (!$gateway->isLive() && !$gateway->simulationAllowed()) {
            $this->flash('error', 'Payment gateway is not configured. Please try again later.');
            return redirect()->to('account/subscribe');
        }

        try {
            $rzpOrder = $gateway->createOrder([
                'amount'   => $amountPaise,
                'currency' => 'INR',
                'receipt'  => 'SELLER-SUB-' . time(),
                'notes'    => [
                    'seller_id' => (int) $seller['id'],
                    'kind'      => 'seller_subscription',
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Seller subscribe createOrder failed: ' . $e->getMessage());
            $this->flash('error', 'Payment gateway error. Please try again.');
            return redirect()->to('account/subscribe');
        }

        $this->session->set('seller_sub_rzp', [
            'id'       => $rzpOrder['id'],
            'amount'   => (float) $plan['price'],
            'plan_id'  => (int) $plan['id'],
            'seller_id'=> (int) $seller['id'],
            'is_live'  => $gateway->isLive(),
        ]);

        $sandboxPaymentId = '';
        $sandboxSignature = '';
        if ($gateway->simulationAllowed()) {
            $sandboxPaymentId = 'pay_' . substr(bin2hex(random_bytes(8)), 0, 12);
            $sandboxSignature = $gateway->sandboxSignature($rzpOrder['id'], $sandboxPaymentId);
        }

        $pending = [
            'name'   => $seller['business_name'],
            'mobile' => db_connect()->table('users')->select('mobile')->where('id', $this->uid())->get()->getRow()->mobile ?? '',
        ];

        return $this->render('account/seller_subscribe_verify', [
            'pending' => $pending,
            'plan'    => $plan,
            'amountPaise'    => $amountPaise,
            'rzpKeyId'       => $gateway->keyId(),
            'rzpOrderId'     => $rzpOrder['id'],
            'isLive'         => $gateway->isLive(),
            'sandboxAllowed' => $gateway->simulationAllowed(),
            'sandboxPaymentId'   => $sandboxPaymentId,
            'sandboxSignature'   => $sandboxSignature,
            'verifyUrl'          => base_url('account/subscribe/pay'),
            'title2'             => 'Complete Payment',
            'activeNav'          => 'profile',
        ], 'Complete Payment');
    }

    /**
     * POST /account/subscribe/pay — verify payment, activate membership.
     */
    public function verify()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }

        $sub = $this->session->get('seller_sub_rzp');
        if (!is_array($sub)) {
            $this->flash('error', 'Your payment session expired. Please start again.');
            return redirect()->to('account/subscribe');
        }

        $gateway = new Razorpay();

        // Fail closed when the gateway is unavailable (no keys configured).
        if (!$gateway->isLive() && !$gateway->simulationAllowed()) {
            $this->flash('error', 'Payment gateway is not configured. Please try again later.');
            return redirect()->to('account/subscribe');
        }

        $success = $gateway->verifySignature([
            'razorpay_order_id'   => (string) $this->request->getPost('razorpay_order_id'),
            'razorpay_payment_id' => (string) $this->request->getPost('razorpay_payment_id'),
            'razorpay_signature'  => (string) $this->request->getPost('razorpay_signature'),
        ]);

        if (!$success) {
            $this->flash('error', 'Payment verification failed. Your subscription was not activated.');
            return redirect()->to('account/subscribe');
        }

        // Also guard against mismatched seller in session.
        if ((int) $sub['seller_id'] !== (int) $seller['id']) {
            $this->flash('error', 'Payment mismatch. Please try again.');
            return redirect()->to('account/subscribe');
        }

        $db = db_connect();
        $db->transStart();

        (new SellerMembershipModel())->grantYearly((int) $seller['id'], (int) $sub['plan_id'], (float) $sub['amount']);

        $db->table('payments')->insert([
            'order_id' => null,
            'user_id'  => $this->uid(),
            'amount'   => (float) $sub['amount'],
            'method'   => 'razorpay',
            'txn_id'   => (string) $this->request->getPost('razorpay_payment_id'),
            'status'   => 'success',
            'gateway_response' => json_encode([
                'rzp_order_id' => (string) $this->request->getPost('razorpay_order_id'),
                'kind'         => 'seller_subscription',
                'plan_id'      => (int) $sub['plan_id'],
            ]),
        ]);

        $db->transCommit();

        $this->session->remove('seller_sub_rzp');

        $this->flash('success', 'Payment received! Your seller subscription is now active.');
        return redirect()->to('account/profile');
    }
}