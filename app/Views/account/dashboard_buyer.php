<?php
/** Buyer dashboard view. */
$base = rtrim(base_url(), '/');
$st = $stats;
?>
<div class="toolbar" style="justify-content:flex-end">
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/rfqs/new">+ Post New RFQ</a>
</div>

<div class="grid">
    <div class="stat"><b><?= (int)$st['rfqs'] ?></b><span>Total RFQs</span></div>
    <div class="stat"><b><?= (int)$st['open'] ?></b><span>Open RFQs</span></div>
    <div class="stat"><b><?= (int)$st['quotes'] ?></b><span>Quotations</span></div>
    <div class="stat"><b><?= (int)$st['accepted'] ?></b><span>Accepted</span></div>
</div>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>My Recent RFQs</h3>
        <table>
            <thead><tr><th>Product</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($recentRfqs)): ?>
                <tr><td colspan="3" class="empty">You haven't posted any RFQs yet.</td></tr>
            <?php else: ?>
                <?php foreach ($recentRfqs as $r): ?>
                    <tr>
                        <td>
                            <b><?= esc($r['product_name']) ?></b>
                            <br><span class="muted mono">Qty <?= rtrim(rtrim(number_format((float)$r['quantity'], 3), '0'), '.') ?> <?= esc($r['unit']) ?></span>
                        </td>
                        <td><span class="badge <?= $r['status'] === 'open' ? 'green' : ($r['status'] === 'cancelled' ? 'red' : 'blue') ?>"><?= esc(ucfirst($r['status'])) ?></span></td>
                        <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/rfqs/<?= (int)$r['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <a class="store-link" href="<?= esc($base) ?>/account/rfqs">View all my RFQs →</a>
    </div>

    <div class="card">
        <h3>Recent Quotations</h3>
        <table>
            <thead><tr><th>Seller</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($recentQuotes)): ?>
                <tr><td colspan="3" class="empty">No quotations received yet. Post an RFQ to get quotes.</td></tr>
            <?php else: ?>
                <?php foreach ($recentQuotes as $q): ?>
                    <tr>
                        <td><b><?= esc($q['business_name'] ?? ('Seller #' . $q['seller_id'])) ?></b><br><span class="muted mono"><?= esc($q['quote_number']) ?></span></td>
                        <td>₹<?= number_format((float)$q['total_amount'], 2) ?></td>
                        <td><span class="badge <?= $q['status'] === 'accepted' ? 'green' : ($q['status'] === 'rejected' ? 'red' : 'blue') ?>"><?= esc(ucfirst($q['status'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <a class="store-link" href="<?= esc($base) ?>/account/quotations">View all quotations →</a>
    </div>
</div>