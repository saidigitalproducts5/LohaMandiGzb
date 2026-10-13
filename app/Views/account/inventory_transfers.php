<?php
/** Stock transfers list. */
$base = rtrim(base_url(), '/');
?>
<div class="toolbar">
    <div>
        <h3>Stock Transfers</h3>
        <p class="muted">Move stock between your warehouses. Source qty is reserved at creation.</p>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/inventory/transfers/new">+ New Transfer</a>
</div>

<form method="get" action="<?= esc($base) ?>/account/inventory/transfers" style="margin-bottom:16px">
    <select name="status">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $s): ?>
            <option value="<?= esc($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filter</button>
    <a class="btn" href="<?= esc($base) ?>/account/inventory/transfers">Reset</a>
</form>

<?php if (empty($transfers)): ?>
    <div class="card"><p class="empty">No transfers yet. Create one to move stock between warehouses.</p></div>
<?php else: ?>
    <div class="card">
        <table>
            <tr><th>#</th><th>Product</th><th>Route</th><th>Qty</th><th>Status</th><th>Date</th><th></th></tr>
            <?php foreach ($transfers as $t): ?>
                <tr>
                    <td class="mono">#<?= (int)$t['id'] ?></td>
                    <td><?= esc($t['product_name']) ?></td>
                    <td><?= esc($t['fw_name'] ?? 'External') ?> → <?= esc($t['tw_name']) ?></td>
                    <td><b><?= number_format((float)$t['quantity'], 2) ?></b></td>
                    <td><span class="badge <?= $t['status'] === 'arrived' ? 'green' : ($t['status'] === 'cancelled' ? 'gray' : ($t['status'] === 'in_transit' ? 'blue' : 'amber')) ?>"><?= ucfirst(str_replace('_', ' ', $t['status'])) ?></span></td>
                    <td class="muted"><?= esc(date('d M Y', strtotime($t['created_at']))) ?></td>
                    <td>
                        <?php if (!in_array($t['status'], ['arrived', 'cancelled'])): ?>
                            <form method="post" action="<?= esc($base) ?>/account/inventory/transfers/<?= (int)$t['id'] ?>/status" style="display:flex;gap:4px">
                                <select name="status">
                                    <option value="in_transit" <?= $t['status'] === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                                    <option value="arrived">Arrived</option>
                                    <option value="cancelled">Cancel</option>
                                </select>
                                <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>