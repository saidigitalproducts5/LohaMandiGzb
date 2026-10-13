<?php $base = rtrim(base_url('admin'), '/'); $s = $seller; ?>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
    <div class="stat"><b>#<?= (int)$s['id'] ?></b><span>Seller ID</span></div>
    <div class="stat"><b><?= number_format($s['product_count']) ?></b><span>Products</span></div>
    <div class="stat"><b><?= number_format($s['lead_count']) ?></b><span>Leads</span></div>
    <div class="stat"><b><?= number_format($s['rfq_count']) ?></b><span>RFQs</span></div>
    <div class="stat"><b><?= $s['rating_avg'] ?> ★</b><span><?= (int)$s['rating_count'] ?> Reviews</span></div>
</div>

<div class="card">
    <h3>Business Profile</h3>
    <div class="row">
        <div><label>Business Name</label><input type="text" value="<?= esc($s['business_name']) ?>" readonly></div>
        <div><label>Owner</label><input type="text" value="<?= esc($s['owner_name']) ?> · <?= esc($s['owner_phone']) ?>" readonly></div>
        <div><label>GSTIN</label><input type="text" value="<?= esc($s['gstin']) ?>" readonly></div>
        <div><label>Years in Business</label><input type="text" value="<?= (int)$s['years_in_business'] ?>" readonly></div>
        <div><label>Status</label>
            <form method="post" action="<?= esc($base) ?>/sellers/<?= (int)$s['id'] ?>/moderate" style="display:flex;gap:8px">
                <?php foreach (['active' => 'Active', 'pending' => 'Pending', 'rejected' => 'Rejected', 'suspended' => 'Suspend'] as $k => $v): ?>
                    <button class="btn btn-sm <?= $s['status'] === $k ? 'btn-primary' : '' ?>" type="submit" name="status" value="<?= $k ?>"><?= $v ?></button>
                <?php endforeach; ?>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <h3>Edit KYC / Location / Flags</h3>
    <form method="post" action="<?= esc($base) ?>/sellers/<?= (int)$s['id'] ?>/update">
        <div class="row">
            <div><label>GSTIN</label><input type="text" name="gstin" value="<?= esc($s['gstin']) ?>"></div>
            <div><label>City</label><input type="text" name="city" value="<?= esc($s['city']) ?>"></div>
            <div><label>State</label><input type="text" name="state" value="<?= esc($s['state']) ?>"></div>
            <div><label>Pincode</label><input type="text" name="pincode" value="<?= esc($s['pincode']) ?>"></div>
            <div><label>Address Line 1</label><input type="text" name="address_line1" value="<?= esc($s['address_line1']) ?>"></div>
            <div><label>Business Hours</label><input type="text" name="business_hours" value="<?= esc($s['business_hours']) ?>"></div>
            <div><label>Years in Business</label><input type="number" name="years_in_business" value="<?= (int)$s['years_in_business'] ?>"></div>
            <div><label>Employees</label><input type="number" name="employees_count" value="<?= (int)$s['employees_count'] ?>"></div>
        </div>
        <div class="row">
            <label style="display:flex;align-items:center;gap:8px;margin:0"><input type="checkbox" name="gst_verified" value="1" <?= $s['gst_verified'] ? 'checked' : '' ?>> GST Verified</label>
            <label style="display:flex;align-items:center;gap:8px;margin:0"><input type="checkbox" name="is_verified" value="1" <?= $s['is_verified'] ? 'checked' : '' ?>> Verified Supplier</label>
            <label style="display:flex;align-items:center;gap:8px;margin:0"><input type="checkbox" name="is_premium" value="1" <?= $s['is_premium'] ? 'checked' : '' ?>> Premium Member</label>
        </div>
        <button class="btn btn-primary" type="submit">Save changes</button>
    </form>
</div>

<?php if ($s['membership']): ?>
<div class="card">
    <h3>Membership</h3>
    <div class="row">
        <div><label>Plan</label><input type="text" value="<?= esc($s['membership']['plan_name']) ?>" readonly></div>
        <div><label>Valid Until</label><input type="text" value="<?= esc($s['membership']['end_date']) ?>" readonly></div>
        <div><label>Status</label><input type="text" value="<?= esc($s['membership']['status']) ?>" readonly></div>
    </div>
</div>
<?php endif; ?>

<a class="btn" href="<?= esc($base) ?>/sellers">← Back to sellers</a>