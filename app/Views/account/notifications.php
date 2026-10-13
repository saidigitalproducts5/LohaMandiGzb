<?php
/** Notification center. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <div class="toolbar">
        <h3 style="margin:0">Notifications</h3>
        <?php if (!empty($rows)): ?>
            <form method="post" action="<?= esc($base) ?>/account/notifications/mark-all-read">
                <button class="btn btn-sm btn-outline" type="submit">Mark all as read</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($rows)): ?>
        <div class="empty">No notifications yet.</div>
    <?php else: ?>
        <?php foreach ($rows as $n): ?>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;border:1px solid var(--line);border-radius:10px;padding:13px 15px;margin-bottom:10px;<?= $n['is_read'] ? '' : 'background:#fff7f7;border-color:#f3c1c5' ?>">
                <div>
                    <b style="font-size:14px"><?= esc($n['title']) ?></b>
                    <?php if ($n['body']): ?><p class="muted" style="margin-top:3px;font-size:13px"><?= esc($n['body']) ?></p><?php endif; ?>
                    <div class="muted" style="font-size:11.5px;margin-top:6px">
                        <?= esc(date('d M Y, h:i A', strtotime($n['created_at']))) ?>
                        <?php if ($n['ref_type']): ?> · <span class="pill"><?= esc($n['ref_type']) ?></span><?php endif; ?>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-shrink:0">
                    <?php if (!$n['is_read']): ?>
                        <a class="btn btn-sm" href="<?= esc($base) ?>/account/notifications/<?= (int)$n['id'] ?>/read">Mark read</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>