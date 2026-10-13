<?php $base = rtrim(base_url('admin'), '/'); $f = $filters; ?>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/sellers">
            <input type="text" name="q" value="<?= esc($f['q']) ?>" placeholder="Search business, GSTIN, mobile…">
            <select name="status">
                <?php foreach (['all' => 'All statuses', 'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'suspended' => 'Suspended'] as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $f['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <select name="city">
                <option value="">All cities</option>
                <?php foreach ($cities as $c): ?>
                    <option value="<?= esc($c) ?>" <?= $f['city'] === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
                <?php endforeach; ?>
            </select>
            <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" name="premium" value="1" <?= $f['premium'] ? 'checked' : '' ?>> Premium</label>
            <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" name="verified" value="1" <?= $f['verified'] ? 'checked' : '' ?>> GST verified</label>
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn" href="<?= esc($base) ?>/sellers">Reset</a>
        </form>
        <span class="muted"><?= number_format($total) ?> seller(s)</span>
    </div>

    <table>
        <thead>
            <tr><th>ID</th><th>Business</th><th>Owner</th><th>GSTIN</th><th>Location</th><th>Premium</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($sellers)): ?>
            <tr><td colspan="8" class="empty">No sellers match.</td></tr>
        <?php else: foreach ($sellers as $s): ?>
            <tr>
                <td class="mono">#<?= (int)$s['id'] ?></td>
                <td><b><?= esc($s['business_name']) ?></b></td>
                <td><?= esc($s['owner_name']) ?><br><span class="muted mono"><?= esc($s['owner_mobile']) ?></span></td>
                <td class="mono"><?= esc($s['gstin']) ?></td>
                <td><?= esc($s['city']) ?>, <?= esc($s['state']) ?></td>
                <td><?= $s['is_premium'] ? '<span class="badge amber">Premium</span>' : '<span class="pill">—</span>' ?></td>
                <td>
                    <?php if ($s['status'] === 'active'): ?><span class="badge green">Active</span>
                    <?php elseif ($s['status'] === 'pending'): ?><span class="badge amber">Pending</span>
                    <?php elseif ($s['status'] === 'rejected'): ?><span class="badge red">Rejected</span>
                    <?php else: ?><span class="badge gray"><?= esc($s['status']) ?></span><?php endif; ?>
                </td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm" href="<?= esc($base) ?>/sellers/<?= (int)$s['id'] ?>">Manage</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php $pages = (int) ceil($total / $limit); if ($pages > 1): ?>
        <div class="pager">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= esc($base) ?>/sellers?page=<?= $i ?>&q=<?= urlencode($f['q']) ?>&status=<?= urlencode($f['status']) ?>&city=<?= urlencode($f['city']) ?>" <?= $i === $page ? 'style="border-color:var(--brand);color:var(--brand)"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>