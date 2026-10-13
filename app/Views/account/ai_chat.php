<?php
/** AI Chat Assistant (seller). */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <h3>💬 AI Chat Assistant</h3>
    <p class="muted" style="margin-bottom:14px">Ask about pricing, products, payments, delivery, GST, or registrations. The assistant answers instantly from the platform's knowledge base.</p>

    <div id="chat-log" style="background:#f9fafb;border:1px solid var(--line);border-radius:10px;padding:16px;min-height:220px;max-height:420px;overflow-y:auto;margin-bottom:14px">
        <?php if (empty($conversation)): ?>
            <div class="muted">Say "hello" to start, or ask e.g. “How do I become a buyer?”</div>
        <?php else: ?>
            <?php foreach ($conversation as $c): ?>
                <div style="margin-bottom:12px" class="chat-row">
                    <div style="display:flex;justify-content:flex-end"><div style="background:var(--brand);color:#fff;border-radius:10px;padding:8px 12px;max-width:70%"><?= esc($c['user']) ?></div></div>
                    <div style="display:flex;margin-top:6px"><div style="background:#fff;border:1px solid var(--line);border-radius:10px;padding:8px 12px;max-width:75%">🤖 <?= esc($c['ai']) ?></div></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form method="post" action="<?= esc($base) ?>/account/ai/chat" class="ai-chat-form" style="display:flex;gap:10px">
        <input type="text" name="message" placeholder="Ask the assistant…" required autofocus style="flex:1">
        <button class="btn btn-primary" type="submit">Send</button>
    </form>
</div>
<script>
const logEl = document.getElementById('chat-log');
logEl.scrollTop = logEl.scrollHeight;
</script>