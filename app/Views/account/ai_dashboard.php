<?php
/** AI Tools dashboard (seller). */
$base = rtrim(base_url(), '/');
$riskBadge = ['low' => 'green', 'medium' => 'amber', 'high' => 'red'];
?>
<div class="toolbar">
    <div>
        <h3>AI Tools</h3>
        <p class="muted">Offline intelligent helpers for your catalog: descriptions, keywords, duplicate detection, and a chat assistant.</p>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/ai/chat">💬 AI Chat Assistant</a>
</div>

<div class="card">
    <h3>Your Seller Risk Profile</h3>
    <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap">
        <div class="stat" style="flex:0 0 160px"><b><?= (int)$fraud['score'] ?>/100</b><span>Risk Score</span></div>
        <div>
            <div>Risk level: <span class="badge <?= $riskBadge[$fraud['risk']] ?? 'gray' ?>"><?= strtoupper($fraud['risk']) ?></span></div>
            <ul style="margin:8px 0 0 18px">
                <?php foreach ($fraud['flags'] as $f): ?><li class="muted"><?= esc($f) ?></li><?php endforeach; ?>
                <?php if (empty($fraud['flags'])): ?><li class="muted">No red flags — listing health looks good.</li><?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php if (!empty($dups)): ?>
<div class="card">
    <h3>Duplicate Listing Alerts</h3>
    <table>
        <tr><th>Listing</th><th>Similar to</th><th>Similarity</th></tr>
        <?php foreach ($dups as $d): ?>
            <tr><td><?= esc($d['name_a']) ?></td><td><?= esc($d['name_b']) ?></td><td><span class="badge red"><?= round($d['sim'] * 100) ?>%</span></td></tr>
        <?php endforeach; ?>
    </table>
    <p class="muted" style="margin-top:8px">Consolidate near-identical listings to keep your catalog clean and searchable.</p>
</div>
<?php endif; ?>

<div class="card">
    <h3>Generate Content with AI</h3>
    <?php if (empty($products)): ?>
        <p class="empty">Add products first — AI descriptions & keywords work on your existing listings.</p>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px">
            <?php foreach ($products as $p): ?>
                <div style="border:1px solid var(--line);border-radius:10px;padding:14px">
                    <b style="display:block;margin-bottom:4px"><?= esc($p['name']) ?></b>
                    <span class="muted" style="font-size:12px"></span><?= esc($p['category_name'] ?? '') ?><?= !empty($p['grade']) ? ' · ' . esc($p['grade']) : '' ?>
                    <div style="margin-top:10px">
                        <a class="btn btn-sm btn-primary" href="<?= esc($base) ?>/account/ai/tools/<?= (int)$p['id'] ?>">Generate</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>