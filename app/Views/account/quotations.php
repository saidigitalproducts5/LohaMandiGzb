<?php
/** Quotations list — seller sent or buyer received. */
$base = rtrim(base_url(), '/');
$isSeller = isset($isSeller) ? $isSeller : false;
?>
<div class="card">
    <table>
        <thead>
            <tr>
                <th>Quote #</th>
                <th>Product</th>
                <th><?= $isSeller ? 'Buyer' : 'Seller' ?></th>
                <th>Amount</th>
                <th>Status</th>
                <th>Valid Till</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr>
                <td colspan="6" class="empty">
                    <?= $isSeller ? 'You haven\'t sent any quotations yet. Reply to a buyer RFQ under "Buyer RFQs".' : 'No quotations received yet. Post an RFQ to get quotes from sellers.' ?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $q): ?>
                <tr>
                    <td class="mono"><?= esc($q['quote_number']) ?></td>
                    <td><b><?= esc($q['rfq_product'] ?? $q['product_name']) ?></b><br><span class="muted"><?= rtrim(rtrim(number_format((float)$q['quantity'], 3), '0'), '.') ?> <?= esc($q['unit']) ?></span></td>
                    <td class="muted"><?= esc($q['business_name'] ?? ('RFQ #' . $q['rfq_id'])) ?></td>
                    <td><b>₹<?= number_format((float)$q['total_amount'], 2) ?></b></td>
                    <td><span class="badge <?= $q['status'] === 'accepted' ? 'green' : ($q['status'] === 'rejected' || $q['status'] === 'expired' ? 'red' : 'blue') ?>"><?= esc(ucfirst($q['status'])) ?></span></td>
                    <td class="muted"><?= esc($q['valid_until'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>