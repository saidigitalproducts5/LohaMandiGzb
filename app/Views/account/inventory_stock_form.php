<?php
/** Stock record form (create/edit). */
$base = rtrim(base_url(), '/');
$isEdit = ($row !== null);
?>
<div class="card">
    <h3><?= $isEdit ? 'Edit Stock Record' : 'Add Stock Record' ?></h3>
    <form method="post" action="<?= $isEdit ? esc($base) . '/account/inventory/stock/' . (int)$row['id'] . '/edit' : esc($base) . '/account/inventory/stock/new' ?>">
        <?php if (!empty($product) && is_array($product) && isset($product['name'])): ?>
            <div class="row"><div><label>Product</label><input type="text" value="<?= esc($product['name']) ?>" disabled></div></div>
        <?php endif; ?>

        <?php if (!$isEdit): ?>
            <div class="row">
                <div><label>Product *</label>
                    <select name="product_id" required>
                        <option value="">— Select product —</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= (int)$p['id'] ?>"><?= esc($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div><label>Warehouse *</label>
                <select name="warehouse_id" required>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= (int)$w['id'] ?>" <?= $isEdit && (int)$row['warehouse_id'] === (int)$w['id'] ? 'selected' : '' ?>>
                            <?= esc($w['name']) ?><?= !empty($w['city']) ? ' (' . esc($w['city']) . ')' : '' ?><?= $w['is_default'] ? ' — default' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($isEdit): ?>
                <div><label>Barcode</label><input type="text" value="<?= esc($row['barcode'] ?? '') ?>" disabled class="mono"></div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div><label>Quantity *</label><input type="number" name="stock_qty" step="0.001" value="<?= esc($row['stock_qty'] ?? 0) ?>" required></div>
            <div><label>Low Stock Threshold</label><input type="number" name="low_stock_threshold" step="0.001" value="<?= esc($row['low_stock_threshold'] ?? 0) ?>"></div>
            <div><label>Batch No</label><input type="text" name="batch_no" value="<?= esc($row['batch_no'] ?? '') ?>"></div>
        </div>

        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Add Stock' ?></button>
        <a class="btn" href="<?= esc($base) ?>/account/inventory/stock">Cancel</a>
    </form>
</div>