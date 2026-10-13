<?php $base = rtrim(base_url('admin'), '/'); $tab = $tab ?? 'rfqs'; ?>

<div class="card" style="padding:12px">
    <div class="toolbar" style="margin:0">
        <div style="display:flex;gap:8px">
            <a class="btn <?= $tab === 'rfqs' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/oversight?tab=rfqs">RFQs</a>
            <a class="btn <?= $tab === 'leads' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/oversight?tab=leads">Leads</a>
            <a class="btn <?= $tab === 'orders' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/oversight?tab=orders">Orders</a>
            <a class="btn <?= $tab === 'reviews' ? 'btn-primary' : '' ?>" href="<?= esc($base) ?>/oversight?tab=reviews">Reviews</a>
        </div>
        <form method="get" action="<?= esc($base) ?>/oversight" style="display:flex;gap:8px">
            <input type="hidden" name="tab" value="<?= esc($tab) ?>">
            <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Search…">
            <select name="status">
                <?php
                $opts = $tab === 'orders'
                    ? ['all' => 'All statuses', 'confirmed' => 'Confirmed', 'dispatched' => 'Dispatched', 'in_transit' => 'In transit', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled']
                    : ($tab === 'leads'
                        ? ['all' => 'All statuses', 'new' => 'New', 'interested' => 'Interested', 'hot' => 'Hot', 'closed' => 'Closed', 'rejected' => 'Rejected', 'lost' => 'Lost', 'won' => 'Won']
                        : ($tab === 'reviews'
                            ? ['all' => 'All statuses', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']
                            : ['all' => 'All statuses', 'open' => 'Open', 'quoted' => 'Quoted', 'accepted' => 'Accepted', 'closed' => 'Closed', 'cancelled' => 'Cancelled']));
                foreach ($opts as $k => $v): ?>
                    <option value="<?= $k ?>" <?= ($status ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>
</div>

<?php if ($tab === 'rfqs'): ?>
<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Product</th><th>Buyer</th><th>Qty</th><th>Location</th><th>Budget</th><th>Matched / Quotes</th><th>Status</th><th>Posted</th></tr></thead>
        <tbody>
        <?php if (empty($rfqs)): ?><tr><td colspan="9" class="empty">No RFQs.</td></tr>
        <?php else: foreach ($rfqs as $r): ?>
            <tr>
                <td class="mono">#<?= (int)$r['id'] ?></td>
                <td><b><?= esc($r['product_name']) ?></b><br><span class="muted"><?= esc($r['category_name'] ?? '') ?></span></td>
                <td><?= esc($r['buyer_name']) ?><br><span class="muted mono"><?= esc($r['buyer_mobile']) ?></span></td>
                <td><?= number_format((float)$r['quantity']) ?> <?= esc($r['unit']) ?></td>
                <td><?= esc($r['city']) ?></td>
                <td><?= $r['budget_min'] ? '₹' . number_format((float)$r['budget_min']) . '–' . number_format((float)$r['budget_max']) : '—' ?></td>
                <td><?= (int)$r['matched_sellers'] ?> / <?= (int)$r['quotes'] ?></td>
                <td><span class="badge <?= in_array($r['status'], ['accepted'], true) ? 'green' : (in_array($r['status'], ['open', 'quoted'], true) ? 'amber' : 'gray') ?>"><?= esc($r['status']) ?></span></td>
                <td class="muted"><?= esc(date('d M y', strtotime($r['created_at']))) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if ($tab === 'leads'): ?>
<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Seller</th><th>Buyer</th><th>Type</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
        <?php if (empty($leads)): ?><tr><td colspan="6" class="empty">No leads.</td></tr>
        <?php else: foreach ($leads as $l): ?>
            <tr>
                <td class="mono">#<?= (int)$l['id'] ?></td>
                <td><?= esc($l['business_name'] ?? '—') ?></td>
                <td><?= esc($l['buyer_name'] ?? $l['name'] ?? '—') ?><br><span class="muted mono"><?= esc($l['buyer_mobile'] ?? $l['mobile']) ?></span></td>
                <td><span class="pill"><?= esc($l['lead_type']) ?></span></td>
                <td><span class="badge <?= $l['status'] === 'won' ? 'green' : ($l['status'] === 'hot' ? 'red' : ($l['status'] === 'new' ? 'blue' : 'amber')) ?>"><?= esc($l['status']) ?></span></td>
                <td class="muted"><?= esc(date('d M y', strtotime($l['created_at']))) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if ($tab === 'reviews'): ?>
<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Seller</th><th>Buyer</th><th>Rating</th><th>Review</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (empty($reviews)): ?><tr><td colspan="7" class="empty">No reviews.</td></tr>
        <?php else: foreach ($reviews as $rv): ?>
            <tr>
                <td class="mono">#<?= (int)$rv['id'] ?></td>
                <td><?= esc($rv['business_name'] ?? '—') ?></td>
                <td><?= esc($rv['buyer_name']) ?></td>
                <td><span style="color:#f59e0b"><?= str_repeat('★', (int)$rv['rating']) ?></span></td>
                <td style="max-width:280px"><?= esc($rv['review'] ?? '—') ?></td>
                <td><span class="badge <?= $rv['status'] === 'approved' ? 'green' : ($rv['status'] === 'pending' ? 'amber' : 'red') ?>"><?= esc($rv['status']) ?></span></td>
                <td>
                    <?php if ($rv['status'] !== 'approved'): ?>
                        <form method="post" action="<?= esc($base) ?>/oversight/reviews/<?= (int)$rv['id'] ?>/moderate" style="display:inline">
                            <input type="hidden" name="action" value="approved">
                            <button class="btn btn-sm btn-primary" type="submit">Approve</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($rv['status'] !== 'rejected'): ?>
                        <form method="post" action="<?= esc($base) ?>/oversight/reviews/<?= (int)$rv['id'] ?>/moderate" style="display:inline">
                            <input type="hidden" name="action" value="rejected">
                            <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php if ($tab === 'orders'): ?>
<div class="card">
    <table>
        <thead><tr><th>Order #</th><th>Buyer</th><th>Seller</th><th>Total</th><th>Payment</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
        <?php if (empty($orders)): ?><tr><td colspan="7" class="empty">No orders yet — Order management ships in Phase 2.</td></tr>
        <?php else: foreach ($orders as $o): ?>
            <tr>
                <td class="mono"><b><?= esc($o['order_number']) ?></b></td>
                <td><?= esc($o['buyer_name']) ?></td>
                <td><?= esc($o['business_name']) ?></td>
                <td><b>₹<?= number_format((float)$o['total'], 2) ?></b></td>
                <td><span class="pill"><?= esc($o['payment_status']) ?></span></td>
                <td><span class="badge <?= $o['order_status'] === 'delivered' ? 'green' : 'amber' ?>"><?= esc($o['order_status']) ?></span></td>
                <td class="muted"><?= esc(date('d M y', strtotime($o['created_at']))) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>