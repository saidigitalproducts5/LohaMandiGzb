<?php
/** CRM add/edit deal. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3><?= $isEdit ? 'Edit Deal' : 'New Deal' ?></h3>
    <form method="post" action="<?= $isEdit ? esc($base) . '/account/crm/deals/' . (int)($deal['id'] ?? 0) . '/edit' : esc($base) . '/account/crm/deal/new' ?>">
        <div class="row">
            <div><label>Deal Title *</label><input type="text" name="title" value="<?= esc($deal['title'] ?? '') ?>" required></div>
            <div><label>Value (₹)</label><input type="number" name="value" step="0.01" min="0" value="<?= esc($deal['value'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>Customer</label>
                <select name="customer_id">
                    <option value="0">— None —</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)($deal['customer_id'] ?? 0) === (int)$c['id'] || ($presetCustomer ?? 0) == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?><?= $c['company'] ? ' — ' . esc($c['company']) : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Stage</label>
                <select name="stage">
                    <?php foreach ($stages as $k => $l): ?>
                        <option value="<?= $k ?>" <?= ($deal['stage'] ?? 'prospecting') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Expected Close</label><input type="date" name="expected_close" value="<?= esc($deal['expected_close'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>Notes</label><textarea name="notes" rows="3"><?= esc($deal['notes'] ?? '') ?></textarea></div>
        </div>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Deal' ?></button>
        <a class="btn" href="<?= esc($base) ?>/account/crm/deals">Cancel</a>
    </form>
</div>