<?php
/** CRM tasks list + add. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3>Add Task</h3>
    <form method="post" action="<?= esc($base) ?>/account/crm/tasks">
        <div class="row">
            <div style="min-width:260px"><label>Title *</label><input type="text" name="title" required></div>
            <div><label>Category</label>
                <select name="category">
                    <?php foreach ($categories as $c): ?><option value="<?= $c ?>"><?= ucfirst(str_replace('_', ' ', $c)) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label>Priority</label>
                <select name="priority">
                    <?php foreach ($priorities as $p): ?><option value="<?= $p ?>"><?= ucfirst($p) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label>Due</label><input type="datetime-local" name="due_at"></div>
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
        </div>
        <button class="btn btn-primary" type="submit">Add Task</button>
    </form>
</div>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/account/crm/tasks">
            <select name="status">
                <option value="">All statuses</option>
                <option value="open" <?= ($status ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                <option value="done" <?= ($status ?? '') === 'done' ? 'selected' : '' ?>>Done</option>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>
    <table>
        <thead><tr><th>Task</th><th>Customer / Deal</th><th>Due</th><th>Priority</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($tasks)): ?>
            <tr><td colspan="6" class="empty">No tasks.</td></tr>
        <?php else: foreach ($tasks as $t): ?>
            <tr>
                <td><b><?= esc($t['title']) ?></b><span class="muted"> · <?= esc($t['category']) ?></span></td>
                <td class="muted" style="font-size:12.5px">
                    <?= esc($t['customer_name'] ?? '') ?><?= ($t['customer_name'] && $t['deal_title']) ? ' → ' : '' ?><?= esc($t['deal_title'] ?? '') ?></td>
                <td class="mono"><?= $t['due_at'] ? date('M j, Y H:i', strtotime($t['due_at'])) : '—' ?></td>
                <td><span class="badge <?= $t['priority'] === 'high' ? 'red' : ($t['priority'] === 'low' ? 'gray' : 'amber') ?>"><?= esc($t['priority']) ?></span></td>
                <td><span class="badge <?= $t['status'] === 'done' ? 'green' : 'blue' ?>"><?= esc($t['status']) ?></span></td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/tasks/<?= (int)$t['id'] ?>/toggle"><?= $t['status'] === 'done' ? 'Reopen' : '✓ Done' ?></a>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/crm/tasks/<?= (int)$t['id'] ?>/delete" onclick="return confirm('Delete task?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>