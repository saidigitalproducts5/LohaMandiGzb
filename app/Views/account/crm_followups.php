<?php
/** CRM follow-ups list + schedule. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3>Schedule Follow-up</h3>
    <form method="post" action="<?= esc($base) ?>/account/crm/followups">
        <div class="row">
            <div><label>Channel</label>
                <select name="channel">
                    <?php foreach ($channels as $ch): ?><option value="<?= $ch ?>"><?= ucfirst($ch) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label>Scheduled At</label><input type="datetime-local" name="scheduled_at" required></div>
            <div><label>Customer</label>
                <select name="customer_id"><option value="0">— None —</option>
                    <?php foreach ($customers as $c): ?><option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label>Deal</label>
                <select name="deal_id"><option value="0">— None —</option>
                    <?php foreach ($deals as $d): ?><option value="<?= (int)$d['id'] ?>"><?= esc($d['title']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div><label>Note</label><input type="text" name="note" placeholder="e.g. Follow up on quotation"></div>
        </div>
        <button class="btn btn-primary" type="submit">Schedule</button>
    </form>
</div>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/account/crm/followups">
            <select name="status">
                <option value="">All</option>
                <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="done" <?= ($status ?? '') === 'done' ? 'selected' : '' ?>>Done</option>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>
    <table>
        <thead><tr><th>Channel</th><th>Customer / Deal</th><th>Scheduled</th><th>Note</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($followups)): ?>
            <tr><td colspan="6" class="empty">No follow-ups.</td></tr>
        <?php else: foreach ($followups as $f): ?>
            <tr>
                <td><span class="badge gray"><?= esc($f['channel']) ?></span></td>
                <td class="muted" style="font-size:12.5px">
                    <?= esc($f['customer_name'] ?? '') ?><?= ($f['customer_name'] && $f['deal_title']) ? ' → ' : '' ?><?= esc($f['deal_title'] ?? '') ?></td>
                <td class="mono"><?= $f['scheduled_at'] ? date('M j, Y H:i', strtotime($f['scheduled_at'])) : '—' ?>
                    <?php if ($f['status'] === 'pending' && strtotime($f['scheduled_at']) < time()): ?><br><span class="badge red">overdue</span><?php endif; ?></td>
                <td class="muted"><?= esc($f['note'] ?? '') ?></td>
                <td><span class="badge <?= $f['status'] === 'done' ? 'green' : 'amber' ?>"><?= esc($f['status']) ?></span></td>
                <td style="white-space:nowrap">
                    <?php if ($f['status'] === 'pending'): ?>
                        <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/followups/<?= (int)$f['id'] ?>/complete">✓ Done</a>
                    <?php endif; ?>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/crm/followups/<?= (int)$f['id'] ?>/delete" onclick="return confirm('Delete follow-up?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>