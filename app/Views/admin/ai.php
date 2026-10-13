<?php $base = rtrim(base_url('admin'), '/'); $tab = $tab ?? 'fraud'; ?>

<div class="card" style="padding:12px">
    <div style="display:flex;gap:8px">
        <a class="btn <?= $tab === 'fraud' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/ai?tab=fraud">Fraud Detection</a>
        <a class="btn <?= $tab === 'duplicates' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/ai?tab=duplicates">Duplicate Listings</a>
        <a class="btn <?= $tab === 'images' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/ai?tab=images">Image Enhancement</a>
    </div>
</div>

<?php if ($tab === 'fraud'): ?>
<div class="card">
    <h3>AI Fraud &amp; Risk Scan</h3>
    <p class="muted">Heuristic risk scores (GST/verification, ratings, price anomalies, duplicate listings). Only medium/high risk sellers shown.</p>
    <?php if (empty($sellers)): ?>
        <p class="empty">All active sellers are currently low-risk.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Seller</th><th>City</th><th>Products</th><th>Score</th><th>Risk</th><th>Flags</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($sellers as $s): ?>
                <tr>
                    <td><b><?= esc($s['seller']['business_name']) ?></b><br><span class="muted mono"><?= esc($s['seller']['gstin'] ?? 'no GST') ?></span></td>
                    <td><?= esc($s['seller']['city'] ?? '-') ?></td>
                    <td><?= (int)$s['products'] ?></td>
                    <td><b><?= (int)$s['result']['score'] ?>/100</b></td>
                    <td><span class="badge <?= $s['result']['risk'] === 'high' ? 'red' : 'amber' ?>"><?= strtoupper($s['result']['risk']) ?></span></td>
                    <td>
                        <?php foreach (array_slice($s['result']['flags'], 0, 3) as $f): ?>
                            <div class="muted" style="font-size:12px">• <?= esc($f) ?></div>
                        <?php endforeach; ?>
                    </td>
                    <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/sellers/<?= (int)$s['seller']['id'] ?>">Review</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($tab === 'duplicates'): ?>
<div class="card">
    <h3>Duplicate Listing Detection</h3>
    <p class="muted">Normalized name-similarity pairs across all products (threshold ≥ 70%).</p>
    <?php if (empty($pairs)): ?>
        <p class="empty">No duplicate pairs detected.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Listing A</th><th>Listing B</th><th>Similarity</th></tr></thead>
            <tbody>
            <?php foreach ($pairs as $p): ?>
                <tr>
                    <td><?= esc($p['name_a']) ?></td>
                    <td><?= esc($p['name_b']) ?></td>
                    <td><span class="badge <?= $p['sim'] >= 0.9 ? 'red' : 'amber' ?>"><?= round($p['sim'] * 100) ?>%</span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($tab === 'images'): ?>
<div class="card">
    <h3>AI Image Enhancement</h3>
    <p class="muted">Offline PHP-GD optimizer: auto-resize to 1200px, unsharp mask, recompress (JPEG q85 / PNG-6).</p>
    <div style="margin:12px 0">
        <a class="btn btn-primary" href="<?= esc($base) ?>/ai/enhance-all" onclick="return confirm('Enhance all product images?')">⚡ Enhance All</a>
    </div>
    <?php if (empty($images)): ?>
        <p class="empty">No product images in the uploads folder.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>File</th><th>Size</th><th>Modified</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($images as $img): ?>
                <tr>
                    <td class="mono"><?= esc($img['name']) ?></td>
                    <td><?= round($img['size'] / 1024) ?> KB</td>
                    <td class="muted"><?= esc($img['mtime']) ?></td>
                    <td><a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/ai/enhance?f=<?= urlencode($img['name']) ?>">Enhance</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>