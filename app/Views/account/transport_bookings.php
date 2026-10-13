<?php /** Transport bookings list (transporter + buyer/seller side). */ ?>
<div class="toolbar">
    <div>
        <?php if (!$isBuyerSide): ?>
            <a class="btn btn-primary" href="<?= base_url('account/transport/new') ?>">+ New Booking</a>
            <a class="btn" href="<?= base_url('account/vehicles') ?>">My Vehicles</a>
        <?php else: ?>
            <a class="btn btn-primary" href="<?= base_url('account/transport/fleets') ?>">+ Book a Transporter</a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <?php if (empty($bookings)): ?>
        <div class="empty">
            <?= $isBuyerSide ? 'No transport bookings yet. Book a transporter to move your loads.' : 'No transport bookings yet. Create one to track freight.' ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Route</th>
                    <th>Load</th>
                    <th>Fleet</th>
                    <th>Vehicle</th>
                    <th>Freight</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td class="mono">#<?= (int) $b['id'] ?></td>
                        <td><?= esc($b['pickup_address'] ?: '—') ?><br><span class="muted" style="font-size:12px">→ <?= esc($b['drop_address'] ?: '—') ?></span></td>
                        <td><?= number_format((float) $b['load_tonnage'], 1) ?> t</td>
                        <td><?= esc($b['fleet_name'] ?? '—') ?></td>
                        <td>
                            <?= esc($b['vehicle_type'] ?: '—') ?>
                            <?php if (!empty($b['vehicle_number'])): ?>
                                <br><span class="muted mono" style="font-size:12px"><?= esc($b['vehicle_number']) ?> · <?= esc($b['driver_name'] ?? '') ?><?= !empty($b['driver_phone']) ? ' · ' . esc($b['driver_phone']) : '' ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $b['freight_charge'] ? '₹' . number_format((float) $b['freight_charge']) : '—' ?></td>
                        <td>
                            <?php $cls = ['requested' => 'amber', 'confirmed' => 'blue', 'in_transit' => 'blue', 'delivered' => 'green', 'cancelled' => 'red'][$b['status']] ?? 'gray'; ?>
                            <span class="badge <?= $cls ?>"><?= esc(ucfirst(str_replace('_', ' ', $b['status']))) ?></span>
                        </td>
                        <td><?= esc($b['order_number'] ?? '—') ?></td>
                        <td><a class="btn btn-sm btn-primary" href="<?= base_url('account/transport/' . (int) $b['id']) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>