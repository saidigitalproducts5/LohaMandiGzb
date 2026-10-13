<?php
/** Seller dashboard view. */
$base = rtrim(base_url(), '/');
$s = $seller;
$st = $stats;
?>
<div class="grid">
    <div class="stat"><b><?= (int)$st['products'] ?></b><span>Total Products</span></div>
    <div class="stat"><b><?= (int)$st['pending'] ?></b><span>Pending Approval</span></div>
    <div class="stat"><b><?= (int)$st['active'] ?></b><span>Live Products</span></div>
    <div class="stat"><b><?= (int)$st['rfqs'] ?></b><span>Matching RFQs</span></div>
    <div class="stat"><b><?= (int)$st['quotes'] ?></b><span>Quotations Sent</span></div>
    <div class="stat"><b><?= (int)$st['leads'] ?></b><span>New Leads</span></div>
</div>

<?php if ($s && $s['status'] === 'pending'): ?>
    <div class="alert info">Your business profile is awaiting admin approval. You can add products now — they will go live after approval.</div>
<?php elseif ($s && $s['status'] !== 'active'): ?>
    <div class="alert error">Your seller profile is <?= esc($s['status']) ?>. Please contact support to reactivate.</div>
<?php endif; ?>

<?php if ($s && !empty($s['slug'])): ?>
    <div class="toolbar" style="justify-content:flex-end">
        <a class="btn btn-outline" href="<?= esc($base) ?>/seller/<?= esc($s['slug']) ?>" target="_blank">View My Storefront ↗</a>
    </div>
<?php endif; ?>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>Latest Buyer RFQs</h3>
        <table>
            <thead><tr><th>RFQ</th><th>City</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($recentRfqs)): ?>
                <tr><td colspan="3" class="empty">No matching RFQs yet.</td></tr>
            <?php else: ?>
                <?php foreach ($recentRfqs as $r): ?>
                    <tr>
                        <td>
                            <b><?= esc($r['product_name']) ?></b>
                            <br><span class="muted mono">Qty <?= rtrim(rtrim(number_format((float)$r['quantity'], 3), '0'), '.') ?> <?= esc($r['unit']) ?></span>
                        </td>
                        <td class="muted"><?= esc($r['city'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($r['viewed_at'])): ?>
                                <span class="badge green">Viewed</span>
                            <?php else: ?>
                                <span class="badge amber">New</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <a class="store-link" href="<?= esc($base) ?>/account/rfqs">View all buyer RFQs →</a>
    </div>

    <div class="card">
        <h3>Recent Quotations</h3>
        <table>
            <thead><tr><th>Quote</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($recentQuotes)): ?>
                <tr><td colspan="3" class="empty">No quotations sent yet.</td></tr>
            <?php else: ?>
                <?php foreach ($recentQuotes as $q): ?>
                    <tr>
                        <td class="mono"><?= esc($q['quote_number']) ?><br><span class="muted"><?= esc($q['product_name']) ?></span></td>
                        <td>₹<?= number_format((float)$q['total_amount'], 2) ?></td>
                        <td>
                            <span class="badge <?= $q['status'] === 'accepted' ? 'green' : ($q['status'] === 'rejected' ? 'red' : 'blue') ?>"><?= esc(ucfirst($q['status'])) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <a class="store-link" href="<?= esc($base) ?>/account/quotations">View all quotations →</a>
    </div>
</div>

<div class="card">
    <h3>Latest Leads</h3>
    <table>
        <thead><tr><th>Buyer</th><th>Mobile</th><th>Product</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($recentLeads)): ?>
            <tr><td colspan="4" class="empty">No leads yet. Leads appear when buyers contact you.</td></tr>
        <?php else: ?>
            <?php foreach ($recentLeads as $l): ?>
                <tr>
                    <td><b><?= esc($l['name']) ?></b><?= $l['company'] ? '<br><span class="muted">' . esc($l['company']) . '</span>' : '' ?></td>
                    <td class="mono"><?= esc($l['mobile']) ?></td>
                    <td class="muted"><?= esc($l['product_id'] ? ('#Product ' . $l['product_id']) : ($l['rfq_id'] ? ('#RFQ ' . $l['rfq_id']) : '-')) ?></td>
                    <td><span class="badge <?= $l['status'] === 'new' ? 'blue' : 'gray' ?>"><?= esc(ucfirst($l['status'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <a class="store-link" href="<?= esc($base) ?>/account/leads">Manage all leads →</a>
</div>
