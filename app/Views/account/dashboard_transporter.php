<?php /** Transporter dashboard. */ ?>
<div class="grid">
    <div class="stat"><b><?= (int) ($stats['bookings'] ?? 0) ?></b><span>Bookings</span></div>
    <div class="stat"><b><?= (int) ($stats['in_transit'] ?? 0) ?></b><span>In Transit</span></div>
    <div class="stat"><b><?= (int) ($stats['delivered'] ?? 0) ?></b><span>Delivered</span></div>
    <div class="stat"><b>₹<?= number_format((float) ($stats['revenue'] ?? 0)) ?></b><span>Freight Earned</span></div>
    <div class="stat"><b><?= (int) ($stats['vehicles'] ?? 0) ?></b><span>Vehicles</span></div>
</div>

<div class="card">
    <h3>Fleet Profile</h3>
    <?php if ($transporter): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px">
            <div><span class="muted">Fleet Name</span><br><b><?= esc($transporter['fleet_name']) ?></b></div>
            <div><span class="muted">Vehicles</span><br><b><?= (int) $transporter['vehicle_count'] ?></b></div>
            <div><span class="muted">Primary Route</span><br><b><?= esc($transporter['route_from'] ?? '—') ?> → <?= esc($transporter['route_to'] ?? '—') ?></b></div>
            <div><span class="muted">Status</span><br>
                <?php if ($transporter['verified']): ?>
                    <span class="badge green">✓ Verified</span>
                <?php else: ?>
                    <span class="badge amber">Pending Verification</span>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="empty">No fleet profile linked.</div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Recent Transport Bookings</h3>
    <?php if (empty($recentBookings)): ?>
        <div class="empty">No bookings yet. New requests will appear here.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>ID</th><th>Route</th><th>Load</th><th>Freight</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($recentBookings as $b): ?>
                    <tr>
                        <td class="mono">#<?= (int) $b['id'] ?></td>
                        <td><?= esc($b['pickup_address'] ?: '—') ?> → <?= esc($b['drop_address'] ?: '—') ?></td>
                        <td><?= number_format((float) $b['load_tonnage'], 1) ?> t</td>
                        <td><?= $b['freight_charge'] ? '₹' . number_format((float) $b['freight_charge']) : '—' ?></td>
                        <td>
                            <?php
                            $cls = ['requested' => 'amber', 'confirmed' => 'blue', 'in_transit' => 'blue', 'delivered' => 'green', 'cancelled' => 'red'][$b['status']] ?? 'gray'; ?>
                            <span class="badge <?= $cls ?>"><?= esc(ucfirst(str_replace('_', ' ', $b['status']))) ?></span>
                        </td>
                        <td><a class="btn btn-sm btn-primary" href="<?= base_url('account/transport/' . (int) $b['id']) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>