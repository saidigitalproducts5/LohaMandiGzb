<?php
/** Seller subscription verify — live Razorpay checkout OR sandbox simulator (account layout). */
$base = rtrim(base_url(), '/');
?>
<div class="card" style="max-width:440px">
    <h3><?= esc($title2 ?? 'Complete Payment') ?></h3>

    <div id="status-box" class="alert info" style="display:none"></div>

    <div class="amount" style="text-align:center;padding:16px;margin:12px 0;border:1px solid var(--line);border-radius:12px">
        <div style="color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:.5px">Yearly Seller Subscription</div>
        <div style="font-size:30px;font-weight:800;color:#e02735">₹<?= number_format($amountPaise / 100, 0) ?></div>
    </div>

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
                description: 'Seller Yearly Subscription',
                order_id: <?= json_encode($rzpOrderId) ?>,
                prefill: { name: <?= json_encode($pending['name'] ?? '') ?>, contact: <?= json_encode($pending['mobile'] ?? '') ?> },
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
        <form method="post" action="<?= esc($verifyUrl) ?>">
            <input type="hidden" name="razorpay_order_id" value="<?= esc($rzpOrderId) ?>">
            <input type="hidden" name="razorpay_payment_id" value="<?= esc($sandboxPaymentId) ?>">
            <input type="hidden" name="razorpay_signature" value="<?= esc($sandboxSignature) ?>">
            <button class="btn btn-primary" type="submit" style="width:100%;padding:12px">
                Simulate UPI Success — Pay ₹<?= number_format($amountPaise / 100, 2) ?>
            </button>
        </form>
        <p class="note muted" style="font-size:12px;margin-top:10px">Sandbox mode — no real money is charged.</p>
    <?php else: ?>
        <div class="alert error">Payments are temporarily unavailable. Please try again later.</div>
    <?php endif; ?>

    <a class="store-link" href="<?= esc($base) ?>/account/subscribe" style="display:block;text-align:center;margin-top:14px;font-size:13px;color:#9ca3af">← Back to plan</a>
</div>