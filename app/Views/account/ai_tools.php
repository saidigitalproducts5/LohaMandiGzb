<?php
/** AI product tools: description + keywords. */
$base = rtrim(base_url(), '/');
?>
<div class="toolbar">
    <div>
        <h3>AI Tools — <?= esc($product['name']) ?></h3>
        <p class="muted">Offline helper generated from product attributes. Copy and paste into your listing.</p>
    </div>
    <a class="btn" href="<?= esc($base) ?>/account/ai">← Back to AI Tools</a>
</div>

<div class="card">
    <h3>AI Product Description</h3>
    <div style="white-space:pre-wrap;background:#f9fafb;border:1px solid var(--line);border-radius:8px;padding:16px;margin-bottom:12px"><?= nl2br(esc($description)) ?></div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/ai/tools/<?= (int)$product['id'] ?>/copy?what=description">Copy Description</a>
</div>

<div class="card">
    <h3>AI Keywords</h3>
    <div style="margin-bottom:12px">
        <?php foreach ($keywords as $k): ?>
            <span class="pill" style="background:#eff6ff;color:#1d4ed8;margin:4px;display:inline-block"><?= esc($k) ?></span>
        <?php endforeach; ?>
        <?php if (empty($keywords)): ?><span class="muted">No keywords extracted.</span><?php endif; ?>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/ai/tools/<?= (int)$product['id'] ?>/copy?what=keywords">Copy Keywords</a>
</div>

<?php if (!empty($dupCheck)): ?>
<div class="card">
    <h3>Similar listings detected</h3>
    <table>
        <tr><th>Similar to</th><th>Similarity</th></tr>
        <?php foreach ($dupCheck as $d): ?>
            <tr><td><?= esc($d['name']) ?></td><td><span class="badge amber"><?= round($d['sim'] * 100) ?>%</span></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>