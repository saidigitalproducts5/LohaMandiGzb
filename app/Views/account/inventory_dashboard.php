<?php
/** Inventory dashboard. */
$base = rtrim(base_url(), '/');
?>
<div class="toolbar">
    <div>
        <h3>Stock at a glance</h3>
        <p class="muted">Product-level aggregates across your warehouses. Low-stock lines are highlighted.</p>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/inventory/stock">View Stock Ledger</a>
    <a class="btn" href="<?= esc($base) ?>/account/inventory/warehouses">Warehouses</a>
    <a class="btn" href="<?= esc($base) ?>/account/inventory/transfers">Transfers</a>
</div>

<div class="grid">
    <div class="stat"><b><?= (int)$productCount ?></b><span>Products Tracked</span></div>
    <div class="stat"><b><?= number_format($totalQty, 0) ?></b><span>Total Units</span></div>
    <div class="stat"><b><?= (int)$warehouseCount ?></b><span>Warehouses</span></div>
    <div class="stat"><b><?= (int)$pendingTfr ?></b><span>Pending Transfers</span></div>
</div>

<div class="card">
    <h3>Low Stock Alert</h3>
    <?php if (empty($lowStock)): ?>
        <p class="empty">No low-stock items. Keep thresholds set to get alerts.</p>
    <?php else: ?>
        <table>
            <tr><th>Product</th><th>Warehouse</th><th>Batch</th><th>Qty</th><th>Threshold</th><th>Barcode</th></tr>
            <?php foreach ($lowStock as $r): ?>
                <tr>
                    <td><?= esc($r['product_name']) ?></td>
                    <td><?= esc($r['warehouse_name'] ?? 'Unassigned') ?></td>
                    <td class="mono"><?= esc($r['batch_no'] ?? '-') ?></td>
                    <td><span class="badge red"><?= number_format((float)$r['stock_qty'], 2) ?></span></td>
                    <td><?= number_format((float)$r['low_stock_threshold'], 2) ?></td>
                    <td class="mono"><?= esc($r['barcode'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Recent Stock Transfers</h3>
    <table>
        <tr><th>Product</th><th>Route</th><th>Qty</th><th>Status</th><th>Date</th></tr>
        <?php if (empty($recentTransfers)): ?>
            <tr><td colspan="5" class="empty">No transfers yet.</td></tr>
        <?php else: ?>
            <?php foreach ($recentTransfers as $t): ?>
                <tr>
                    <td><?= esc($t['product_name']) ?></td>
                    <td><?= esc($t['fw_name'] ?? 'External') ?> → <?= esc($t['tw_name']) ?></td>
                    <td><?= number_format((float)$t['quantity'], 2) ?></td>
                    <td><span class="badge <?= in_array($t['status'], ['arrived']) ? 'green' : (in_array($t['status'], ['cancelled']) ? 'red' : 'amber') ?>"><?= ucfirst(str_replace('_', ' ', $t['status'])) ?></span></td>
                    <td class="muted"><?= esc(date('d M Y', strtotime($t['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>