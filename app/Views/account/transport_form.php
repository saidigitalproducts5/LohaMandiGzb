<?php /** New transport booking form — seller/buyer pick a fleet + vehicle. */
$old = $old ?? [];
$errors = $errors ?? [];
$fleets = $fleets ?? [];
$vehicles = $vehicles ?? [];
$isTransporter = !empty($transporter) && ($uRole ?? '') === 'transporter';
function tv(array $old, string $k): string { return esc($old[$k] ?? ''); }
$oldTid = (int) ($old['transporter_id'] ?? ($transporter['id'] ?? 0));
$oldVid = (int) ($old['vehicle_id'] ?? 0);
?>
<div class="card">
    <h3>New Transport Booking</h3>
    <?php if (!empty($order)): ?>
        <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:10px 14px;border-radius:8px;font-weight:600;margin-bottom:16px;font-size:13.5px">
            Booking for Order <?= esc($order['order_number']) ?> · ₹<?= number_format((float) $order['total']) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php if (!$isTransporter): ?>
            <div class="row">
                <div>
                    <label>Transporter Fleet</label>
                    <select name="transporter_id" id="tidSel" required>
                        <option value="">— Select transporter —</option>
                        <?php foreach ($fleets as $f): ?>
                            <option value="<?= (int) $f['id'] ?>" data-vehicles="<?= (int) ($f['vehicles_total'] ?? 0) ?>" <?= $oldTid === (int) $f['id'] ? 'selected' : '' ?>>
                                <?= esc($f['fleet_name'] ?: 'Fleet #' . (int) $f['id']) ?><?= !empty($f['verified']) ? ' ✓' : '' ?> (<?= (int) ($f['vehicles_total'] ?? 0) ?> vehicles)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['transporter_id'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['transporter_id']) ?></div><?php endif; ?>
                </div>
                <div>
                    <label>Vehicle (optional — from fleet)</label>
                    <select name="vehicle_id" id="vehSel">
                        <option value="0">— Any available vehicle —</option>
                        <?php foreach ($vehicles as $v): ?>
                            <option value="<?= (int) $v['id'] ?>" data-tid="<?= (int) $v['transporter_id'] ?>" data-type="<?= esc($v['vehicle_type']) ?>" <?= $oldVid === (int) $v['id'] ? 'selected' : '' ?>>
                                <?= esc($v['vehicle_number']) ?> — <?= esc($v['driver_name']) ?><?= !empty($v['driver_phone']) ? ' (' . esc($v['driver_phone']) . ')' : '' ?> (<?= esc($v['vehicle_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="muted" style="font-size:12px;margin-top:4px">Picking a vehicle prefills its type below.</div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div>
                <label>Pickup Address</label>
                <input type="text" name="pickup_address" value="<?= tv($old, 'pickup_address') ?>" placeholder="Seller warehouse / location">
                <?php if (isset($errors['pickup_address'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['pickup_address']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Drop Address</label>
                <input type="text" name="drop_address" value="<?= tv($old, 'drop_address') ?>" placeholder="Delivery location">
                <?php if (isset($errors['drop_address'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['drop_address']) ?></div><?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Load (tonnes)</label>
                <input type="number" name="load_tonnage" value="<?= tv($old, 'load_tonnage') ?>" step="0.1" min="0.1">
                <?php if (isset($errors['load_tonnage'])): ?><div class="alert error" style="margin-top:6px"><?= esc($errors['load_tonnage']) ?></div><?php endif; ?>
            </div>
            <div>
                <label>Vehicle Type</label>
                <select name="vehicle_type" id="typeSel">
                    <option value="">Select type</option>
                    <?php foreach (['Mini Truck', 'Pickup', 'Container', 'Flatbed Trailer', 'Tata Ace', 'Eicher 14ft', 'Truck 32ft'] as $vt): ?>
                        <option value="<?= esc($vt) ?>" <?= tv($old, 'vehicle_type') === $vt ? 'selected' : '' ?>><?= esc($vt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Freight Charge (₹)</label>
                <input type="number" name="freight_charge" value="<?= tv($old, 'freight_charge') ?>" step="50" min="0" placeholder="e.g. 8500">
            </div>
        </div>

        <button class="btn btn-primary" type="submit" style="margin-top:6px">Create Booking</button>
        <a class="btn" href="<?= base_url('account/transport') ?>" style="margin-top:6px">Cancel</a>
    </form>
</div>

<script>
(function(){
    var tidSel = document.getElementById('tidSel');
    var vehSel = document.getElementById('vehSel');
    var typeSel = document.getElementById('typeSel');
    if (!tidSel || !vehSel) return;

    function filterVehicles(){
        var tid = tidSel.value;
        var firstVisible = null;
        Array.prototype.forEach.call(vehSel.options, function(opt){
            if (!opt.dataset.tid) return; // "Any vehicle" option
            var show = !tid || opt.dataset.tid === tid;
            opt.hidden = !show;
            opt.disabled = !show;
            if (show && firstVisible === null) firstVisible = opt;
        });
        var cur = vehSel.options[vehSel.selectedIndex];
        if (cur && cur.disabled) vehSel.value = firstVisible ? firstVisible.value : '0';
    }

    tidSel.addEventListener('change', filterVehicles);

    vehSel.addEventListener('change', function(){
        var opt = vehSel.options[vehSel.selectedIndex];
        if (opt && opt.dataset.type && typeSel) {
            typeSel.value = opt.dataset.type;
        }
    });

    filterVehicles();
})();
</script>
