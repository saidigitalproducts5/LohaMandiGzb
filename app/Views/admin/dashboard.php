<?php $base = rtrim(base_url('admin'), '/'); ?>
<?php $waStats = $waStats ?? ['total' => 0, 'success' => 0, 'failed' => 0]; ?>
<?php $recentWaLogs = $recentWaLogs ?? []; ?>

<div class="grid">
    <div class="stat"><b><?= number_format($stats['users']) ?></b><span>Total Users</span></div>
    <div class="stat"><b><?= number_format($stats['buyers']) ?></b><span>Buyers</span></div>
    <div class="stat"><b><?= number_format($stats['sellers']) ?></b><span>Sellers</span></div>
    <div class="stat"><b><?= number_format($stats['products']) ?></b><span>Products</span></div>
    <div class="stat"><b><?= number_format($stats['pending_products']) ?></b><span>Pending Products</span></div>
    <div class="stat"><b><?= number_format($stats['pending_sellers']) ?></b><span>Pending Sellers</span></div>
    <div class="stat"><b><?= number_format($stats['rfqs']) ?></b><span>RFQs</span></div>
    <div class="stat"><b><?= number_format($stats['leads']) ?></b><span>Leads</span></div>
    <div class="stat"><b><?= number_format($stats['orders']) ?></b><span>Orders</span></div>
    <div class="stat"><b>₹<?= number_format($stats['revenue'], 0) ?></b><span>Revenue</span></div>
    <div class="stat"><b><?= number_format($stats['premium']) ?></b><span>Premium Sellers</span></div>
    <div class="stat"><b><?= number_format($stats['ads']) ?></b><span>Active Ads</span></div>
    <div class="stat"><b><?= number_format($waStats['total']) ?></b><span>WA Messages</span></div>
    <div class="stat"><b><?= number_format($waStats['success']) ?></b><span>WA Sent OK</span></div>
    <div class="stat"><b><?= number_format($waStats['failed']) ?></b><span>WA Failed</span></div>
</div>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>Recent Users</h3>
        <table>
            <thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($recentUsers)): ?>
                <tr><td colspan="3" class="empty">No users yet.</td></tr>
            <?php else: foreach ($recentUsers as $u): ?>
                <tr>
                    <td><?= esc($u['name']) ?><br><span class="muted mono"><?= esc($u['email']) ?></span></td>
                    <td><span class="pill"><?= esc($u['role_name'] ?? '—') ?></span></td>
                    <td>
                        <?php if ($u['status'] === 'active'): ?><span class="badge green">Active</span>
                        <?php elseif ($u['status'] === 'suspended'): ?><span class="badge red">Suspended</span>
                        <?php else: ?><span class="badge gray"><?= esc($u['status']) ?></span><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Recent Products</h3>
        <table>
            <thead><tr><th>Product</th><th>Seller</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($recentProducts)): ?>
                <tr><td colspan="3" class="empty">No products yet.</td></tr>
            <?php else: foreach ($recentProducts as $p): ?>
                <tr>
                    <td><?= esc($p['name']) ?></td>
                    <td class="muted"><?= esc($p['business_name'] ?? '—') ?></td>
                    <td>
                        <?php if ($p['status'] === 'approved'): ?><span class="badge green">Approved</span>
                        <?php elseif ($p['status'] === 'pending'): ?><span class="badge amber">Pending</span>
                        <?php else: ?><span class="badge red">Rejected</span><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
    </table>
    </div>
</div>

<div class="card">
    <h3>Moderation Queue</h3>
    <div class="toolbar" style="justify-content:flex-start">
        <a class="btn btn-primary" href="<?= esc($base) ?>/products?status=pending">Review <?= $stats['pending_products'] ?> pending product(s)</a>
        <a class="btn" href="<?= esc($base) ?>/sellers?status=pending">Review <?= $stats['pending_sellers'] ?> pending seller(s)</a>
    </div>
</div>

<div class="card" id="wa-logs">
    <h3>Recent WhatsApp Messages</h3>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Recipient</th><th>Trigger</th><th>Status</th><th>HTTP</th><th>Error Code</th><th>Error Message</th><th>API Response</th><th>Date</th><th>Time</th></tr></thead>
        <tbody>
        <?php if (empty($recentWaLogs)): ?>
            <tr><td colspan="9" class="empty">No messages yet.</td></tr>
        <?php else: foreach ($recentWaLogs as $log): ?>
            <?php $dt = strtotime($log['created_at'] ?? ''); ?>
            <tr>
                <td class="mono"><?= esc($log['phone']) ?></td>
                <td><span class="pill"><?= esc($log['trigger_source'] ?? '—') ?></span></td>
                <td>
                    <?php if ($log['send_status'] === 'success'): ?><span class="badge green">Sent</span>
                    <?php elseif ($log['send_status'] === 'failed'): ?><span class="badge red">Failed</span>
                    <?php else: ?><span class="badge gray"><?= esc($log['send_status']) ?></span><?php endif; ?>
                </td>
                <td class="mono"><?= esc($log['http_status'] ?? '—') ?></td>
                <td class="mono"><?= esc($log['error_code'] ?? '—') ?></td>
                <td style="max-width:200px;word-break:break-word;font-size:12px" class="muted"><?= esc($log['error_message'] ?? '—') ?></td>
                <td><button class="btn btn-sm" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'">View</button><pre style="display:none;max-width:300px;max-height:150px;overflow:auto;font-size:11px;background:#f9fafb;padding:8px;border-radius:6px;margin-top:6px;white-space:pre-wrap;word-break:break-all"><?= esc($log['api_response'] ?? '—') ?></pre></td>
                <td class="muted"><?= $dt ? date('d M Y', $dt) : '—' ?></td>
                <td class="muted"><?= $dt ? date('h:i A', $dt) : '—' ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
    <?php if ($waPages > 1): ?>
    <div class="pager" style="margin-top:14px;justify-content:center;align-items:center;gap:6px">
        <?php if ($waPage > 1): ?>
            <a href="?wa_page=<?= $waPage - 1 ?>#wa-logs">&laquo; Prev</a>
        <?php endif; ?>
        <span style="font-size:13px;color:var(--muted);font-weight:600">Page <?= $waPage ?> of <?= $waPages ?> (<?= number_format($waTotal) ?> total)</span>
        <?php if ($waPage < $waPages): ?>
            <a href="?wa_page=<?= $waPage + 1 ?>#wa-logs">Next &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>