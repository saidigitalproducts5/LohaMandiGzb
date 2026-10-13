<?php
/** Orders list — buyer or seller view. */
$base = rtrim(base_url(), '/');
$isSeller = isset($isSeller) ? $isSeller : false;
?>
<div class="grid">
    <div class="stat"><b><?= (int)$counts['total'] ?></b><span>Total</span></div>
    <div class="stat"><b><?= (int)$counts['confirmed'] ?></b><span>Confirmed</span></div>
    <div class="stat"><b><?= (int)$counts['dispatched'] + (int)$counts['in_transit'] ?></b><span>In Transit</span></div>
    <div class="stat"><b><?= (int)$counts['delivered'] ?></b><span>Delivered</span></div>
    <div class="stat"><b><?= (int)$counts['cancelled'] ?></b><span>Cancelled</span></div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th><?= $isSeller ? 'Buyer' : 'Seller' ?></th>
                <th>Quote</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr>
                <td colspan="7" class="empty">
                    <?= $isSeller ? 'No orders yet. Orders are created when a buyer accepts your quotation.' : 'No orders yet. Accept a quotation to place an order.' ?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $o): ?>
                <tr>
                    <td class="mono"><?= esc($o['order_number']) ?><br><span class="muted"><?= esc(date('d M Y', strtotime($o['created_at']))) ?></span></td>
                    <td>
                        <?php if ($isSeller): ?>
                            <b><?= esc($o['buyer_name'] ?? '#') ?></b><br>
                            <span class="mono"><?= esc($o['buyer_mobile'] ?? '') ?></span>
                        <?php else: ?>
                            <b><?= esc($o['business_name'] ?? 'Seller #' . $o['seller_id']) ?></b>
                        <?php endif; ?>
                    </td>
                    <td class="mono muted"><?= esc($o['quote_number'] ?? '-') ?></td>
                    <td><b>₹<?= number_format((float)$o['total'], 2) ?></b></td>
                    <td><span class="badge <?= $o['payment_status'] === 'paid' ? 'green' : 'amber' ?>"><?= esc(ucfirst($o['payment_status'])) ?></span></td>
                    <td>
                        <span class="badge <?= in_array($o['order_status'], ['delivered']) ? 'green' : ($o['order_status'] === 'cancelled' ? 'red' : 'blue') ?>"><?= esc(ucfirst($o['order_status'])) ?></span>
                    </td>
                    <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>