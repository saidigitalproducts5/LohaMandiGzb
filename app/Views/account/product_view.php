<?php
/** Seller product detail view. */
$base = rtrim(base_url(), '/');
$p = $product;
$badge = $p['status'] === 'approved' ? ($p['is_active'] ? 'green' : 'amber') : ($p['status'] === 'pending' ? 'amber' : 'red');
$badgeLabel = $p['status'] === 'approved' ? ($p['is_active'] ? 'Live' : 'Paused') : ucfirst($p['status']);
$imgUrl = !empty($p['primary_image']) ? base_url('uploads/' . $p['primary_image']) : null;
?>
<div class="toolbar" style="justify-content:space-between;margin-top:0">
    <a class="btn" href="<?= esc($base) ?>/account/products">← Back to products</a>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a class="btn btn-sm" href="<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>/edit">Edit</a>
        <?php if ($p['status'] === 'approved'): ?>
            <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>/toggle"><?= $p['is_active'] ? 'Pause' : 'Activate' ?></a>
        <?php endif; ?>
        <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/products/<?= (int)$p['id'] ?>/delete" onclick="return confirm('Delete this product?')">Delete</a>
    </div>
</div>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(300px,1fr));align-items:start">

    <div class="card">
        <h3 style="margin-top:0">Product Images (<?= count($p['images']) ?>)</h3>
        <?php if ($imgUrl !== null): ?>
            <div style="border:1px solid var(--line);border-radius:10px;overflow:hidden;height:300px;display:flex;align-items:center;justify-content:center;background:#fafafa">
                <img src="<?= esc($imgUrl) ?>" alt="<?= esc($p['name']) ?>" id="mainSrc" style="max-width:100%;max-height:100%;object-fit:contain">
            </div>
            <?php if (count($p['images']) > 1): ?>
                <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap">
                    <?php foreach ($p['images'] as $i => $img): ?>
                        <div onclick="swap(this,'<?= esc(base_url('uploads/' . $img['file_path'])) ?>')"
                             style="width:54px;height:54px;border-radius:8px;overflow:hidden;border:2px solid <?= $img['file_path'] === $p['primary_image'] ? 'var(--primary, #e02735)' : 'var(--line)' ?>;cursor:pointer;flex:none">
                            <img src="<?= esc(base_url('uploads/' . $img['file_path'])) ?>" style="width:100%;height:100%;object-fit:cover">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty" style="height:300px;display:flex;align-items:center;justify-content:center;border:1px dashed var(--line);border-radius:10px">No image uploaded yet</div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card">
            <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;flex-wrap:wrap">
                <h3 style="margin:0"><?= esc($p['name']) ?></h3>
                <span class="badge <?= $badge ?>"><?= esc($badgeLabel) ?></span>
            </div>
            <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap">
                <span class="muted mono">#<?= (int)$p['id'] ?></span>
                <?php if (!empty($p['is_featured'])): ?><span class="pill">★ Featured</span><?php endif; ?>
                <?php if (!empty($p['is_bulk'])): ?><span class="pill">Bulk</span><?php endif; ?>
            </div>

            <table style="margin-top:14px">
                <tbody>
                    <tr><th style="width:40%">Category</th><td><?= esc($p['category_name'] ?? '—') ?></td></tr>
                    <?php if (!empty($p['brand_name'])): ?><tr><th>Brand</th><td><?= esc($p['brand_name']) ?></td></tr><?php endif; ?>
                    <tr><th>Price</th><td><?= !empty($p['price']) ? '₹' . number_format((float)$p['price']) . ' <span class="muted">/' . esc($p['unit']) . '</span>' : '<span class="muted">—</span>' ?></td></tr>
                    <tr><th>Offer Price</th><td><?= !empty($p['offer_price']) ? '<b>₹' . number_format((float)$p['offer_price']) . '</b> <span class="muted">/' . esc($p['unit']) . '</span>' : '<span class="muted">—</span>' ?></td></tr>
                    <tr><th>Stock</th><td><?= rtrim(rtrim(number_format((float)$p['available_stock'], 3), '0'), '.') ?> <?= esc($p['unit']) ?></td></tr>
                    <tr><th>MOQ</th><td><?= number_format((float)$p['moq']) ?> <?= esc($p['unit']) ?></td></tr>
                    <tr><th>HSN / Grade</th><td><?= esc($p['hsn_code'] ?? '—') ?> / <?= esc($p['grade'] ?? '—') ?></td></tr>
                    <tr><th>GST</th><td><?= (float)$p['gst_rate'] ?>%</td></tr>
                    <tr><th>Weight</th><td><?= esc($p['weight'] ?? '—') ?></td></tr>
                    <tr><th>Dimensions</th><td><?= esc($p['dimensions'] ?? '—') ?></td></tr>
                    <tr><th>Delivery Time</th><td><?= esc($p['delivery_time'] ?? '—') ?></td></tr>
                    <tr><th>Location</th><td><?= esc($p['location'] ?? '—') ?></td></tr>
                    <tr><th>Views / Inquiries</th><td><?= number_format((int)($p['view_count'] ?? 0)) ?> / <?= number_format((int)($p['inquiry_count'] ?? 0)) ?></td></tr>
                    <tr><th>Created</th><td class="muted"><?= esc(date('d M Y, h:i A', strtotime($p['created_at']))) ?></td></tr>
                </tbody>
            </table>

            <?php if (!empty($p['description'])): ?>
                <div style="margin-top:12px">
                    <label style="font-size:12px">Description</label>
                    <p class="muted" style="margin:4px 0 0"><?= nl2br(esc($p['description'])) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($p['bulk_prices'])): ?>
            <div class="card">
                <h3>Bulk Pricing Slabs</h3>
                <table>
                    <thead><tr><th>Min Qty</th><th>Max Qty</th><th>Unit Price</th></tr></thead>
                    <tbody>
                    <?php foreach ($p['bulk_prices'] as $b): ?>
                        <tr>
                            <td><?= number_format((float)$b['min_qty']) ?> <?= esc($p['unit']) ?></td>
                            <td><?= $b['max_qty'] ? number_format((float)$b['max_qty']) . ' ' . esc($p['unit']) : '∞' ?></td>
                            <td><b>₹<?= number_format((float)$b['unit_price']) ?></b></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function swap(el, url){
    if (!url) return;
    var main = document.getElementById('mainSrc');
    if (main) { main.src = url; }
    if (el) {
        Array.prototype.forEach.call(el.parentNode.children, function(c){ c.style.borderColor = 'var(--line)'; });
        el.style.borderColor = 'var(--primary, #e02735)';
    }
}
</script>

<a class="btn" href="<?= esc($base) ?>/account/products">← Back to products</a>
