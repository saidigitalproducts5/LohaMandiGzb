<?php
/** Seller subscription — paywall for existing sellers. */
$base = rtrim(base_url(), '/');
?>
<div class="card" style="max-width:520px">
    <h3>Activate your Seller Subscription</h3>
    <p class="muted" style="margin-bottom:14px">
        Your seller account is currently <b>not subscribed</b>. A paid subscription is required to list products, receive RFQs, and manage inventory. Complete payment below to continue.
    </p>

    <div class="plan-box" style="border:2px solid #e02735;border-radius:12px;padding:18px;margin-bottom:16px;background:#fff5f5">
        <div style="font-weight:800;font-size:17px"><?= esc($plan['name']) ?></div>
        <div style="font-size:28px;font-weight:800;color:#e02735;margin-top:6px">
            ₹<?= number_format((float) $plan['price'], 0) ?> <small style="font-size:14px;color:#6b7280;font-weight:600">/ year</small>
        </div>
        <ul style="margin-top:10px;padding-left:18px;font-size:13px;color:#374151">
            <li>Business listing on the marketplace</li>
            <li>Manage products &amp; receive RFQs</li>
            <li>Inventory, CRM &amp; AI tools</li>
            <li>365-day subscription (auto from today)</li>
        </ul>
    </div>

    <form method="get" action="<?= esc($base) ?>/account/subscribe/pay">
        <button class="btn btn-primary" type="submit" style="width:100%;padding:12px">
            Pay ₹<?= number_format((float) $plan['price'], 0) ?> &amp; Activate
        </button>
    </form>
    <p class="muted" style="font-size:12px;margin-top:10px">
        Protected by Razorpay (UPI / cards). Your account is created only after the payment succeeds.
    </p>
</div>