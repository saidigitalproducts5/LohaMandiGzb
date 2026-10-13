<?php
/** CRM dashboard — pipeline board + upcoming items. */
$base = rtrim(base_url(), '/');
$stageColor = [
    'prospecting' => 'gray', 'qualified' => 'blue', 'quotation' => 'amber',
    'negotiation' => 'amber', 'won' => 'green', 'lost' => 'red',
];
?>
<div class="grid">
    <div class="stat"><b><?= (int)$customerCount ?></b><span>Customers</span></div>
    <div class="stat"><b>₹<?= number_format((float)$pipelineValue) ?></b><span>Pipeline Value</span></div>
    <div class="stat"><b>₹<?= number_format((float)$wonValue) ?></b><span>Won to Date</span></div>
    <div class="stat"><b><?= (int)$openTaskCount ?></b><span>Open Tasks</span></div>
    <div class="stat"><b><?= (int)$followupCount ?></b><span>Due Follow-ups</span></div>
    <div class="stat"><b><?= (int)$meetingCount ?></b><span>Upcoming Meetings</span></div>
</div>

<div class="card">
    <div class="toolbar">
        <h3 style="margin:0">Sales Pipeline</h3>
        <div>
            <a class="btn btn-primary btn-sm" href="<?= esc($base) ?>/account/crm/deals">All Deals</a>
            <a class="btn btn-sm" href="<?= esc($base) ?>/account/crm/deal/new">+ New Deal</a>
        </div>
    </div>
    <?php if (count(array_merge(...array_values($board))) === 0): ?>
        <div class="empty">No deals yet. Create your first deal to start tracking the pipeline.</div>
    <?php endif; ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px">
        <?php foreach ($stages as $key => $label):
            $cards = $board[$key] ?? [];
            $total = array_sum(array_column($cards, 'value'));
        ?>
            <div style="background:#f9fafb;border:1px solid var(--line);border-radius:10px;padding:12px;min-height:90px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                    <b style="font-size:13px"><?= esc($label) ?></b>
                    <span class="badge gray"><?= count($cards) ?> · ₹<?= number_format((float)$total) ?></span>
                </div>
                <?php foreach ($cards as $d): ?>
                    <div style="background:#fff;border:1px solid var(--line);border-left:3px solid <?= $key === 'won' ? '#16a34a' : ($key === 'lost' ? '#dc2626' : 'var(--brand)') ?>;border-radius:8px;padding:10px;margin-bottom:8px">
                        <div style="font-weight:700;font-size:13px"><?= esc($d['title']) ?></div>
                        <div class="muted" style="font-size:12px"><?= esc($d['customer_name'] ?? '—') ?></div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px">
                            <b style="font-size:13px">₹<?= number_format((float)$d['value']) ?></b>
                            <form method="post" action="<?= esc($base) ?>/account/crm/deals/<?= (int)$d['id'] ?>/stage" style="display:flex;gap:4px">
                                <select name="stage" onchange="this.form.submit()" style="width:auto;font-size:12px;padding:4px 6px">
                                    <?php foreach ($stages as $k => $l): ?>
                                        <option value="<?= $k ?>" <?= $d['stage'] === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <h3>Needs Attention</h3>
    <table>
        <thead><tr><th>Type</th><th>Item</th><th>When</th></tr></thead>
        <tbody>
        <?php $any = false; foreach ($followups as $f):
            if ($f['status'] !== 'pending') continue; $any = true; ?>
            <tr>
                <td><span class="badge amber">Follow-up</span></td>
                <td><b><?= esc($f['note'] ?: ($f['customer_name'] ?? 'Follow-up')) ?></b>
                    <span class="muted"> · <?= esc($f['customer_name'] ?? 'General') ?></span>
                    <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/followups/<?= (int)$f['id'] ?>/complete" style="margin-left:8px">✓ Done</a></td>
                <td class="mono"><?= $f['scheduled_at'] ? date('M j, H:i', strtotime($f['scheduled_at'])) : '—' ?></td>
            </tr>
        <?php endforeach;
        foreach ($meetings as $m): $any = true; ?>
            <tr>
                <td><span class="badge blue">Meeting</span></td>
                <td><b><?= esc($m['title']) ?></b>
                    <span class="muted"> · <?= esc($m['customer_name'] ?? 'General') ?></span></td>
                <td class="mono"><?= $m['starts_at'] ? date('M j, H:i', strtotime($m['starts_at'])) : '—' ?></td>
            </tr>
        <?php endforeach;
        if (!$any): ?>
            <tr><td colspan="3" class="empty">Nothing scheduled — add a follow-up or meeting.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>