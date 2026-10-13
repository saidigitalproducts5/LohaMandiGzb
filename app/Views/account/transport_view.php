<?php /** Transport booking detail + status update. */
$vehicle ??= null;
$fleet ??= null;
?>
<div class="card">
    <h3>Booking #<?= (int) $booking['id'] ?> — <span class="badge <?= ['requested' => 'amber', 'confirmed' => 'blue', 'in_transit' => 'blue', 'delivered' => 'green', 'cancelled' => 'red'][$booking['status']] ?? 'gray' ?>"><?= esc(ucfirst(str_replace('_', ' ', $booking['status']))) ?></span></h3>

    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
        <div class="card" style="margin:0"><span class="muted">Route</span><br><b><?= esc($booking['pickup_address']) ?></b><br><span class="muted">→</span><br><b><?= esc($booking['drop_address']) ?></b></div>
        <div class="card" style="margin:0"><span class="muted">Load</span><br><b><?= number_format((float) $booking['load_tonnage'], 2) ?> tonnes</b></div>
        <div class="card" style="margin:0"><span class="muted">Vehicle</span><br><b><?= esc($booking['vehicle_type'] ?: '—') ?></b>
            <?php if ($vehicle): ?>
                <br><span class="muted mono" style="font-size:12px"><?= esc($vehicle['vehicle_number']) ?> · <?= esc($vehicle['driver_name']) ?><?= !empty($vehicle['driver_phone']) ? ' · ' . esc($vehicle['driver_phone']) : '' ?></span>
            <?php endif; ?>
        </div>
        <div class="card" style="margin:0"><span class="muted">Freight</span><br><b><?= $booking['freight_charge'] ? '₹' . number_format((float) $booking['freight_charge']) : '—' ?></b></div>
        <div class="card" style="margin:0"><span class="muted">Fleet</span><br><b><?= esc($fleet['fleet_name'] ?? '—') ?></b></div>
    </div>

    <?php if (!empty($order)): ?>
        <div style="margin-top:16px">
            <span class="muted">Linked Order: </span><b><?= esc($order['order_number']) ?></b>
            <?php if ($sellerName): ?><span class="muted"> · Seller <b><?= esc($sellerName) ?></b></span><?php endif; ?>
            <span class="muted"> · Total <b>₹<?= number_format((float) $order['total']) ?></b></span>
        </div>
    <?php endif; ?>
</div>

<?php if ($isTransporter): ?>
    <div class="card">
        <h3>Update Status</h3>
        <form method="post" action="<?= base_url('account/transport/' . (int) $booking['id'] . '/status') ?>">
            <div class="row">
                <div>
                    <label>Status</label>
                    <select name="status">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= esc($st) ?>" <?= $booking['status'] === $st ? 'selected' : '' ?>><?= esc(ucfirst(str_replace('_', ' ', $st))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Note (optional — added to tracking)</label>
                    <input type="text" name="note" placeholder="e.g. Vehicle departed, ETA 6pm">
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Update Status</button>
        </form>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Tracking Timeline</h3>
    <?php if (empty($tracking)): ?>
        <div class="empty">No tracking points yet.</div>
    <?php else: ?>
        <div style="padding-left:8px">
            <?php foreach ($tracking as $pt): ?>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:8px 0;border-bottom:1px solid var(--line)">
                    <div style="width:10px;height:10px;border-radius:50%;background:var(--brand);margin-top:6px;flex-shrink:0"></div>
                    <div>
                        <b><?= esc(ucfirst(str_replace('_', ' ', $pt['status'] ?? ''))) ?></b>
                        <?php if (!empty($pt['note'])): ?> — <?= esc($pt['note']) ?><?php endif; ?>
                        <div class="muted" style="font-size:12px"><?= esc($pt['at'] ?? '') ?> · by <?= esc($pt['by'] ?? '—') ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<a class="btn" href="<?= base_url('account/transport') ?>">← Back to bookings</a>