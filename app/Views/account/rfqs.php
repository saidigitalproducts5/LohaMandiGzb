<?php
/** RFQ list — buyer "My RFQs" or seller "Buyer RFQs" inbox. */
$base = rtrim(base_url(), '/');
$isBuyer = isset($isBuyer) ? $isBuyer : (isset($isSeller) ? !$isSeller : true);
$isBuyer = isset($isSeller) ? !$isSeller : $isBuyer;
?>
<div class="toolbar">
    <form method="get" action="<?= esc($base) ?>/account/rfqs">
        <?php if ($isBuyer): ?>
            <select name="status">
                <option value="all" <?= ($status ?? 'all') === 'all' ? 'selected' : '' ?>>All statuses</option>
                <?php foreach (['open', 'quoted', 'accepted', 'closed', 'cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($status ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        <?php endif; ?>
    </form>
    <?php if ($isBuyer): ?>
        <a class="btn btn-primary" href="<?= esc($base) ?>/account/rfqs/new">+ Post RFQ</a>
    <?php endif; ?>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th><?= $isBuyer ? 'Product' : 'RFQ' ?></th>
                <th>Qty</th>
                <th>City</th>
                <th><?= $isBuyer ? 'Quotes' : 'Buyer' ?></th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rfqs)): ?>
            <tr>
                <td colspan="6" class="empty">
                    <?= $isBuyer ? 'You haven\'t posted any RFQs yet. ' . '<a class="store-link" href="' . esc($base) . '/account/rfqs/new">Post your first RFQ →</a>' : 'No buyer RFQs match your business yet.' ?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($rfqs as $r):
                $rfqId    = $isBuyer ? (int)$r['id'] : (int)($r['rfq_id'] ?? $r['id']);
                $rfqStatus = $isBuyer ? $r['status'] : ($r['rfq_status'] ?? $r['status']);
            ?>
                <tr>
                    <td>
                        <b><?= esc($r['product_name']) ?></b>
                        <?php if (isset($r['specification']) && $r['specification']): ?>
                            <br><span class="muted"><?= esc(mb_strimwidth($r['specification'], 0, 60, '…')) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="mono"><?= rtrim(rtrim(number_format((float)$r['quantity'], 3), '0'), '.') ?> <?= esc($r['unit']) ?></td>
                    <td class="muted"><?= esc($r['city'] ?? '-') ?></td>
                    <td>
                        <?php if ($isBuyer): ?>
                            <span class="pill"><?= (int)($r['quote_count'] ?? 0) ?> quote(s)</span>
                        <?php else: ?>
                            <span class="muted"><?= esc($r['buyer_name'] ?? '') ?></span><br>
                            <span class="mono"><?= esc($r['buyer_mobile'] ?? '') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$isBuyer && empty($r['viewed_at'])): ?>
                            <span class="badge amber">New</span>
                        <?php endif; ?>
                        <span class="badge <?= in_array($rfqStatus, ['accepted', 'closed']) ? 'green' : ($rfqStatus === 'cancelled' ? 'red' : 'blue') ?>"><?= esc(ucfirst($rfqStatus)) ?></span>
                    </td>
                    <td>
                        <?php if (!$isBuyer && in_array($rfqStatus, ['open', 'quoted'], true)): ?>
                            <a class="btn btn-sm btn-primary" href="<?= esc($base) ?>/account/quotations/new/<?= $rfqId ?>">Reply</a>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/rfqs/<?= $rfqId ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>