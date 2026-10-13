<?php
/** RFQ detail view — buyer or seller perspective. */
$base = rtrim(base_url(), '/');
$r = $rfq;
$statusBadge = in_array($r['status'], ['accepted', 'closed']) ? 'green' : ($r['status'] === 'cancelled' ? 'red' : 'blue');
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3><?= esc($r['product_name']) ?></h3>
        <span class="badge <?= $statusBadge ?>"><?= esc(ucfirst($r['status'])) ?></span>
        <table style="margin-top:14px">
            <tbody>
                <tr><th style="width:40%">Quantity</th><td><?= rtrim(rtrim(number_format((float)$r['quantity'], 3), '0'), '.') ?> <?= esc($r['unit']) ?></td></tr>
                <tr><th>Required by</th><td><?= esc($r['required_date'] ?? '-') ?></td></tr>
                <tr><th>Delivery to</th><td><?= esc($r['delivery_location'] ?? '-') ?><?= $r['city'] ? '<br>' . esc($r['city']) . ', ' . esc($r['state']) : '' ?></td></tr>
                <tr><th>Budget</th><td><?= $r['budget_min'] !== null ? '₹' . number_format((float)$r['budget_min']) . ' – ₹' . number_format((float)$r['budget_max']) : 'Not disclosed' ?></td></tr>
                <tr><th>Specification</th><td><?= nl2br(esc($r['specification'] ?? '-')) ?></td></tr>
                <?php if ($r['document_path']): ?><tr><th>Document</th><td><a class="store-link" href="<?= esc(base_url('uploads/' . $r['document_path'])) ?>" target="_blank">Download</a></td></tr><?php endif; ?>
                <?php if ($r['photo_path']): ?><tr><th>Photo</th><td><a class="store-link" href="<?= esc(base_url('uploads/' . $r['photo_path'])) ?>" target="_blank">View</a></td></tr><?php endif; ?>
                <tr><th>Posted</th><td class="muted"><?= esc(date('d M Y, h:i A', strtotime($r['created_at']))) ?></td></tr>
            </tbody>
        </table>

        <?php if (!$isBuyer && in_array($r['status'], ['open', 'quoted'], true)): ?>
            <div style="margin-top:16px">
                <a class="btn btn-primary" href="<?= esc($base) ?>/account/quotations/new/<?= (int)$r['id'] ?>">Send Quotation</a>
            </div>
        <?php endif; ?>

        <?php if ($isBuyer && $isOwner && $r['status'] === 'open'): ?>
            <div style="margin-top:16px">
                <a class="btn btn-danger" href="<?= esc($base) ?>/account/rfqs/<?= (int)$r['id'] ?>/cancel" onclick="return confirm('Cancel this RFQ?')">Cancel RFQ</a>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card">
            <h3>Quotations (<?= count($quotations) ?>)</h3>
            <?php if (empty($quotations)): ?>
                <div class="empty">No quotations yet.</div>
            <?php else: ?>
                <?php foreach ($quotations as $q): ?>
                    <div style="border:1px solid var(--line);border-radius:10px;padding:14px;margin-bottom:12px">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <b><?= esc($q['business_name'] ?? ('Seller #' . $q['seller_id'])) ?></b>
                            <span class="badge <?= $q['status'] === 'accepted' ? 'green' : ($q['status'] === 'rejected' ? 'red' : 'blue') ?>"><?= esc(ucfirst($q['status'])) ?></span>
                        </div>
                        <div style="margin-top:8px;font-size:18px;font-weight:800">₹<?= number_format((float)$q['total_amount'], 2) ?></div>
                        <div class="muted" style="font-size:12.5px">
                            <?= esc($q['quote_number']) ?> · <?= rtrim(rtrim(number_format((float)$q['quantity'], 3), '0'), '.') ?> <?= esc($q['unit']) ?> @ ₹<?= number_format((float)$q['unit_price'], 2) ?>/<?= esc($q['unit']) ?>
                            <?php if ($q['valid_until']): ?> · Valid till <?= esc($q['valid_until']) ?><?php endif; ?>
                        </div>
                        <?php if ($q['notes']): ?><div style="margin-top:8px;font-size:13px"><?= nl2br(esc($q['notes'])) ?></div><?php endif; ?>
                        <?php if ($isBuyer && $isOwner && $q['status'] === 'sent'): ?>
                            <form method="post" action="<?= esc($base) ?>/account/quotations/<?= (int)$q['id'] ?>/accept" style="margin-top:12px;display:flex;gap:8px;align-items:flex-end">
                                <div style="flex:1">
                                    <label style="font-size:12px">Delivery Address</label>
                                    <input type="text" name="shipping_address" placeholder="Site / delivery address">
                                </div>
                                <button class="btn btn-primary" type="submit" onclick="return confirm('Accept this quotation and create an order?')">Accept &amp; Place Order</button>
                            </form>
                        <?php elseif ($q['status'] === 'accepted'): ?>
                            <div class="alert success" style="margin-top:10px">✓ Quotation accepted — order created.</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($isBuyer && $isOwner && !empty($matchedSellers)): ?>
            <div class="card">
                <h3>Matched Sellers</h3>
                <table>
                    <thead><tr><th>Seller</th><th>Location</th></tr></thead>
                    <tbody>
                    <?php foreach ($matchedSellers as $m): ?>
                        <tr>
                            <td>
                                <b><?= esc($m['business_name']) ?></b>
                                <?php if ($m['is_premium']): ?><span class="pill">★ Premium</span><?php endif; ?>
                                <?php if ($m['gst_verified']): ?><span class="pill">✓ GST</span><?php endif; ?>
                            </td>
                            <td class="muted"><?= esc($m['city'] ?? '') ?><?= $m['state'] ? ', ' . esc($m['state']) : '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>