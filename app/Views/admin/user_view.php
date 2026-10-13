<?php $base = rtrim(base_url('admin'), '/'); ?>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
    <div class="stat"><b>#<?= (int)$user['id'] ?></b><span>User ID</span></div>
    <div class="stat"><b><?= $stats['rfqs'] ?></b><span>RFQs Posted</span></div>
    <div class="stat"><b><?= $stats['orders'] ?></b><span>Orders</span></div>
    <?php if ($seller): ?><div class="stat"><b><?= $stats['products'] ?></b><span>Products</span></div><?php endif; ?>
    <?php if ($seller): ?><div class="stat"><b><?= $stats['leads'] ?></b><span>Leads</span></div><?php endif; ?>
</div>

<div class="card">
    <h3>Profile</h3>
    <div class="row">
        <div><label>Name</label><input type="text" value="<?= esc($user['name']) ?>" readonly></div>
        <div><label>Email</label><input type="text" value="<?= esc($user['email']) ?>" readonly></div>
        <div><label>Mobile</label><input type="text" value="<?= esc($user['mobile']) ?>" readonly></div>
        <div><label>Role</label><input type="text" value="<?= esc($user['role_name'] ?? '—') ?>" readonly></div>
        <div><label>Status</label><input type="text" value="<?= esc($user['status']) ?>" readonly></div>
        <div><label>Joined</label><input type="text" value="<?= esc($user['created_at']) ?>" readonly></div>
    </div>
    <div class="toolbar" style="justify-content:flex-start">
        <a class="btn <?= $user['status'] === 'active' ? 'btn-danger' : 'btn-primary' ?>" href="<?= esc($base) ?>/users/toggle/<?= (int)$user['id'] ?>"><?= $user['status'] === 'active' ? 'Suspend user' : 'Activate user' ?></a>
        <a class="btn" href="<?= esc($base) ?>/users">← Back</a>
    </div>
</div>

<?php if ($seller): ?>
<div class="card">
    <h3>Business Profile</h3>
    <div class="row">
        <div><label>Business Name</label><input type="text" value="<?= esc($seller['business_name']) ?>" readonly></div>
        <div><label>GSTIN</label><input type="text" value="<?= esc($seller['gstin']) ?>" readonly></div>
        <div><label>City / State</label><input type="text" value="<?= esc($seller['city'] . ', ' . $seller['state']) ?>" readonly></div>
        <div><label>Status</label><input type="text" value="<?= esc($seller['status']) ?>" readonly></div>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/sellers/<?= (int)$seller['id'] ?>">Manage business →</a>
</div>
<?php endif; ?>