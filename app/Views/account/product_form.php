<?php
/** Seller product add/edit form view. */
$base = rtrim(base_url(), '/');
$errors = $errors ?? [];
$p = $product ?? [];
function pval($p, string $k): string { return esc($p[$k] ?? ''); }
?>
<form method="post" action="<?= esc($base) ?>/account/products<?= $isEdit ? '/' . (int)$p['id'] . '/edit' : '/new' ?>" enctype="multipart/form-data">
    <div class="card">
        <h3><?= $isEdit ? 'Edit Product' : 'Add New Product' ?></h3>

        <div class="row">
            <div>
                <label>Product Name *</label>
                <input type="text" name="name" value="<?= pval($p, 'name') ?>" required>
                <?php if (isset($errors['name'])): ?><div class="err alert error"><?= esc($errors['name']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Category *</label>
                <select name="category_id" required>
                    <option value="">Select category…</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)($p['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category_id'])): ?><div class="err alert error"><?= esc($errors['category_id']) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Brand</label>
                <select name="brand_id">
                    <option value="0">No brand</option>
                    <?php foreach ($brands as $b): ?>
                        <option value="<?= (int)$b['id'] ?>" <?= (int)($p['brand_id'] ?? 0) === (int)$b['id'] ? 'selected' : '' ?>><?= esc($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Grade / Specification</label>
                <input type="text" name="grade" value="<?= pval($p, 'grade') ?>" placeholder="e.g. Fe-500D, IS 2062">
            </div>
        </div>

        <div class="row">
            <div>
                <label>Unit *</label>
                <select name="unit">
                    <?php foreach (['ton', 'kg', 'mt', 'piece', 'bundle', 'unit'] as $u): ?>
                        <option value="<?= $u ?>" <?= pval($p, 'unit') === $u ? 'selected' : '' ?>><?= strtoupper($u) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['unit'])): ?><div class="err alert error"><?= esc($errors['unit']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Price (₹) </label>
                <input type="number" name="price" step="0.01" min="0" value="<?= pval($p, 'price') ?>" placeholder="e.g. 56500">
                <?php if (isset($errors['price'])): ?><div class="err alert error"><?= esc($errors['price']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Offer Price (₹)</label>
                <input type="number" name="offer_price" step="0.01" min="0" value="<?= pval($p, 'offer_price') ?>" placeholder="optional">
            </div>
        </div>

        <div class="row">
            <div>
                <label>Available Stock</label>
                <input type="number" name="available_stock" step="0.001" min="0" value="<?= pval($p, 'available_stock') ?: '0' ?>">
            </div>
            <div>
                <label>MOQ</label>
                <input type="number" name="moq" step="0.001" min="0" value="<?= pval($p, 'moq') ?: '0' ?>">
            </div>
            <div>
                <label>GST Rate (%)</label>
                <input type="number" name="gst_rate" step="0.01" min="0" max="30" value="<?= pval($p, 'gst_rate') ?: '18' ?>">
            </div>
            <div>
                <label>HSN Code</label>
                <input type="text" name="hsn_code" value="<?= pval($p, 'hsn_code') ?>" placeholder="e.g. 7214">
                <?php if (isset($errors['hsn_code'])): ?><div class="err alert error"><?= esc($errors['hsn_code']) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Weight (per unit)</label>
                <input type="text" name="weight" value="<?= pval($p, 'weight') ?>" placeholder="e.g. 12mm, 1.0 ton">
            </div>
            <div>
                <label>Dimensions</label>
                <input type="text" name="dimensions" value="<?= pval($p, 'dimensions') ?>" placeholder="e.g. 6m length">
            </div>
            <div>
                <label>Delivery Time</label>
                <input type="text" name="delivery_time" value="<?= pval($p, 'delivery_time') ?>" placeholder="e.g. 24 hrs">
            </div>
            <div>
                <label>Location</label>
                <input type="text" name="location" value="<?= pval($p, 'location') ?>" placeholder="e.g. Loha Mandi, Ghaziabad">
            </div>
        </div>

        <div class="row">
            <div>
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*">
                <div class="muted" style="font-size:12px;margin-top:4px">JPEG / PNG. Optional.</div>
            </div>
            <div>
                <label style="margin-bottom:8px">&nbsp;</label>
                <label style="display:flex;align-items:center;gap:8px;font-weight:600">
                    <input type="checkbox" name="is_bulk" value="1" style="width:auto" <?= !empty($p['is_bulk']) ? 'checked' : '' ?>>
                    Bulk order available
                </label>
            </div>
        </div>

        <div style="margin-bottom:14px">
            <label>Description</label>
            <textarea name="description" rows="4" placeholder="Product details, delivery terms, payment terms…"><?= pval($p, 'description') ?></textarea>
        </div>

        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Add Product' ?></button>
        <a class="btn" href="<?= esc($base) ?>/account/products">Cancel</a>
    </div>
</form>
