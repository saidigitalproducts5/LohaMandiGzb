<?php
/** Payment selection — buyer picks a method to pay for an order. */
$base = rtrim(base_url(), '/');
$o = $order;
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>Order <?= esc($o['order_number']) ?></h3>
        <span class="badge <?= $o['payment_status'] === 'paid' ? 'green' : 'amber' ?>">Payment <?= esc($o['payment_status']) ?></span>
        <table style="margin-top:14px">
            <tbody>
                <tr><th style="width:40%">Seller</th><td><b><?= esc($seller['business_name'] ?? '') ?></b></td></tr>
                <?php if ($o['shipping_address']): ?><tr><th>Ship To</th><td><?= nl2br(esc($o['shipping_address'])) ?></td></tr><?php endif; ?>
                <tr><th>Placed</th><td class="muted"><?= esc(date('d M Y, h:i A', strtotime($o['created_at']))) ?></td></tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Pay Online</h3>
        <div style="display:flex;justify-content:space-between;font-size:15px">
            <span class="muted">Amount Due</span>
            <b style="font-size:22px">₹<?= number_format((float)$o['total'], 2) ?></b>
        </div>

        <?php if ($existing && $existing['status'] === 'pending'): ?>
            <div class="alert info" style="margin-top:14px">You have a pending payment. Complete it or start a fresh one.</div>
        <?php endif; ?>

        <form method="post" action="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>/pay" style="margin-top:16px">
            <label>Payment Method</label>
            <select name="method">
                <option value="upi">UPI — GPay / PhonePe / Paytm</option>
                <option value="net_banking">Net Banking</option>
                <option value="debit_card">Debit Card</option>
                <option value="credit_card">Credit Card</option>
                <option value="wallet">Wallet</option>
                <option value="razorpay">Razorpay (all options)</option>
            </select>
            <p class="muted" style="margin:12px 0;font-size:12.5px">
                Payments are processed securely via Razorpay (UPI, cards &amp; net banking supported).
            </p>
            <button class="btn btn-primary" type="submit" style="width:100%;padding:12px">Proceed to Pay ₹<?= number_format((float)$o['total'], 2) ?></button>
        </form>

        <a class="store-link" href="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>">← Back to order</a>
    </div>
</div>