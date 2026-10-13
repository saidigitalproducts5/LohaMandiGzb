<?php
/** New stock transfer form. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3>New Stock Transfer</h3>
    <form method="post" action="<?= esc($base) ?>/account/inventory/transfers/new">
        <div class="row">
            <div><label>Product *</label>
                <select name="product_id" required>
                    <option value="">— Select product —</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= (int)$p['id'] ?>"><?= esc($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Quantity *</label><input type="number" name="quantity" step="0.001" required></div>
        </div>
        <div class="row">
            <div><label>From Warehouse *</label>
                <select name="from_warehouse_id" required>
                    <option value="">— Select source —</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= (int)$w['id'] ?>"><?= esc($w['name']) ?><?= !empty($w['city']) ? ' (' . esc($w['city']) . ')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>To Warehouse *</label>
                <select name="to_warehouse_id" required>
                    <option value="">— Select destination —</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= (int)$w['id'] ?>"><?= esc($w['name']) ?><?= !empty($w['city']) ? ' (' . esc($w['city']) . ')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div><label>Note</label><input type="text" name="note" placeholder="e.g. restock main yard"></div>
        </div>
        <button class="btn btn-primary" type="submit">Create Transfer</button>
        <a class="btn" href="<?= esc($base) ?>/account/inventory/transfers">Cancel</a>
    </form>
</div>