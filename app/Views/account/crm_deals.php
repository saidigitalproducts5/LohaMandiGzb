<?php
/** CRM deals list. */
$base = rtrim(base_url(), '/');
$stageColor = ['prospecting' => 'gray', 'qualified' => 'blue', 'quotation' => 'amber', 'negotiation' => 'amber', 'won' => 'green', 'lost' => 'red'];
?>
<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/account/crm/deals">
            <select name="stage">
                <option value="">All stages</option>
                <?php foreach ($stages as $k => $l): ?>
                    <option value="<?= $k ?>" <?= ($stage ?? '') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
        <a class="btn btn-primary" href="<?= esc($base) ?>/account/crm/deal/new">+ New Deal</a>
    </div>
    <table>
        <thead><tr><th>Deal</th><th>Customer</th><th>Stage</th><th>Value</th><th>Expected Close</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($deals)): ?>
            <tr><td colspan="6" class="empty">No deals found.</td></tr>
        <?php else: foreach ($deals as $d): ?>
            <tr>
                <td><b><?= esc($d['title']) ?></b></td>
                <td><?= esc($d['customer_name'] ?? '—') ?></td>
                <td><span class="badge <?= $stageColor[$d['stage']] ?? 'gray' ?>"><?= esc($stages[$d['stage']] ?? $d['stage']) ?></span></td>
                <td class="mono">₹<?= number_format((float)$d['value']) ?></td>
                <td class="mono"><?= $d['expected_close'] ? date('M j, Y', strtotime($d['expected_close'])) : '—' ?></td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/deals/<?= (int)$d['id'] ?>/edit">Edit</a>
                    <form method="post" action="<?= esc($base) ?>/account/crm/deals/<?= (int)$d['id'] ?>/stage" style="display:inline-flex;gap:4px">
                        <select name="stage" style="width:auto;font-size:12px;padding:4px 6px" onchange="this.form.submit()">
                            <?php foreach ($stages as $k => $l): ?>
                                <option value="<?= $k ?>" <?= $d['stage'] === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/crm/deals/<?= (int)$d['id'] ?>/delete" onclick="return confirm('Delete deal?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>