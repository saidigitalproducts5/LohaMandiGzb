<?php
/** CRM customer detail — contact, deals, tasks, follow-ups, meetings, notes. */
$base = rtrim(base_url(), '/');
$stageColor = ['prospecting' => 'gray', 'qualified' => 'blue', 'quotation' => 'amber', 'negotiation' => 'amber', 'won' => 'green', 'lost' => 'red'];
?>
<div class="card">
    <div class="toolbar" style="align-items:center">
        <div>
            <h3 style="margin:0"><?= esc($customer['name']) ?><?= $customer['company'] ? ' — ' . esc($customer['company']) : '' ?></h3>
            <div class="muted" style="font-size:12.5px;margin-top:4px">
                <?= $customer['mobile'] ? esc($customer['mobile']) . ' · ' : '' ?><?= $customer['email'] ? esc($customer['email']) . ' · ' : '' ?><?= esc($customer['city'] ?? '') ?>
            </div>
        </div>
        <div style="white-space:nowrap">
            <a class="btn btn-sm" href="<?= esc($base) ?>/account/crm/customers/<?= (int)$customer['id'] ?>/edit">Edit</a>
            <a class="btn btn-sm btn-primary" href="<?= esc($base) ?>/account/crm/deal/new?customer_id=<?= (int)$customer['id'] ?>">+ New Deal</a>
        </div>
    </div>
</div>

<div class="grid">
    <div class="stat"><b><?= count($deals) ?></b><span>Deals</span></div>
    <div class="stat"><b>₹<?= number_format((float)$totalWon) ?></b><span>Won Value</span></div>
    <div class="stat"><b><?= count($tasks) ?></b><span>Tasks</span></div>
    <div class="stat"><b><?= count($followups) ?></b><span>Follow-ups</span></div>
</div>

<div class="card">
    <h3>Deals</h3>
    <?php if (empty($deals)): ?>
        <div class="empty">No deals for this customer yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Deal</th><th>Stage</th><th>Value</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($deals as $d): ?>
                <tr>
                    <td><b><?= esc($d['title']) ?></b></td>
                    <td><span class="badge <?= $stageColor[$d['stage']] ?? 'gray' ?>"><?= esc($stages[$d['stage']] ?? $d['stage']) ?></span></td>
                    <td class="mono">₹<?= number_format((float)$d['value']) ?></td>
                    <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/deals/<?= (int)$d['id'] ?>/edit">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Tasks</h3>
    <table>
        <thead><tr><th>Task</th><th>Due</th><th>Priority</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($tasks)): ?>
            <tr><td colspan="4" class="empty">No tasks.</td></tr>
        <?php else: foreach ($tasks as $t): ?>
            <tr>
                <td><b><?= esc($t['title']) ?></b><span class="muted"> · <?= esc($t['category']) ?></span></td>
                <td class="mono"><?= $t['due_at'] ? date('M j, Y', strtotime($t['due_at'])) : '—' ?></td>
                <td><span class="badge <?= $t['priority'] === 'high' ? 'red' : ($t['priority'] === 'low' ? 'gray' : 'amber') ?>"><?= esc($t['priority']) ?></span></td>
                <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/tasks/<?= (int)$t['id'] ?>/toggle"><?= $t['status'] === 'done' ? 'Reopen' : '✓ Done' ?></a></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Follow-ups</h3>
    <table>
        <thead><tr><th>Channel</th><th>Scheduled</th><th>Note</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($followups)): ?>
            <tr><td colspan="4" class="empty">No follow-ups.</td></tr>
        <?php else: foreach ($followups as $f): ?>
            <tr>
                <td><span class="badge gray"><?= esc($f['channel']) ?></span></td>
                <td class="mono"><?= $f['scheduled_at'] ? date('M j, H:i', strtotime($f['scheduled_at'])) : '—' ?></td>
                <td class="muted"><?= esc($f['note'] ?? '') ?></td>
                <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/followups/<?= (int)$f['id'] ?>/complete">✓ Done</a></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Meetings</h3>
    <table>
        <thead><tr><th>Meeting</th><th>When</th><th>Location</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($meetings)): ?>
            <tr><td colspan="4" class="empty">No meetings.</td></tr>
        <?php else: foreach ($meetings as $m): ?>
            <tr>
                <td><b><?= esc($m['title']) ?></b></td>
                <td class="mono"><?= $m['starts_at'] ? date('M j, H:i', strtotime($m['starts_at'])) : '—' ?></td>
                <td><?= esc($m['location'] ?? '—') ?></td>
                <td><span class="badge <?= $m['status'] === 'completed' ? 'green' : ($m['status'] === 'cancelled' ? 'red' : 'blue') ?>"><?= esc($m['status']) ?></span></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Notes</h3>
    <form method="post" action="<?= esc($base) ?>/account/crm/notes/new" style="margin-bottom:16px">
        <input type="hidden" name="customer_id" value="<?= (int)$customer['id'] ?>">
        <textarea name="body" rows="3" placeholder="Add a note about this customer…" required></textarea>
        <button class="btn btn-primary" type="submit" style="margin-top:8px">Add Note</button>
    </form>
    <?php if (empty($notes)): ?>
        <div class="empty">No notes yet.</div>
    <?php else: foreach ($notes as $n): ?>
        <div style="background:#f9fafb;border:1px solid var(--line);border-radius:8px;padding:12px;margin-bottom:8px">
            <div class="muted mono" style="font-size:12px;margin-bottom:4px"><?= date('M j, Y H:i', strtotime($n['created_at'])) ?>
                <a class="btn btn-sm btn-danger" style="padding:2px 8px" href="<?= esc($base) ?>/account/crm/notes/<?= (int)$n['id'] ?>/delete" onclick="return confirm('Delete note?')">✕</a>
            </div>
            <div><?= nl2br(esc($n['body'])) ?></div>
        </div>
    <?php endforeach; endif; ?>
</div>