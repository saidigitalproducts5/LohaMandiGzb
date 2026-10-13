<?php
/** Warehouse form (create/edit). */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3><?= $isEdit ? 'Edit Warehouse' : 'Add Warehouse' ?></h3>
    <form method="post" action="<?= $isEdit ? esc($base) . '/account/inventory/warehouses/' . (int)$warehouse['id'] . '/edit' : esc($base) . '/account/inventory/warehouses/new' ?>">
        <div class="row">
            <div><label>Warehouse Name *</label><input type="text" name="name" value="<?= esc($warehouse['name'] ?? '') ?>" required></div>
            <div><label>Contact Phone</label><input type="tel" name="contact_phone" value="<?= esc($warehouse['contact_phone'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>Address</label><input type="text" name="address" value="<?= esc($warehouse['address'] ?? '') ?>"></div>
            <div><label>City</label><input type="text" name="city" value="<?= esc($warehouse['city'] ?? '') ?>"></div>
        </div>
        <div class="row">
            <div><label>State</label><input type="text" name="state" value="<?= esc($warehouse['state'] ?? '') ?>"></div>
            <div><label>Pincode</label><input type="text" name="pincode" value="<?= esc($warehouse['pincode'] ?? '') ?>"></div>
        </div>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Add Warehouse' ?></button>
        <a class="btn" href="<?= esc($base) ?>/account/inventory/warehouses">Cancel</a>
    </form>
</div>