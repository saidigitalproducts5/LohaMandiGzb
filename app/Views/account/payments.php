<?php
/** Buyer payment history. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <div class="toolbar">
        <h3 style="margin:0">Payment History</h3>
    </div>
    <?php if (empty($rows)): ?>
        <div class="empty">No payments yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Order</th><th>Seller</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $p): ?>
                <?php $cls = $p['status'] === 'success' ? 'green' : ($p['status'] === 'failed' ? 'red' : 'amber'); ?>
                <tr>
                    <td class="mono"><a href="<?= esc($base) ?>/account/orders/<?= (int)$p['order_id'] ?>" style="color:var(--brand)"><?= esc($p['order_number'] ?? '-') ?></a></td>
                    <td><?= esc($p['business_name'] ?? '-') ?></td>
                    <td>₹<?= number_format((float)$p['amount'], 2) ?></td>
                    <td><?= esc(ucwords(str_replace('_', ' ', $p['method']))) ?></td>
                    <td><span class="badge <?= $cls ?>"><?= esc(ucfirst($p['status'])) ?></span></td>
                    <td class="muted"><?= esc(date('d M Y, h:i A', strtotime($p['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>