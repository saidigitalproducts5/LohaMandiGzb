<?php $base = rtrim(base_url('admin'), '/'); $f = $filters; ?>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/products">
            <input type="text" name="q" value="<?= esc($f['q']) ?>" placeholder="Search product, HSN, seller…">
            <select name="status">
                <?php foreach (['all' => 'All statuses', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $f['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <select name="category">
                <option value="0">All categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= (int)$f['category'] === (int)$c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn" href="<?= esc($base) ?>/products">Reset</a>
        </form>
        <span class="muted"><?= number_format($total) ?> product(s)</span>
    </div>

    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>ID</th><th>Product</th><th>Seller</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="8" class="empty">No products match.</td></tr>
        <?php else: foreach ($products as $p): ?>
            <tr>
                <td class="mono">#<?= (int)$p['id'] ?></td>
                <td>
                    <b><?= esc($p['name']) ?></b>
                    <?php if ($p['is_featured']): ?><span class="pill">★ Featured</span><?php endif; ?>
                    <br><span class="muted mono"><?= esc($p['hsn_code']) ?></span>
                </td>
                <td class="muted"><?= esc($p['business_name'] ?? '—') ?></td>
                <td><?= esc($p['category_name'] ?? '—') ?></td>
                <td>
                    <?php if ($p['offer_price']): ?><b>₹<?= number_format((float)$p['offer_price']) ?></b> <s class="muted">₹<?= number_format((float)$p['price']) ?></s>
                    <?php elseif ($p['price']): ?>₹<?= number_format((float)$p['price']) ?><?php else: ?>—<?php endif; ?>
                </td>
                <td><?= number_format((float)$p['available_stock']) ?> <?= esc($p['unit']) ?></td>
                <td>
                    <?php if ($p['status'] === 'approved'): ?><span class="badge green">Approved</span>
                    <?php elseif ($p['status'] === 'pending'): ?><span class="badge amber">Pending</span>
                    <?php else: ?><span class="badge red">Rejected</span><?php endif; ?>
                </td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm" href="<?= esc($base) ?>/products/<?= (int)$p['id'] ?>">View</a>
                    <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/products/feature/<?= (int)$p['id'] ?>"><?= $p['is_featured'] ? 'Unfeature' : 'Feature' ?></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>

    <?php $pages = (int) ceil($total / $limit); if ($pages > 1): ?>
        <div class="pager">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= esc($base) ?>/products?page=<?= $i ?>&q=<?= urlencode($f['q']) ?>&status=<?= urlencode($f['status']) ?>&category=<?= (int)$f['category'] ?>" <?= $i === $page ? 'style="border-color:var(--brand);color:var(--brand)"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>