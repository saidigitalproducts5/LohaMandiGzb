<?php
/** CRM meetings list + schedule. */
$base = rtrim(base_url(), '/');
$statusColor = ['scheduled' => 'blue', 'completed' => 'green', 'cancelled' => 'red'];
?>
<div class="card">
    <h3>Schedule Meeting</h3>
    <form method="post" action="<?= esc($base) ?>/account/crm/meetings">
        <div class="row">
            <div style="min-width:260px"><label>Title *</label><input type="text" name="title" required></div>
            <div><label>Starts</label><input type="datetime-local" name="starts_at" required></div>
            <div><label>Ends</label><input type="datetime-local" name="ends_at"></div>
        </div>
        <div class="row">
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
            <div><label>Location</label><input type="text" name="location" placeholder="Office / Video call / Phone"></div>
        </div>
        <div class="row">
            <div><label>Notes</label><input type="text" name="notes" placeholder="Agenda"></div>
        </div>
        <button class="btn btn-primary" type="submit">Schedule Meeting</button>
    </form>
</div>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/account/crm/meetings">
            <select name="status">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= ($status ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>
    <table>
        <thead><tr><th>Meeting</th><th>Customer / Deal</th><th>When</th><th>Location</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($meetings)): ?>
            <tr><td colspan="6" class="empty">No meetings.</td></tr>
        <?php else: foreach ($meetings as $m): ?>
            <tr>
                <td><b><?= esc($m['title']) ?></b></td>
                <td class="muted" style="font-size:12.5px">
                    <?= esc($m['customer_name'] ?? '') ?><?= ($m['customer_name'] && $m['deal_title']) ? ' → ' : '' ?><?= esc($m['deal_title'] ?? '') ?></td>
                <td class="mono"><?= $m['starts_at'] ? date('M j, Y H:i', strtotime($m['starts_at'])) : '—' ?></td>
                <td><?= esc($m['location'] ?? '—') ?></td>
                <td><span class="badge <?= $statusColor[$m['status']] ?? 'gray' ?>"><?= esc($m['status']) ?></span></td>
                <td style="white-space:nowrap">
                    <?php if ($m['status'] === 'scheduled'): ?>
                        <form method="post" action="<?= esc($base) ?>/account/crm/meetings/<?= (int)$m['id'] ?>/status" style="display:inline-flex;gap:4px">
                            <select name="status" style="background:transparent;width:auto;font-size:12px;padding:4px 6px" onchange="this.form.submit()">
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?= $s ?>" <?= $m['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php endif; ?>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/crm/meetings/<?= (int)$m['id'] ?>/delete" onclick="return confirm('Delete meeting?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>