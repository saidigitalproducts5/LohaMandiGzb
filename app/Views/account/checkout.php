<?php
/** Checkout screen — live Razorpay checkout.js OR sandbox simulator. */
$base = rtrim(base_url(), '/');
$o = $order;
$verifyUrl = $base . '/account/orders/' . (int)$o['id'] . '/pay/verify';
$sandboxAllowed = $sandboxAllowed ?? false;
$sandboxPaymentId = 'pay_' . substr(bin2hex(random_bytes(8)), 0, 12);
$sandboxSig = '';
if (!$isLive && $sandboxAllowed) {
    $gateway = new \App\Libraries\Razorpay();
    $sandboxSig = $gateway->sandboxSignature($rzpOrderId, $sandboxPaymentId);
}
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>Order <?= esc($o['order_number']) ?></h3>
        <table style="margin-top:10px">
            <tbody>
                <tr><th style="width:50%">Total Payable</th><td><b style="font-size:20px">₹<?= number_format($amount, 2) ?></b></td></tr>
                <tr><th>Method</th><td><?= esc(ucwords(str_replace('_', ' ', $method))) ?></td></tr>
                <tr><th>Gateway Ref</th><td class="mono"><?= esc($rzpOrderId) ?></td></tr>
            </tbody>
        </table>
        <p class="muted" style="font-size:12px;margin-top:10px">
            <?= $isLive ? 'Secure payment via Razorpay.' : ($sandboxAllowed ? 'Sandbox mode — no real money is charged. Simulate a UPI payment below.' : 'Payments are temporarily unavailable.') ?>
        </p>
    </div>

    <div class="card" id="pay-box">
        <h3><?= $isLive ? 'Complete Payment' : ($sandboxAllowed ? 'Simulate UPI Payment' : 'Payment Unavailable') ?></h3>

        <?php if ($isLive): ?>
            <form id="rzp-form" method="post" action="<?= esc($verifyUrl) ?>">
                <input type="hidden" name="razorpay_order_id" id="rzp-order-id" value="">
                <input type="hidden" name="razorpay_payment_id" id="rzp-payment-id" value="">
                <input type="hidden" name="razorpay_signature" id="rzp-signature" value="">
            </form>
            <div id="rzp-status" class="alert info">Opening secure Razorpay checkout…</div>

            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                var options = {
                    key: <?= json_encode($rzpKeyId) ?>,
                    amount: <?= (int)$amountPaise ?>,
                    currency: 'INR',
                    name: 'LOHA MANDI',
                    description: 'Order <?= esc($o['order_number']) ?>',
                    order_id: <?= json_encode($rzpOrderId) ?>,
                    handler: function (response) {
                        document.getElementById('rzp-order-id').value = response.razorpay_order_id;
                        document.getElementById('rzp-payment-id').value = response.razorpay_payment_id;
                        document.getElementById('rzp-signature').value = response.razorpay_signature;
                        document.getElementById('rzp-form').submit();
                    },
                    theme: { color: '#e02735' }
                };
                var rzp = new Razorpay(options);
                rzp.open();
                rzp.on('payment.failed', function (resp) {
                    document.getElementById('rzp-status').className = 'alert error';
                    document.getElementById('rzp-status').textContent = 'Payment failed: ' + (resp.error && resp.error.description ? resp.error.description : 'Unknown error');
                });
            </script>
        <?php elseif ($sandboxAllowed): ?>
            <div class="alert info" style="margin-bottom:16px">
                Sandbox checkout — simulate a UPI success.
            </div>
            <form method="post" action="<?= esc($verifyUrl) ?>">
                <input type="hidden" name="razorpay_order_id" value="<?= esc($rzpOrderId) ?>">
                <input type="hidden" name="razorpay_payment_id" value="<?= esc($sandboxPaymentId) ?>">
                <input type="hidden" name="razorpay_signature" value="<?= esc($sandboxSig) ?>">
                <button class="btn btn-primary" type="submit" style="width:100%;padding:12px">
                    Simulate UPI Success — Pay ₹<?= number_format($amount, 2) ?>
                </button>
            </form>
            <a class="store-link" href="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>/pay">← Cancel and go back</a>
        <?php else: ?>
            <div class="alert error">Payments are temporarily unavailable. Please try again later.</div>
        <?php endif; ?>
    </div>
</div>