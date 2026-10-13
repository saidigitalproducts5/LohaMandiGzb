<?php
/** Seller subscription payment — live Razorpay checkout OR sandbox simulator. */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment — LOHA MANDI</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;background:linear-gradient(135deg,#1a1d23,#2a2f3a);min-height:100vh;display:flex;align-items:center;justify-content:center;color:#1a1d23;font-size:14px;padding:30px 0}
        .box{background:#fff;border-radius:16px;width:100%;max-width:440px;padding:34px 36px;box-shadow:0 24px 60px rgba(0,0,0,.35)}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px;margin-bottom:4px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:#e02735;display:inline-block}
        .sub{color:#6b7280;font-size:13px;margin-bottom:20px}
        .amount{text-align:center;padding:18px;margin-bottom:18px;border:1px solid #e5e7eb;border-radius:12px}
        .amount .label{color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:.5px}
        .amount .value{font-size:32px;font-weight:800;color:#e02735}
        .btn{width:100%;padding:13px;background:#e02735;color:#fff;border:0;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer}
        .btn:hover{background:#b81f2b}
        .note{font-size:12px;color:#6b7280;margin-top:12px;text-align:center}
        .back{display:block;text-align:center;margin-top:14px;font-size:13px;color:#9ca3af}
        .back:hover{color:#e02735}
        .alert{padding:11px 14px;border-radius:8px;margin-bottom:18px;font-weight:600;font-size:13.5px}
        .alert.info{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
        .alert.error{background:#feefef;color:#b91c1c;border:1px solid #fecaca}
    </style>
</head>
<body>
<div class="box">
    <div class="brand"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA MANDI" style="height:52px;width:auto"></div>
    <div class="sub"><?= esc($title2 ?? 'Complete Payment') ?></div>

    <div id="status-box" class="alert <?= $isLive ? 'info' : 'info' ?>" style="display:none"></div>

    <div class="amount">
        <div style="color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:.5px">Yearly Seller Subscription</div>
        <div class="value">₹<?= number_format($amountPaise / 100, 0) ?></div>
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
                prefill: { name: <?= json_encode($pending['name']) ?>, contact: <?= json_encode($pending['mobile']) ?> },
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
            <button class="btn" type="submit">Simulate UPI Success — Pay ₹<?= number_format($amountPaise / 100, 2) ?></button>
        </form>
        <p class="note">Sandbox mode — no real money is charged. Your account is created only on payment success.</p>
    <?php else: ?>
        <div class="alert error">Payments are temporarily unavailable. Please try again later.</div>
    <?php endif; ?>

    <a class="back" href="<?= esc($base) ?>/account/register/seller/checkout">← Back to plan</a>
</div>
</body>
</html>
