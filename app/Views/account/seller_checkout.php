<?php
/* * Seller subscription checkout — ₹9,999/year before account creation. */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Subscription — LOHA Mandi</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;background:linear-gradient(135deg,#1a1d23,#2a2f3a);min-height:100vh;display:flex;align-items:center;justify-content:center;color:#1a1d23;font-size:14px;padding:30px 0}
        .box{background:#fff;border-radius:16px;width:100%;max-width:480px;padding:34px 36px;box-shadow:0 24px 60px rgba(0,0,0,.35)}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px;margin-bottom:4px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:#e02735;display:inline-block}
        .sub{color:#6b7280;font-size:13px;margin-bottom:20px}
        .plan{border:2px solid #e02735;border-radius:12px;padding:20px;margin-bottom:16px;background:#fff5f5}
        .plan .name{font-weight:800;font-size:18px}
        .plan .price{font-size:30px;font-weight:800;color:#e02735;margin-top:6px}
        .plan .price small{font-size:15px;color:#6b7280;font-weight:600}
        .features{list-style:none;margin-top:12px}
        .features li{padding:5px 0;color:#374151}
        .acc{background:#f3f4f6;border-radius:10px;padding:14px;margin-bottom:16px;font-size:13px}
        .acc b{display:block;margin-bottom:4px;color:#1f2937}
        .acc .row{display:flex;justify-content:space-between;padding:3px 0}
        .btn{width:100%;padding:13px;background:#e02735;color:#fff;border:0;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer}
        .btn:hover{background:#b81f2b}
        .note{font-size:12px;color:#6b7280;margin-top:12px;line-height:1.5}
        .back{display:block;text-align:center;margin-top:14px;font-size:13px;color:#9ca3af}
        .back:hover{color:#e02735}
        .alert{padding:11px 14px;border-radius:8px;margin-bottom:18px;font-weight:600;font-size:13.5px}
        .alert.error{background:#feefef;color:#b91c1c;border:1px solid #fecaca}
    </style>
</head>
<body>
<div class="box">
    <div class="brand"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:52px;width:auto"></div>
    <div class="sub">Activate your seller account</div>

    <?php foreach (['success','error','info'] as $k):
        $msg = session()->getFlashdata('flash_' . $k); ?>
        <?php if ($msg): ?><div class="alert <?= $k ?>"><?= esc($msg) ?></div><?php endif; ?>
    <?php endforeach; ?>

    <div class="plan">
        <div class="name"><?= esc($plan['name']) ?></div>
        <div class="price">₹<?= number_format((float)$plan['price'], 0) ?> <small>/ year</small></div>
        <ul class="features">
            <li>✓ Business listing on the marketplace</li>
            <li>✓ Manage products &amp; receive RFQs</li>
            <li>✓ Verified seller badge subject to approval</li>
            <li>✓ 365-day subscription (auto from today)</li>
        </ul>
    </div>

    <div class="acc">
        <b>Account details</b>
        <div class="row"><span>Full Name</span><span><?= esc($pending['name']) ?></span></div>
        <div class="row"><span>Mobile</span><span><?= esc($pending['mobile']) ?></span></div>
        <div class="row"><span>Business</span><span><?= esc($pending['business_name']) ?></span></div>
    </div>

    <form method="get" action="<?= esc($base) ?>/account/register/seller/pay">
        <button class="btn" type="submit">Pay ₹<?= number_format((float)$plan['price'], 0) ?> &amp; Register</button>
    </form>

    <p class="note">Your seller account is created <b>only after</b> this payment succeeds. Protected by Razorpay (UPI / cards).</p>
    <a class="back" href="<?= esc($base) ?>/register">← Back to registration</a>
</div>
</body>
</html>
