<?php
/** Profile view — account + optional seller/transporter info. */
$base = rtrim(base_url(), '/');
$u = $user;
$s = $seller;
$t = $transporter;
?>
<form method="post" action="<?= esc($base) ?>/account/profile" enctype="multipart/form-data">
    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
        <div class="card">
            <h3>Account Details</h3>
            <label>Full Name</label>
            <input type="text" name="name" value="<?= esc($u['name']) ?>" required>

            <div class="row">
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?= esc($u['email'] ?? '') ?>">
                </div>
                <div>
                    <label>Mobile</label>
                    <input type="tel" value="<?= esc($u['mobile']) ?>" disabled>
                </div>
            </div>

            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current">
            <div class="muted" style="font-size:12px;margin-top:4px">Minimum 6 characters.</div>
        </div>

        <?php if ($s): ?>
            <div class="card">
                <h3>Subscription Plan</h3>
                <?php if ($membership): ?>
                    <div class="plan-box" style="border:2px solid #22c55e;border-radius:12px;padding:16px;background:#f0fdf4">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <b style="font-size:16px"><?= esc($membership['plan_name'] ?? 'Seller — Yearly') ?></b>
                            <?php
                            $expired = strtotime($membership['end_date']) < time();
                            $daysLeft = (int) floor((strtotime($membership['end_date']) - time()) / 86400);
                            ?>
                            <span class="badge <?= $expired ? 'red' : 'green' ?>"><?= $expired ? 'Expired' : 'Active' ?></span>
                        </div>
                        <table style="margin-top:10px;font-size:13px">
                            <tbody>
                                <tr><th style="width:45%">Plan</th><td><?= esc($membership['plan_name'] ?? '') ?></td></tr>
                                <tr><th>Amount Paid</th><td>₹<?= number_format((float) $membership['amount'], 2) ?></td></tr>
                                <tr><th>Start Date</th><td><?= esc(date('d M Y', strtotime($membership['start_date']))) ?></td></tr>
                                <tr><th>End Date</th><td><?= esc(date('d M Y', strtotime($membership['end_date']))) ?></td></tr>
                                <tr><th>Valid Days</th><td><?= max(0, $daysLeft) ?> days remaining</td></tr>
                            </tbody>
                        </table>
                        <div style="margin-top:12px">
                            <a class="btn btn-sm" href="<?= esc($base) ?>/account/subscribe">Renew / Upgrade</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert error">No active subscription.</div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>Business Profile</h3>

                <label>Shop / Business Logo</label>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                    <?php if (!empty($s['logo'])): ?>
                        <img src="<?= esc(base_url('uploads/' . $s['logo'])) ?>" alt="Logo"
                             style="width:64px;height:64px;border-radius:12px;object-fit:cover;border:1px solid var(--line,#e5e7eb)">
                    <?php else: ?>
                        <div style="width:64px;height:64px;border-radius:12px;background:#f0f1f4;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:24px;color:#9aa0ab">
                            <?= esc(strtoupper(substr($s['business_name'] ?? 'B', 0, 1))) ?>
                        </div>
                    <?php endif; ?>
                    <div style="flex:1">
                        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" style="font-size:13px">
                        <div class="muted" style="font-size:11.5px;margin-top:4px">JPG, PNG or WEBP · max 2 MB · shown on your storefront</div>
                    </div>
                </div>

                <label>Business Name</label>
                <input type="text" name="business_name" value="<?= esc($s['business_name']) ?>" required>

                <div class="row">
                    <div>
                        <label>Owner Name</label>
                        <input type="text" name="owner_name" value="<?= esc($s['owner_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label>Owner Phone</label>
                        <input type="tel" name="owner_phone" value="<?= esc($s['owner_phone'] ?? '') ?>">
                    </div>
                </div>

                <label>GSTIN</label>
                <input type="text" name="gstin" value="<?= esc($s['gstin'] ?? '') ?>" placeholder="e.g. 09ABCDE1234F1Z5">

                <div class="row">
                    <div>
                        <label>City</label>
                        <input type="text" name="city" value="<?= esc($s['city'] ?? '') ?>">
                    </div>
                    <div>
                        <label>State</label>
                        <input type="text" name="state" value="<?= esc($s['state'] ?? '') ?>">
                    </div>
                    <div>
                        <label>Pincode</label>
                        <input type="text" name="pincode" value="<?= esc($s['pincode'] ?? '') ?>">
                    </div>
                </div>

                <label>Address</label>
                <input type="text" name="address_line1" value="<?= esc($s['address_line1'] ?? '') ?>" placeholder="Street, market, shop no.">

                <div class="row">
                    <div>
                        <label>Website</label>
                        <input type="text" name="website" value="<?= esc($s['website'] ?? '') ?>">
                    </div>
                </div>

                <label>About Business</label>
                <textarea name="about" rows="3"><?= esc($s['about'] ?? '') ?></textarea>

                <div class="muted" style="font-size:12px;margin-top:8px">
                    Status: <b><?= esc(ucfirst($s['status'])) ?></b>
                    <?php if ($s['gst_verified']): ?> · ✓ GST verified<?php endif; ?>
                    <?php if ($s['is_premium']): ?> · ★ Premium<?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($t): ?>
            <div class="card">
                <h3>Fleet Profile</h3>
                <label>Fleet / Company Name</label>
                <input type="text" name="fleet_name" value="<?= esc($t['fleet_name']) ?>" required>

                <div class="row">
                    <div>
                        <label>Vehicles</label>
                        <input type="number" name="vehicle_count" value="<?= (int) $t['vehicle_count'] ?>" min="1">
                    </div>
                    <div>
                        <label>Route From</label>
                        <input type="text" name="route_from" value="<?= esc($t['route_from'] ?? '') ?>">
                    </div>
                    <div>
                        <label>Route To</label>
                        <input type="text" name="route_to" value="<?= esc($t['route_to'] ?? '') ?>">
                    </div>
                </div>

                <div class="muted" style="font-size:12px;margin-top:8px">
                    Verification: <?php if ($t['verified']): ?><b>✓ Verified</b><?php else: ?><b>Pending</b><?php endif; ?>
                    — verified fleets are matched to orders.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <button class="btn btn-primary" type="submit">Save Changes</button>
</form>