<?php
/** Seller leads list view. */
$base = rtrim(base_url(), '/');
?>
<div class="grid">
    <div class="stat"><b><?= (int)$counts['new'] ?></b><span>New</span></div>
    <div class="stat"><b><?= (int)$counts['contacted'] ?></b><span>Contacted</span></div>
    <div class="stat"><b><?= (int)$counts['won'] ?></b><span>Won</span></div>
    <div class="stat"><b><?= (int)$counts['lost'] ?></b><span>Lost</span></div>
</div>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/account/leads">
            <select name="status">
                <option value="">All statuses</option>
                <?php foreach (['new', 'contacted', 'won', 'lost'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($status ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>

    <table>
        <thead><tr><th>Buyer</th><th>Mobile</th><th>Source</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($leads)): ?>
            <tr><td colspan="5" class="empty">No leads yet.</td></tr>
        <?php else: ?>
            <?php foreach ($leads as $l): ?>
                <tr>
                    <td>
                        <b><?= esc($l['name']) ?></b>
                        <?php if ($l['company']): ?><br><span class="muted"><?= esc($l['company']) ?></span><?php endif; ?>
                        <?php if ($l['message']): ?><br><span class="muted"><?= esc(mb_strimwidth($l['message'], 0, 60, '…')) ?></span><?php endif; ?>
                    </td>
                    <td class="mono"><?= esc($l['mobile']) ?></td>
                    <td class="muted"><?= esc($l['lead_type'] ?? '-') ?></td>
                    <td><span class="badge <?= $l['status'] === 'new' ? 'blue' : ($l['status'] === 'won' ? 'green' : ($l['status'] === 'lost' ? 'red' : 'amber')) ?>"><?= esc(ucfirst($l['status'])) ?></span></td>
                    <td>
                        <form method="post" action="<?= esc($base) ?>/account/leads/<?= (int)$l['id'] ?>/status" style="display:flex;gap:6px;align-items:center">
                            <select name="status" style="width:auto">
                                <?php foreach (['new', 'contacted', 'won', 'lost'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $l['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm" type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>