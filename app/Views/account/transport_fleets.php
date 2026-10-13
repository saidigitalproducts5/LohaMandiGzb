<?php /** Fleet directory — sellers/buyers pick a transporter and book. */ ?>
<div class="toolbar" style="margin-top:0">
    <div>
        <b>Transporters on LOHA MANDI </b>
        <div class="muted" style="font-size:13px">Pick a fleet and create a freight booking.</div>
    </div>
    <a class="btn" href="<?= base_url('account/transport') ?>">My Bookings</a>
</div>

<div class="card">
    <?php if (empty($transporters)): ?>
        <div class="empty">No transporters available right now.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Fleet</th><th>Owner</th><th>Route</th><th>Vehicles</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($transporters as $t): ?>
                    <tr>
                        <td>
                            <b><?= esc($t['fleet_name'] ?: 'Fleet #' . (int) $t['id']) ?></b>
                            <?php if (!empty($t['verified'])): ?><span class="badge green">✓ Verified</span><?php endif; ?>
                        </td>
                        <td><?= esc($t['owner_name'] ?? '—') ?><br><span class="muted mono" style="font-size:12px"><?= esc($t['owner_mobile'] ?? '') ?></span></td>
                        <td><?= esc(trim(($t['route_from'] ?? '') . ' → ' . ($t['route_to'] ?? ''), ' →')) ?: '—' ?></td>
                        <td><?= (int) ($t['vehicles_total'] ?? 0) ?> vehicle(s)</td>
                        <td><?php if ((int) ($t['vehicles_total'] ?? 0) === 0): ?><span class="badge amber">No vehicles</span><?php else: ?><span class="badge blue">Active</span><?php endif; ?></td>
                        <td>
                            <?php if ((int) ($t['vehicles_total'] ?? 0) > 0): ?>
                                <a class="btn btn-sm btn-primary" href="<?= base_url('account/transport/new?t=' . (int) $t['id']) ?>">Book →</a>
                            <?php else: ?>
                                <span class="muted" style="font-size:12px">Unavailable</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
