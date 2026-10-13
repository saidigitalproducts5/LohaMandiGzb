<?php
/** New chat — pick a seller to start chatting with (buyer or seller). */
$base = rtrim(base_url(), '/');
$isSeller = $isSeller ?? false;
?>
<div class="toolbar">
    <a class="btn btn-outline" href="<?= esc($base) ?>/account/chats">← Back to Messages</a>
</div>
<div class="card">
    <h3>Start a New Chat</h3>
    <p class="muted" style="margin-bottom:14px">
        <?= $isSeller
            ? 'Pick a community seller to start a conversation with.'
            : 'Pick a seller to message. You can also start a chat from any seller storefront or product.' ?>
    </p>
    <?php if (empty($sellers)): ?>
        <div class="empty">No active sellers yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Seller</th><th>City</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($sellers as $s): ?>
                <tr>
                    <td><b><?= esc($s['business_name']) ?></b></td>
                    <td class="muted"><?= esc($s['city'] ?? '-') ?></td>
                    <td><a class="btn btn-primary btn-sm" href="<?= esc($base) ?>/account/chats/open/<?= (int)$s['id'] ?>">Chat</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>