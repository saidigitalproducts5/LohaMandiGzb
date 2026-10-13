<?php
/** Seller review list + reply. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3>Reviews for your business</h3>
    <?php if (empty($rows)): ?>
        <div class="empty">No reviews yet. Reviews appear once a delivered order is reviewed and approved.</div>
    <?php else: ?>
        <?php foreach ($rows as $r): ?>
            <div style="border:1px solid var(--line);border-radius:12px;padding:16px;margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <b><?= esc($r['buyer_name'] ?? 'Buyer') ?></b>
                    <div style="color:#f59e0b;font-size:16px"><?= str_repeat('★', (int)$r['rating']) ?><?= str_repeat('☆', 5 - (int)$r['rating']) ?></div>
                </div>
                <div class="muted" style="font-size:12px;margin:4px 0 10px">
                    <?= esc(date('d M Y', strtotime($r['created_at']))) ?> ·
                    <span class="badge <?= $r['status'] === 'approved' ? 'green' : 'amber' ?>"><?= esc(ucfirst($r['status'])) ?></span>
                </div>
                <p style="margin-bottom:8px"><?= nl2br(esc($r['review'] ?? '')) ?></p>

                <?php if ($r['seller_reply']): ?>
                    <div style="background:#f3f4f6;border-radius:10px;padding:12px;margin-top:8px">
                        <b style="font-size:12.5px">Your reply</b>
                        <p style="margin-top:4px;font-size:13.5px"><?= nl2br(esc($r['seller_reply'])) ?></p>
                    </div>
                <?php else: ?>
                    <form method="post" action="<?= esc($base) ?>/account/reviews/<?= (int)$r['id'] ?>/reply" style="margin-top:10px">
                        <input type="text" name="seller_reply" placeholder="Reply to this review…" style="width:calc(100% - 100px);display:inline-block">
                        <button class="btn btn-primary btn-sm" type="submit">Reply</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>