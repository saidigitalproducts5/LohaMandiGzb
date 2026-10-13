<?php
/** Chat thread view. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h3 style="margin:0">Chat with <?= esc($partyName) ?></h3>
        <a class="btn btn-sm" href="<?= esc($base) ?>/account/chats">← All chats</a>
    </div>

    <div id="msgBox" style="max-height:420px;overflow-y:auto;border:1px solid var(--line);border-radius:10px;padding:14px;display:flex;flex-direction:column;gap:10px;background:#fafafa">
        <?php if (empty($messages)): ?>
            <div class="empty" style="margin:auto">Say hello to start the conversation.</div>
        <?php endif; ?>
        <?php foreach ($messages as $m): ?>
            <?php $mine = (int)$m['sender_id'] === (int) session()->get('uid'); ?>
            <div style="max-width:72%;align-self:<?= $mine ? 'flex-end' : 'flex-start' ?>;background:<?= $mine ? 'var(--brand)' : '#fff' ?>;color:<?= $mine ? '#fff' : 'var(--ink)' ?>;padding:10px 13px;border-radius:12px;border:<?= $mine ? '0' : '1px solid var(--line)' ?>;font-size:13.5px">
                <?= nl2br(esc($m['body'] ?? '')) ?>
                <div style="font-size:10.5px;opacity:.75;margin-top:4px"><?= esc(date('d M, h:i A', strtotime($m['created_at']))) ?><?= $mine && $m['is_read'] ? ' · ✓✓' : '' ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" action="<?= esc($base) ?>/account/chats/<?= (int)$chat['id'] ?>" style="display:flex;gap:10px;margin-top:14px">
        <input type="text" name="message" placeholder="Type a message…" required autofocus style="flex:1">
        <button class="btn btn-primary" type="submit">Send</button>
    </form>
</div>
<script>
    const box = document.getElementById('msgBox');
    box.scrollTop = box.scrollHeight;
</script>