<?php
/** Seller product list view. */
$base = rtrim(base_url(), '/');
$f = $filters;
?>
<div class="toolbar">
    <form method="get" action="<?= esc($base) ?>/account/products">
        <input type="text" name="q" value="<?= esc($f['q']) ?>" placeholder="Search your products…">
        <select name="status">
            <option value="all" <?= $f['status'] === 'all' ? 'selected' : '' ?>>All statuses</option>
            <option value="pending" <?= $f['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $f['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $f['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
        <a class="btn" href="<?= esc($base) ?>/account/products">Reset</a>
    </form>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/products/new">+ Add Product</a>
</div>

<div class="card">
    <div class="toolbar" style="margin:0 0 6px">
        <span class="muted"><?= (int)$total ?> product(s)</span>
    </div>
    <table>
        <thead>
            <tr><th>ID</th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="7" class="empty">No products found. <a class="store-link" href="<?= esc($base) ?>/account/products/new">Add your first product →</a></td></tr>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <tr style="cursor:pointer" title="View details" onclick="location.href='<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>'">
                    <td class="mono">#<?= (int)$p['id'] ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <?php if (!empty($p['primary_image'])): ?>
                                <img src="<?= esc(base_url('uploads/' . $p['primary_image'])) ?>" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid var(--line);flex:none;background:#f8fafc">
                            <?php else: ?>
                                <span style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px dashed var(--line);flex:none;background:#f8fafc;display:flex;align-items:center;justify-content:center;font-size:18px">📦</span>
                            <?php endif; ?>
                            <div>
                                <b><?= esc($p['name']) ?></b>
                                <?php if (!empty($p['is_featured'])): ?><span class="pill">★ Featured</span><?php endif; ?>
                                <br><span class="muted mono"><?= esc($p['hsn_code'] ?? '') ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="muted"><?= esc($p['category_name'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($p['offer_price'])): ?>
                            <b>₹<?= number_format((float)$p['offer_price']) ?></b>
                            <span class="muted">/<?= esc($p['unit']) ?></span>
                        <?php elseif (!empty($p['price'])): ?>
                            ₹<?= number_format((float)$p['price']) ?> <span class="muted">/<?= esc($p['unit']) ?></span>
                        <?php else: ?>
                            <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= rtrim(rtrim(number_format((float)$p['available_stock'], 3), '0'), '.') ?> <?= esc($p['unit']) ?></td>
                    <td>
                        <?php if ($p['status'] === 'approved'): ?>
                            <span class="badge green"><?= $p['is_active'] ? 'Live' : 'Paused' ?></span>
                        <?php elseif ($p['status'] === 'pending'): ?>
                            <span class="badge amber">Pending</span>
                        <?php else: ?>
                            <span class="badge red"><?= esc(ucfirst($p['status'])) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap" onclick="event.stopPropagation()">
                        <a class="btn btn-sm" href="<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>/edit">Edit</a>
                        <?php if ($p['status'] === 'approved'): ?>
                            <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>/toggle"><?= $p['is_active'] ? 'Pause' : 'Activate' ?></a>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>/delete" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>