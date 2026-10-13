<?php
/** Stock ledger — products aggregated across warehouses. */
$base = rtrim(base_url(), '/');
?>
<div class="toolbar">
    <div>
        <h3>Stock Levels</h3>
        <p class="muted"><?= count($rows) ?> product(s) tracked across <?= count($warehouses) ?> warehouse(s).</p>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/inventory/stock/new">+ Add Stock</a>
</div>

<form method="get" action="<?= esc($base) ?>/account/inventory/stock" style="margin-bottom:16px">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search product, batch or barcode…">
    <select name="warehouse_id">
        <option value="0">All warehouses</option>
        <?php foreach ($warehouses as $w): ?>
            <option value="<?= (int)$w['id'] ?>" <?= $warehouseId === (int)$w['id'] ? 'selected' : '' ?>><?= esc($w['name']) ?><?= !empty($w['city']) ? ' (' . esc($w['city']) . ')' : '' ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filter</button>
    <a class="btn" href="<?= esc($base) ?>/account/inventory/stock">Reset</a>
</form>

<?php if (empty($rows)): ?>
    <div class="card"><p class="empty">No stock records. <a class="store-link" href="<?= esc($base) ?>/account/inventory/stock/new">Add your first stock record →</a></p></div>
<?php else: ?>
    <div class="card">
        <table>
            <tr><th>Product</th><th>Total</th><th>Branches</th><th>Barcode</th><th></th></tr>
            <?php foreach ($rows as $p): ?>
                <tr>
                    <td>
                        <b><?= esc($p['product_name']) ?></b>
                        <?php if ($p['low']): ?><span class="badge red">Low</span><?php endif; ?>
                    </td>
                    <td><b><?= number_format($p['total'], 2) ?> <?= esc($p['unit']) ?></b></td>
                    <td>
                        <?php foreach ($p['warehouses'] as $w): ?>
                            <div class="muted" style="margin-bottom:4px">
                                <?= esc($w['warehouse_name']) ?><?= $w['city'] ? ' · ' . esc($w['city']) : '' ?>
                                — <b><?= number_format($w['stock_qty'], 2) ?></b>
                                <?php if ($w['low_stock_threshold'] > 0): ?>
                                    <span class="pill">thr <?= number_format($w['low_stock_threshold'], 0) ?></span>
                                <?php endif; ?>
                                <?php if ($w['batch_no']): ?><span class="pill mono"><?= esc($w['batch_no']) ?></span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach ($p['warehouses'] as $w): ?>
                            <?php if ($w['barcode']): ?><span class="mono pill"><?= esc($w['barcode']) ?></span><?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach ($p['warehouses'] as $w): ?>
                            <a class="btn btn-sm btn-outline" style="margin:2px 2px 2px 0" href="<?= esc($base) ?>/account/inventory/stock/<?= (int)$w['id'] ?>/edit">Edit</a>
                            <a class="btn btn-sm btn-danger" style="margin:2px 2px 2px 0" href="<?= esc($base) ?>/account/inventory/stock/<?= (int)$w['id'] ?>/delete" onclick="return confirm('Delete this stock record? Product stock will be recalculated.')">✕</a>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>