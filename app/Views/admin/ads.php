<?php $base = rtrim(base_url('admin'), '/');
$types = [
    'homepage_banner'   => 'Homepage Banner',
    'sponsored_product' => 'Sponsored Product',
    'featured_seller'   => 'Featured Seller',
    'category_banner'   => 'Category Banner',
    'popup'             => 'Popup / Offer',
];
$img = function ($img) {
    $type = explode('/', (string)$img)[0] ?? '';
    $name = explode('/', (string)$img)[1] ?? '';
    return $img ? base_url('uploads/' . $type . '/' . $name) : '';
};
?>
<div class="card">
    <h3>Create Advertisement</h3>
    <form method="post" action="<?= esc($base) ?>/ads" enctype="multipart/form-data">
        <div class="row">
            <div><label>Title *</label><input type="text" name="title" required></div>
            <div><label>Placement</label>
                <select name="type">
                    <?php foreach ($types as $k => $v): ?><option value="<?= esc($k) ?>"><?= esc($v) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div><label>Image</label><input type="file" name="image" accept="image/*"></div>
            <div><label>Destination Link</label><input type="url" name="link" placeholder="https://"></div>
        </div>
        <div class="row">
            <div><label>Featured Seller</label>
                <select name="seller_id"><option value="0">— None —</option>
                    <?php foreach ($sellers as $s): ?><option value="<?= (int)$s['id'] ?>"><?= esc($s['business_name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label>Sponsored Product</label>
                <select name="product_id"><option value="0">— None —</option>
                    <?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= esc($p['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label>Category (banner)</label>
                <select name="category_id"><option value="0">— All —</option>
                    <?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div><label>Start Date</label><input type="datetime-local" name="start_date"></div>
            <div><label>End Date</label><input type="datetime-local" name="end_date"></div>
            <div><label>Status</label><select name="status"><option value="1">Active</option><option value="0">Hidden</option></select></div>
        </div>
        <button class="btn btn-primary" type="submit">Create Ad</button>
    </form>
</div>

<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Ad</th><th>Placement</th><th>Target</th><th>Schedule</th><th>Clicks</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($ads)): ?>
            <tr><td colspan="8" class="empty">No ads yet.</td></tr>
        <?php else: foreach ($ads as $a): ?>
            <tr>
                <td class="mono">#<?= (int)$a['id'] ?></td>
                <td>
                    <div style="display:flex;gap:10px;align-items:center">
                        <?php if ($a['image']): ?>
                            <img src="<?= esc($img($a['image'])) ?>" style="width:52px;height:34px;object-fit:cover;border-radius:6px">
                        <?php else: ?>
                            <span style="width:52px;height:34px;background:#f3f4f6;border-radius:6px;display:inline-block"></span>
                        <?php endif; ?>
                        <div><b><?= esc($a['title']) ?></b>
                            <div class="muted" style="font-size:12px"><?= $a['link'] ? esc($a['link']) : 'No link' ?></div>
                        </div>
                    </div>
                </td>
                <td><span class="pill"><?= esc($types[$a['type']] ?? $a['type']) ?></span></td>
                <td class="muted" style="font-size:12px">
                    <?= $a['category_name'] ? 'Cat: ' . esc($a['category_name']) . '<br>' : '' ?>
                    <?= $a['product_name'] ? 'Product: ' . esc($a['product_name']) . '<br>' : '' ?>
                    <?= $a['seller_name'] ? 'Seller: ' . esc($a['seller_name']) : '' ?>
                </td>
                <td class="mono muted" style="font-size:12px">
                    <?= $a['start_date'] ? date('M j', strtotime($a['start_date'])) : '∞' ?> →
                    <?= $a['end_date'] ? date('M j', strtotime($a['end_date'])) : '∞' ?>
                </td>
                <td class="mono"><?= number_format((int)$a['clicks']) ?></td>
                <td><?= $a['status'] ? '<span class="badge green">Active</span>' : '<span class="badge gray">Hidden</span>' ?></td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/ads/<?= (int)$a['id'] ?>/toggle"><?= $a['status'] ? 'Hide' : 'Publish' ?></a>
                    <button class="btn btn-sm btn-outline" onclick="editAd(<?= (int)$a['id'] ?>)">Edit</button>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/ads/delete/<?= (int)$a['id'] ?>" onclick="return confirm('Delete this ad?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="card" id="editBox" style="display:none">
    <h3>Edit Ad</h3>
    <form method="post" id="editForm" action="" enctype="multipart/form-data">
        <div class="row">
            <div><label>Title *</label><input type="text" name="title" id="editTitle" required></div>
            <div><label>Placement</label>
                <select name="type" id="editType">
                    <?php foreach ($types as $k => $v): ?><option value="<?= esc($k) ?>"><?= esc($v) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div><label>New Image (optional)</label><input type="file" name="image" accept="image/*"></div>
            <div><label>Destination Link</label><input type="url" name="link" id="editLink" placeholder="https://"></div>
        </div>
        <div class="row">
            <div><label>Featured Seller</label><select name="seller_id" id="editSellerId"><option value="0">— None —</option><?php foreach ($sellers as $s): ?><option value="<?= (int)$s['id'] ?>"><?= esc($s['business_name']) ?></option><?php endforeach; ?></select></div>
            <div><label>Sponsored Product</label><select name="product_id" id="editProductId"><option value="0">— None —</option><?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= esc($p['name']) ?></option><?php endforeach; ?></select></div>
            <div><label>Category</label><select name="category_id" id="editCategoryId"><option value="0">— All —</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="row">
            <div><label>Start Date</label><input type="datetime-local" name="start_date" id="editStart"></div>
            <div><label>End Date</label><input type="datetime-local" name="end_date" id="editEnd"></div>
            <div><label>Status</label><select name="status" id="editStatus"><option value="1">Active</option><option value="0">Hidden</option></select></div>
        </div>
        <button class="btn btn-primary" type="submit">Save Ad</button>
        <button class="btn" type="button" onclick="document.getElementById('editBox').style.display='none'">Cancel</button>
    </form>
</div>

<script>
function editAd(id){
    var form = document.getElementById('editForm');
    form.action = '<?= esc($base) ?>/ads/' + id + '/update';
    var edits = <?= json_encode(array_map(fn($a) => [
        'id' => (int)$a['id'], 'type' => $a['type'], 'title' => $a['title'], 'link' => $a['link'],
        'seller' => (int)$a['seller_id'], 'product' => (int)$a['product_id'],
        'category' => (int)$a['category_id'], 'status' => (int)$a['status'],
        'start' => $a['start_date'] ? date('Y-m-d\TH:i', strtotime($a['start_date'])) : '',
        'end' => $a['end_date'] ? date('Y-m-d\TH:i', strtotime($a['end_date'])) : '',
    ], $ads)) ?>;
    var ad = edits.find(function(x){ return x.id === id; });
    document.getElementById('editType').value = ad.type;
    document.getElementById('editTitle').value = ad.title;
    document.getElementById('editLink').value = ad.link || '';
    document.getElementById('editSellerId').value = ad.seller;
    document.getElementById('editProductId').value = ad.product;
    document.getElementById('editCategoryId').value = ad.category;
    document.getElementById('editStatus').value = ad.status;
    document.getElementById('editStart').value = ad.start;
    document.getElementById('editEnd').value = ad.end;
    document.getElementById('editBox').style.display = 'block';
    document.getElementById('editBox').scrollIntoView({behavior:'smooth'});
}
</script>