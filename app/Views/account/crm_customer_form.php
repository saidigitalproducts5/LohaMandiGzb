<?php
/** CRM customer form (create/edit). */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3><?= $isEdit ? 'Edit Customer' : 'Add Customer' ?></h3>
    <form method="post" action="<?= esc($base) ?>/account/crm/customers<?= $isEdit ? '/' . (int)($customer['id'] ?? 0) . '/edit' : '/new' ?>">
        <div class="row">
            <div><label>Name *</label><input type="text" name="name" value="<?= esc($customer['name'] ?? '') ?>" required></div>
            <div><label>Company</label><input type="text" name="company" value="<?= esc($customer['company'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>Mobile</label><input type="tel" name="mobile" value="<?= esc($customer['mobile'] ?? '') ?>"></div>
            <div><label>Email</label><input type="email" name="email" value="<?= esc($customer['email'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>City</label><input type="text" name="city" value="<?= esc($customer['city'] ?? '') ?>"></div>
            <div><label>Source</label>
                <select name="source">
                    <?php foreach (['manual', 'lead', 'rfq', 'order', 'referral'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($customer['source'] ?? 'manual') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div><label>Address</label><input type="text" name="address" value="<?= esc($customer['address'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>Notes</label><textarea name="notes" rows="3"><?= esc($customer['notes'] ?? '') ?></textarea></div>
        </div>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Add Customer' ?></button>
        <a class="btn" href="<?= esc($base) ?>/account/crm/customers">Cancel</a>
    </form>
</div>