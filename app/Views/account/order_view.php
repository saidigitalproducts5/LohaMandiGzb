<?php
/** Order detail view — buyer or seller. */
$base = rtrim(base_url(), '/');
$o = $order;
$badgeClass = in_array($o['order_status'], ['delivered']) ? 'green' : ($o['order_status'] === 'cancelled' ? 'red' : 'blue');
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>Order <?= esc($o['order_number']) ?></h3>
        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($o['order_status'])) ?></span>
        <span class="badge <?= $o['payment_status'] === 'paid' ? 'green' : 'amber' ?>">Payment <?= esc($o['payment_status']) ?></span>
        <table style="margin-top:14px">
            <tbody>
                <tr><th style="width:40%"><?= $isSeller ? 'Buyer' : 'Seller' ?></th>
                    <td>
                        <?php if ($isSeller): ?>
                            <b><?= esc($buyer['name'] ?? '') ?></b><br><span class="mono"><?= esc($buyer['mobile'] ?? '') ?></span>
                        <?php else: ?>
                            <b><?= esc($seller['business_name'] ?? '') ?></b><br><span class="muted"><?= esc($seller['city'] ?? '') ?></span>
                            <?php if (!empty($seller['id'])): ?>
                                <a class="btn btn-sm btn-outline" style="margin-top:8px" href="<?= esc($base) ?>/account/chats/open/<?= (int)$seller['id'] ?>">💬 Chat with seller</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td></tr>
                <tr><th>Quote</th><td class="mono"><?= esc($quote['quote_number'] ?? '-') ?></td></tr>
                <?php if ($o['lr_number']): ?><tr><th>LR Number</th><td class="mono"><?= esc($o['lr_number']) ?></td></tr><?php endif; ?>
                <?php if ($o['transport_details']): ?><tr><th>Transport</th><td><?= nl2br(esc($o['transport_details'])) ?></td></tr><?php endif; ?>
                <?php if ($o['shipping_address']): ?><tr><th>Ship To</th><td><?= nl2br(esc($o['shipping_address'])) ?></td></tr><?php endif; ?>
                <tr><th>Placed</th><td class="muted"><?= esc(date('d M Y, h:i A', strtotime($o['created_at']))) ?></td></tr>
            </tbody>
        </table>
    </div>

    <div>
        <div class="card">
            <h3>Items</h3>
            <table>
                <thead><tr><th>Product</th><th>Qty</th><th>Rate</th><th>Amount</th></tr></thead>
                <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><b><?= esc($it['product_name']) ?></b></td>
                        <td><?= rtrim(rtrim(number_format((float)$it['quantity'], 3), '0'), '.') ?> <?= esc($it['unit']) ?></td>
                        <td>₹<?= number_format((float)$it['unit_price'], 2) ?></td>
                        <td>₹<?= number_format((float)$it['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:14px;border-top:1px solid var(--line);padding-top:12px">
                <div style="display:flex;justify-content:space-between"><span class="muted">Subtotal</span><b>₹<?= number_format((float)$o['subtotal'], 2) ?></b></div>
                <div style="display:flex;justify-content:space-between"><span class="muted">GST</span><b>₹<?= number_format((float)$o['gst_amount'], 2) ?></b></div>
                <?php if ((float)$o['discount'] > 0): ?><div style="display:flex;justify-content:space-between"><span class="muted">Discount</span><b>-₹<?= number_format((float)$o['discount'], 2) ?></b></div><?php endif; ?>
                <div style="display:flex;justify-content:space-between"><span class="muted">Shipping &amp; Other</span><b>₹<?= number_format((float)$o['shipping'], 2) ?></b></div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid var(--line);padding-top:10px;margin-top:10px"><b>Total</b><b>₹<?= number_format((float)$o['total'], 2) ?></b></div>
            </div>
        </div>

        <?php if (!$isSeller && $o['payment_status'] !== 'paid' && $o['order_status'] !== 'cancelled'): ?>
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <div class="muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.4px">Amount Due</div>
                        <b style="font-size:24px">₹<?= number_format((float)$o['total'], 2) ?></b>
                    </div>
                    <a class="btn btn-primary" style="padding:11px 18px" href="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>/pay">Pay Now</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$isSeller && $o['order_status'] === 'delivered'): ?>
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <div class="muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.4px">Order Delivered</div>
                        <b style="font-size:16px">Share your feedback</b>
                    </div>
                    <a class="btn btn-primary" href="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>/review">Rate &amp; Review</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isSeller): ?>
            <div class="card">
                <h3>Update Order</h3>
                <form method="post" action="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>/status">
                    <label>Order Status</label>
                    <select name="order_status">
                        <?php $statuses = ['confirmed', 'dispatched', 'in_transit', 'delivered', 'cancelled']; ?>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $o['order_status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div style="margin:14px 0">
                        <label>LR Number</label>
                        <input type="text" name="lr_number" value="<?= esc($o['lr_number'] ?? '') ?>" placeholder="LR / docket no.">
                    </div>
                    <div style="margin-bottom:14px">
                        <label>Transport Details</label>
                        <textarea name="transport_details" rows="3" placeholder="Transporter, vehicle no., driver contact…"><?= esc($o['transport_details'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Update Status</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>