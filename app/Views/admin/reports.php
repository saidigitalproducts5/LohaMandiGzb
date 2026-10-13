<?php $base = rtrim(base_url('admin'), '/'); $tab = $tab ?? 'revenue';
$tabs = ['revenue' => 'Revenue', 'monthly' => 'Monthly Growth', 'leads' => 'Leads', 'sellers' => 'Sellers', 'categories' => 'Categories', 'gst' => 'GST'];
?>

<div class="card" style="padding:12px">
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <?php foreach ($tabs as $k => $v): ?>
            <a class="btn <?= $tab === $k ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/reports?tab=<?= $k ?>"><?= $v ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($tab === 'revenue'): ?>
    <div class="grid">
        <div class="stat"><b>₹<?= number_format($revenue['total'], 2) ?></b><span>Total Revenue</span></div>
        <div class="stat"><b><?= number_format($revenue['orders']) ?></b><span>Orders</span></div>
    </div>
    <div class="card">
        <h3>By Payment Method</h3>
        <table>
            <thead><tr><th>Method</th><th>Count</th><th>Amount</th></tr></thead>
            <tbody>
            <?php if (empty($revenue['by_method'])): ?><tr><td colspan="3" class="empty">No successful payments yet (Phase 2).</td></tr>
            <?php else: foreach ($revenue['by_method'] as $m): ?>
                <tr><td><span class="pill"><?= esc($m['method']) ?></span></td><td><?= (int)$m['cnt'] ?></td><td><b>₹<?= number_format((float)$m['total'], 2) ?></b></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($tab === 'monthly'): ?>
    <div class="card">
        <h3>Monthly Registrations & RFQs (last 12 months)</h3>
        <table>
            <thead><tr><th>Month</th><th>Users</th><th>Sellers</th><th>Buyers</th><th>RFQs</th></tr></thead>
            <tbody>
            <?php if (empty($monthly)): ?><tr><td colspan="5" class="empty">No data yet.</td></tr>
            <?php else: foreach ($monthly as $m): ?>
                <tr><td><b><?= esc($m['month']) ?></b></td><td><?= (int)$m['users'] ?></td><td><?= (int)$m['sellers'] ?></td><td><?= (int)$m['buyers'] ?></td><td><?= (int)$m['rfqs'] ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if ($tab === 'leads'): ?>
    <div class="grid">
        <div class="stat"><b><?= number_format($leadReport['total']) ?></b><span>Total Leads</span></div>
    </div>
    <div class="card">
        <h3>Leads by Status</h3>
        <table><thead><tr><th>Status</th><th>Count</th></tr></thead><tbody>
        <?php foreach ($leadReport['by_status'] as $r): ?><tr><td><?= esc($r['status']) ?></td><td><?= (int)$r['cnt'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
    <div class="card">
        <h3>Leads by Type</h3>
        <table><thead><tr><th>Type</th><th>Count</th></tr></thead><tbody>
        <?php foreach ($leadReport['by_type'] as $r): ?><tr><td><?= esc($r['lead_type']) ?></td><td><?= (int)$r['cnt'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
<?php endif; ?>

<?php if ($tab === 'sellers'): ?>
    <div class="grid"><div class="stat"><b><?= number_format($sellerReport['total']) ?></b><span>Total Sellers</span></div></div>
    <div class="card">
        <h3>Top Cities</h3>
        <table><thead><tr><th>City</th><th>Sellers</th></tr></thead><tbody>
        <?php foreach ($sellerReport['by_city'] as $r): ?><tr><td><?= esc($r['city']) ?></td><td><?= (int)$r['cnt'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
    <div class="card">
        <h3>By State</h3>
        <table><thead><tr><th>State</th><th>Sellers</th></tr></thead><tbody>
        <?php foreach ($sellerReport['by_state'] as $r): ?><tr><td><?= esc($r['state']) ?></td><td><?= (int)$r['cnt'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
<?php endif; ?>

<?php if ($tab === 'categories'): ?>
    <div class="card">
        <h3>Products per Category</h3>
        <table><thead><tr><th>Category</th><th>Products</th></tr></thead><tbody>
        <?php if (empty($categoryReport)): ?><tr><td colspan="2" class="empty">No products yet.</td></tr>
        <?php else: foreach ($categoryReport as $r): ?><tr><td><?= esc($r['category'] ?? 'Uncategorized') ?></td><td><?= (int)$r['products'] ?></td></tr><?php endforeach; endif; ?>
        </tbody></table>
    </div>
<?php endif; ?>

<?php if ($tab === 'gst'): ?>
    <div class="grid">
        <div class="stat"><b><?= number_format($gstReport['verified']) ?></b><span>GST Verified</span></div>
        <div class="stat"><b><?= number_format($gstReport['with_gstin']) ?></b><span>Have GSTIN</span></div>
        <div class="stat"><b><?= number_format($gstReport['unverified']) ?></b><span>Not Verified</span></div>
    </div>
    <div class="card">
        <h3>Products by GST Rate</h3>
        <table><thead><tr><th>GST Rate</th><th>Products</th></tr></thead><tbody>
        <?php foreach ($gstReport['gst_rates'] as $r): ?><tr><td><?= (float)$r['gst_rate'] ?>%</td><td><?= (int)$r['products'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
<?php endif; ?>