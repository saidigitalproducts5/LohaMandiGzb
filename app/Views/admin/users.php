<?php $base = rtrim(base_url('admin'), '/'); $f = $filters; ?>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/users">
            <input type="text" name="q" value="<?= esc($f['q']) ?>" placeholder="Search name, email or mobile…">
            <select name="role">
                <option value="0">All roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= (int)$r['id'] ?>" <?= (int)$f['role'] === (int)$r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status">
                <?php foreach (['all' => 'All statuses', 'active' => 'Active', 'suspended' => 'Suspended', 'deleted' => 'Deleted', 'inactive' => 'Inactive'] as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $f['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn" href="<?= esc($base) ?>/users">Reset</a>
        </form>
        <span class="muted"><?= number_format($total) ?> user(s)</span>
    </div>

    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Contact</th><th>Role</th><th>Verified</th><th>Status</th><th>Joined</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="8" class="empty">No users match.</td></tr>
        <?php else: foreach ($users as $u): ?>
            <tr>
                <td class="mono">#<?= (int)$u['id'] ?></td>
                <td><b><?= esc($u['name']) ?></b></td>
                <td class="mono"><?= esc($u['email']) ?><br><?= esc($u['mobile']) ?></td>
                <td><span class="pill"><?= esc($u['role_name'] ?? '—') ?></span></td>
                <td>
                    <?php if ($u['mobile_verified']): ?><span class="pill">📱</span><?php endif; ?>
                    <?php if ($u['email_verified']): ?><span class="pill">✉️</span><?php endif; ?>
                </td>
                <td>
                    <?php if ($u['status'] === 'active'): ?><span class="badge green">Active</span>
                    <?php elseif ($u['status'] === 'suspended'): ?><span class="badge red">Suspended</span>
                    <?php elseif ($u['status'] === 'deleted'): ?><span class="badge gray">Deleted</span>
                    <?php else: ?><span class="badge gray"><?= esc($u['status']) ?></span><?php endif; ?>
                </td>
                <td class="muted"><?= esc(date('d M Y', strtotime($u['created_at']))) ?></td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm" href="<?= esc($base) ?>/users/<?= (int)$u['id'] ?>">View</a>
                    <a class="btn btn-sm <?= $u['status'] === 'active' ? 'btn-danger' : 'btn-outline' ?>" href="<?= esc($base) ?>/users/toggle/<?= (int)$u['id'] ?>"><?= $u['status'] === 'active' ? 'Suspend' : 'Activate' ?></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php $pages = (int) ceil($total / $limit); if ($pages > 1): ?>
        <div class="pager">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= esc($base) ?>/users?page=<?= $i ?>&q=<?= urlencode($f['q']) ?>&role=<?= (int)$f['role'] ?>&status=<?= urlencode($f['status']) ?>" <?= $i === $page ? 'style="border-color:var(--brand);color:var(--brand)"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>