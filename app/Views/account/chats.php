<?php
/** Chat inbox — buyer or seller. Sellers get a Start Chat dropdown of community sellers. */
$base = rtrim(base_url(), '/');
$pickSellers = $pickSellers ?? [];
?>
<?php if ($isSeller): ?>
<style>
    .sc-wrap{position:relative;display:inline-block}
    .sc-menu{display:none;position:absolute;top:calc(100% + 6px);left:0;min-width:300px;max-width:min(360px,90vw);background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 14px 40px rgba(0,0,0,.16);z-index:40;overflow:hidden}
    .sc-menu.open{display:block}
    .sc-menu input{width:100%;border:0;border-bottom:1px solid var(--line);border-radius:0;padding:11px 13px;font-size:13.5px}
    .sc-list{max-height:300px;overflow-y:auto}
    .sc-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 13px;font-size:13.5px;border-bottom:1px solid var(--line);transition:.12s}
    .sc-item:last-child{border-bottom:0}
    .sc-item:hover{background:#fff5f5;color:var(--brand)}
    .sc-item b{font-weight:700}
    .sc-item span{color:var(--muted);font-size:12px;white-space:nowrap}
    .sc-empty{padding:16px 13px;color:var(--muted);font-size:13px;text-align:center}
</style>
<?php endif; ?>

<div class="toolbar">
    <?php if ($isSeller): ?>
        <div class="sc-wrap">
            <button class="btn btn-primary" type="button" id="startChatBtn" onclick="toggleStartChat(event)">Start Chat ▾</button>
            <div class="sc-menu" id="startChatMenu">
                <input type="text" id="startChatSearch" placeholder="Search business name…" autocomplete="off"
                       oninput="filterStartChat()" onclick="event.stopPropagation()">
                <div class="sc-list" id="startChatList">
                    <?php foreach ($pickSellers as $ps): ?>
                        <a class="sc-item" href="<?= esc($base) ?>/account/chats/open/<?= (int) $ps['id'] ?>"
                           data-name="<?= esc(strtolower($ps['business_name'] . ' ' . ($ps['city'] ?? ''))) ?>">
                            <b><?= esc($ps['business_name']) ?></b>
                            <span><?= esc($ps['city'] ?? '') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($pickSellers)): ?>
                    <div class="sc-empty">No other sellers available yet.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <a class="btn btn-primary" href="<?= esc($base) ?>/account/chats/new">+ New Chat</a>
    <?php endif; ?>
</div>
<div class="card">
    <h3>Messages</h3>
    <?php if (empty($rows)): ?>
        <div class="empty">No conversations yet.<?= $isSeller ? ' Click “Start Chat” to message another seller.' : ' Open a chat from a seller storefront or product.' ?></div>
    <?php else: ?>
        <table>
            <thead><tr><th><?= $isSeller ? 'Chat With' : 'Seller' ?></th><th>Last Message</th><th>Unread</th><th>Last Activity</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $c): ?>
                <tr>
                    <td>
                        <b><?= esc($c['party_name'] ?? 'Unknown') ?></b>
                        <?php if (!empty($c['party_is_seller'])): ?><span class="badge blue">Seller</span><?php endif; ?>
                    </td>
                    <td class="muted"><?= esc($c['last_message'] ?? 'No messages yet') ?></td>
                    <td>
                        <?php if ((int)$c['unread_count'] > 0): ?>
                            <span class="badge green"><?= (int)$c['unread_count'] ?> new</span>
                        <?php else: ?>
                            <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="muted"><?= $c['last_message_at'] ? esc(date('d M Y, h:i A', strtotime($c['last_message_at']))) : '-' ?></td>
                    <td><a class="btn btn-primary btn-sm" href="<?= esc($base) ?>/account/chats/<?= (int)$c['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php if ($isSeller): ?>
<script>
(function(){
    var btn = document.getElementById('startChatBtn');
    var menu = document.getElementById('startChatMenu');
    var search = document.getElementById('startChatSearch');

    window.toggleStartChat = function(e){
        if (e) { e.stopPropagation(); }
        menu.classList.toggle('open');
        if (menu.classList.contains('open')) {
            search.value = '';
            filterStartChat();
            setTimeout(function(){ search.focus(); }, 30);
        }
    };

    window.filterStartChat = function(){
        var q = (search.value || '').toLowerCase().trim();
        var items = document.querySelectorAll('#startChatList .sc-item');
        var shown = 0;
        items.forEach(function(el){
            var match = !q || (el.getAttribute('data-name') || '').indexOf(q) !== -1;
            el.style.display = match ? '' : 'none';
            if (match) { shown++; }
        });
        var empty = document.querySelector('#startChatMenu .sc-empty');
        if (empty) { empty.style.display = shown ? 'none' : ''; }
    };

    // Close when clicking outside.
    document.addEventListener('click', function(e){
        if (menu.classList.contains('open') && !menu.contains(e.target)) {
            menu.classList.remove('open');
        }
    });
    // Keep menu open when clicking inside (except links navigate).
    menu.addEventListener('click', function(e){ e.stopPropagation(); });
})();
</script>
<?php endif; ?>
