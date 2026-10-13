<?php
/** Buyer RFQ post form. */
$base = rtrim(base_url(), '/');
$errors = $errors ?? [];
$old = $old ?? [];
function rval($old, string $k): string { return esc($old[$k] ?? ''); }
?>
<form method="post" action="<?= esc($base) ?>/account/rfqs/new" enctype="multipart/form-data">
    <div class="card">
        <h3>Post a Request for Quotation</h3>
        <p class="muted" style="margin-bottom:16px">Matching verified sellers in Ghaziabad &amp; nearby will be notified instantly.</p>

        <div class="row">
            <div>
                <label>Product / Material *</label>
                <input type="text" name="product_name" value="<?= rval($old, 'product_name') ?>" placeholder="e.g. TMT Bar 12mm Fe-500D" required>
                <?php if (isset($errors['product_name'])): ?><div class="err alert error"><?= esc($errors['product_name']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">Select category…</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)($old['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Quantity *</label>
                <input type="number" name="quantity" step="0.001" min="0.001" value="<?= rval($old, 'quantity') ?>" required>
                <?php if (isset($errors['quantity'])): ?><div class="err alert error"><?= esc($errors['quantity']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Unit *</label>
                <select name="unit">
                    <?php foreach (['ton', 'kg', 'mt', 'piece', 'bundle', 'unit'] as $u): ?>
                        <option value="<?= $u ?>" <?= rval($old, 'unit') === $u ? 'selected' : '' ?>><?= strtoupper($u) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['unit'])): ?><div class="err alert error"><?= esc($errors['unit']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Required By</label>
                <input type="date" name="required_date" value="<?= rval($old, 'required_date') ?>">
                <?php if (isset($errors['required_date'])): ?><div class="err alert error"><?= esc($errors['required_date']) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Budget Min (₹)</label>
                <input type="number" name="budget_min" step="0.01" min="0" value="<?= rval($old, 'budget_min') ?>">
            </div>
            <div>
                <label>Budget Max (₹)</label>
                <input type="number" name="budget_max" step="0.01" min="0" value="<?= rval($old, 'budget_max') ?>">
            </div>
            <div>
                <label>City</label>
                <input type="text" name="city" value="<?= rval($old, 'city') ?>" placeholder="e.g. Ghaziabad">
            </div>
            <div>
                <label>State</label>
                <input type="text" name="state" value="<?= rval($old, 'state') ?>" placeholder="e.g. Uttar Pradesh">
            </div>
        </div>

        <div class="row">
            <div>
                <label>Delivery Location</label>
                <input type="text" name="delivery_location" value="<?= rval($old, 'delivery_location') ?>" placeholder="Site / warehouse address">
            </div>
        </div>

        <div style="margin-bottom:14px">
            <label>Specification / Details</label>
            <textarea name="specification" rows="4" placeholder="Grade, brand preference, packing, payment terms…"><?= rval($old, 'specification') ?></textarea>
            <?php if (isset($errors['specification'])): ?><div class="err alert error"><?= esc($errors['specification']) ?></div><?php endif; ?>
        </div>

        <div class="row">
            <div>
                <label>Specification Document</label>
                <input type="file" name="document" accept=".pdf,.doc,.docx">
            </div>
            <div>
                <label>Product Photo</label>
                <input type="file" name="photo" accept="image/*">
            </div>
        </div>

        <button class="btn btn-primary" type="submit">Post RFQ</button>
        <a class="btn" href="<?= esc($base) ?>/account/rfqs">Cancel</a>
    </div>
</form>
