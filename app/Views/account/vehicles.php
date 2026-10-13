<?php
/** Transporter fleet vehicles — list + add. */
$errors = $errors ?? [];
$old = $old ?? [];
function vv(array $old, string $k): string { return esc($old[$k] ?? ''); }
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start">
    <div class="card">
        <h3>Add Vehicle</h3>
        <form method="post" action="<?= base_url('account/vehicles') ?>">
            <div>
                <label>Vehicle Number</label>
                <input type="text" name="vehicle_number" value="<?= vv($old, 'vehicle_number') ?>" placeholder="e.g. GA01AB1234" maxlength="32" required style="text-transform:uppercase">
                <?php if (isset($errors['vehicle_number'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['vehicle_number']) ?></div><?php endif; ?>
            </div>
            <div style="margin-top:12px">
                <label>Driver Name</label>
                <input type="text" name="driver_name" value="<?= vv($old, 'driver_name') ?>" placeholder="e.g. Ramesh Kumar" maxlength="120" required>
                <?php if (isset($errors['driver_name'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['driver_name']) ?></div><?php endif; ?>
            </div>
            <div style="margin-top:12px">
                <label>Driver Phone Number</label>
                <input type="tel" name="driver_phone" value="<?= vv($old, 'driver_phone') ?>" maxlength="10" pattern="[6-9][0-9]{9}" placeholder="10-digit mobile" required>
                <?php if (isset($errors['driver_phone'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['driver_phone']) ?></div><?php endif; ?>
            </div>
            <div style="margin-top:12px">
                <label>Vehicle Type</label>
                <select name="vehicle_type" required>
                    <option value="">Select type</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= esc($t) ?>" <?= vv($old, 'vehicle_type') === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['vehicle_type'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['vehicle_type']) ?></div><?php endif; ?>
            </div>
            <button class="btn btn-primary" type="submit" style="margin-top:14px">+ Add Vehicle</button>
        </form>
    </div>

    <div class="card">
        <h3>My Fleet (<?= count($vehicles) ?>)</h3>
        <?php if (empty($vehicles)): ?>
            <div class="empty">No vehicles added yet. Add your first vehicle to start receiving bookings.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Number</th><th>Driver</th><th>Phone</th><th>Type</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                        <tr>
                            <td class="mono"><b><?= esc($v['vehicle_number']) ?></b></td>
                            <td><?= esc($v['driver_name']) ?></td>
                            <td class="mono"><?= esc($v['driver_phone'] ?? '—') ?></td>
                            <td><span class="badge blue"><?= esc($v['vehicle_type']) ?></span></td>
                            <td><a class="btn btn-sm btn-danger" href="<?= base_url('account/vehicles/' . (int) $v['id'] . '/delete') ?>" onclick="return confirm('Remove this vehicle?')">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<a class="btn" href="<?= base_url('account/transport') ?>">← Back to bookings</a>
