<?php $base = rtrim(base_url('admin'), '/'); $p = $product; ?>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
    <div class="stat"><b>#<?= (int)$p['id'] ?></b><span>Product ID</span></div>
    <div class="stat"><b><?= $p['status'] ?></b><span>Status</span></div>
    <div class="stat"><b><?= number_format((int)$p['view_count']) ?></b><span>Views</span></div>
    <div class="stat"><b><?= number_format((int)$p['inquiry_count']) ?></b><span>Inquiries</span></div>
</div>

<div class="card">
    <h3><?= esc($p['name']) ?></h3>
    <div class="row">
        <div><label>Seller</label><input type="text" value="<?= esc($p['seller']['business_name'] ?? '—') ?>" readonly></div>
        <div><label>Category</label><input type="text" value="<?= esc($p['category_name'] ?? '—') ?>" readonly></div>
        <div><label>Brand</label><input type="text" value="<?= esc($p['brand_name'] ?? '—') ?>" readonly></div>
        <div><label>HSN / Grade</label><input type="text" value="<?= esc($p['hsn_code']) ?> / <?= esc($p['grade']) ?>" readonly></div>
        <div><label>GST</label><input type="text" value="<?= (float)$p['gst_rate'] ?>%" readonly></div>
        <div><label>Location</label><input type="text" value="<?= esc($p['location']) ?>" readonly></div>
        <div><label>MOQ</label><input type="text" value="<?= number_format((float)$p['moq']) ?> <?= esc($p['unit']) ?>" readonly></div>
        <div><label>Stock</label><input type="text" value="<?= number_format((float)$p['available_stock']) ?> <?= esc($p['unit']) ?>" readonly></div>
        <div><label>Price</label><input type="text" value="<?= $p['price'] ? '₹' . number_format((float)$p['price']) : '—' ?>" readonly></div>
        <div><label>Offer Price</label><input type="text" value="<?= $p['offer_price'] ? '₹' . number_format((float)$p['offer_price']) : '—' ?>" readonly></div>
    </div>
    <p class="muted" style="margin-top:4px"><?= esc($p['description']) ?></p>
</div>

<?php if (!empty($p['specifications']) && is_string($p['specifications']) && json_decode($p['specifications'])): ?>
<div class="card">
    <h3>Specifications</h3>
    <div class="row">
        <?php foreach (json_decode($p['specifications'], true) as $k => $v): ?>
            <div><label><?= esc(ucfirst((string)$k)) ?></label><input type="text" value="<?= esc($v) ?>" readonly></div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($p['bulk_prices'])): ?>
<div class="card">
    <h3>Bulk Pricing Slabs</h3>
    <table>
        <thead><tr><th>Min Qty</th><th>Max Qty</th><th>Unit Price</th></tr></thead>
        <tbody>
        <?php foreach ($p['bulk_prices'] as $b): ?>
            <tr><td><?= number_format((float)$b['min_qty']) ?> <?= esc($p['unit']) ?></td><td><?= $b['max_qty'] ? number_format((float)$b['max_qty']) . ' ' . esc($p['unit']) : '∞' ?></td><td><b>₹<?= number_format((float)$b['unit_price']) ?></b></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="card">
    <h3>Moderation</h3>
    <div class="toolbar" style="justify-content:flex-start;margin:0">
        <form method="post" action="<?= esc($base) ?>/products/<?= (int)$p['id'] ?>/moderate" style="display:flex;gap:8px">
            <button class="btn btn-sm <?= $p['status'] === 'approved' ? 'btn-primary' : '' ?>" type="submit" name="status" value="approved">Approve</button>
            <button class="btn btn-sm <?= $p['status'] === 'pending' ? 'btn-primary' : '' ?>" type="submit" name="status" value="pending">Set pending</button>
            <button class="btn btn-sm btn-danger <?= $p['status'] === 'rejected' ? 'btn-primary' : '' ?>" type="submit" name="status" value="rejected">Reject</button>
        </form>
        <a class="btn" href="<?= esc($base) ?>/products/feature/<?= (int)$p['id'] ?>"><?= $p['is_featured'] ? 'Unfeature' : 'Mark featured' ?></a>
    </div>
</div>

<a class="btn" href="<?= esc($base) ?>/products">← Back to products</a>