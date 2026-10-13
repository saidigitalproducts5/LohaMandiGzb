<?php
/** Register — buyer or seller signup. */
$base = rtrim(base_url(), '/');
$errors = $errors ?? [];
$old = $old ?? [];
function val(array $old, string $k): string { return esc($old[$k] ?? ''); }
$presetRole = in_array(strtolower($_GET['role'] ?? ''), ['buyer', 'seller', 'transporter'], true) ? strtolower($_GET['role']) : 'seller';
$roleVal = ($old['role'] ?? '') !== '' ? $old['role'] : $presetRole;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — LOHA Mandi</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;background:linear-gradient(135deg,#1a1d23,#2a2f3a);min-height:100vh;display:flex;align-items:center;justify-content:center;color:#1a1d23;font-size:14px;padding:30px 0}
        .box{background:#fff;border-radius:16px;width:100%;max-width:440px;padding:34px 36px;box-shadow:0 24px 60px rgba(0,0,0,.35)}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px;margin-bottom:6px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:#e02735;display:inline-block}
        .sub{color:#6b7280;font-size:13px;margin-bottom:20px}
        label{display:block;font-weight:600;font-size:13px;margin:13px 0 5px}
        input,select{width:100%;padding:11px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;outline:0;background:#fff}
        input:focus,select:focus{border-color:#e02735}
        .err{color:#b91c1c;font-size:12.5px;font-weight:600;margin-top:5px}
        .btn{width:100%;margin-top:22px;padding:12px;background:#e02735;color:#fff;border:0;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer}
        .btn:hover{background:#b81f2b}
        .roles{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:6px}
        .role{position:relative}
        .role input{position:absolute;opacity:0}
        .role label{border:1px solid #e5e7eb;border-radius:10px;padding:12px;text-align:center;cursor:pointer;font-weight:700;margin:0;transition:.12s}
        .role input:checked + label{border-color:#e02735;background:#fef2f2;color:#e02735}
        .hide{display:none}
        .alt{text-align:center;margin-top:18px;font-size:13.5px;color:#6b7280}
        .alt a{color:#e02735;font-weight:700}
        .back{display:block;text-align:center;margin-top:14px;font-size:13px;color:#9ca3af}
        .back:hover{color:#e02735}
        .promo{position:relative;margin-top:10px;border-radius:12px;padding:14px 14px 12px;background:linear-gradient(135deg,#fff7ed 0%,#fef2f2 100%);border:1.5px dashed #fca5a5;overflow:hidden}
        .promo-ribbon{position:absolute;top:9px;right:-32px;transform:rotate(35deg);background:#e02735;color:#fff;font-size:9.5px;font-weight:800;letter-spacing:.6px;padding:3px 36px;box-shadow:0 2px 6px rgba(0,0,0,.15);white-space:nowrap}
        .promo-title{font-size:12.5px;font-weight:800;color:#b91c1c;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;padding-right:76px}
        .promo-price{display:flex;align-items:baseline;gap:8px;flex-wrap:wrap}
        .promo-price .old{color:#9ca3af;text-decoration:line-through;font-size:15px;font-weight:700}
        .promo-price .new{font-size:28px;font-weight:900;color:#e02735;line-height:1}
        .promo-price .per{font-size:12.5px;color:#6b7280;font-weight:700}
        .promo-badges{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}
        .promo-badges span{font-size:10.5px;font-weight:800;color:#047857;background:#ecfdf5;border:1px solid #a7f3d0;padding:3px 8px;border-radius:20px}
        .promo-note{font-size:11.5px;color:#b45309;font-weight:600;margin-top:8px}
    </style>
</head>
<body>
<div class="box">
    <div class="brand"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:52px;width:auto"></div>
    <div class="sub">Create your buyer or seller account</div>

    <form method="post" action="<?= esc($base) ?>/register">
        <label>I am a</label>
        <div class="roles">
                <div class="role">
                    <input type="radio" name="role" id="rbuyer" value="buyer" <?= $roleVal === 'buyer' ? 'checked' : '' ?>>
                    <label for="rbuyer">🏬 Buyer</label>
                </div>
                <div class="role">
                    <input type="radio" name="role" id="rseller" value="seller" <?= $roleVal === '' || $roleVal === 'seller' ? 'checked' : '' ?>>
                    <label for="rseller">🏪 Seller</label>
                </div>
                <div class="role" style="grid-column:1/-1">
                    <input type="radio" name="role" id="rtransporter" value="transporter" <?= $roleVal === 'transporter' ? 'checked' : '' ?>>
                    <label for="rtransporter">🚚 Transporter / Fleet Owner</label>
                </div>
            </div>

        <div id="businessWrap" class="<?= $roleVal === 'seller' ? '' : 'hide' ?>">
            <label>Business / Company Name</label>
            <input type="text" name="business_name" value="<?= val($old, 'business_name') ?>" placeholder="e.g. Shree Steel Traders">
            <?php if (isset($errors['business_name'])): ?><div class="err"><?= esc($errors['business_name']) ?></div><?php endif; ?>
            <div class="promo">
                <span class="promo-ribbon">FLAT ₹5,000 OFF</span>
                <div class="promo-title">🔨 Heavy Seller Registration Discount</div>
                <div class="promo-price">
                    <span class="old">₹14,999</span>
                    <span class="new">₹9,999</span><span class="per">/year</span>
                </div>
                <div class="promo-badges">
                    <span>33% OFF</span>
                    <span>One-time yearly subscription</span>
                </div>
                <div class="promo-note">Your seller account is created only after payment succeeds.</div>
            </div>
        </div>

        <div id="fleetWrap" class="<?= $roleVal === 'transporter' ? '' : 'hide' ?>">
            <label>Fleet / Logistic Company Name</label>
            <input type="text" name="fleet_name" value="<?= val($old, 'fleet_name') ?>" placeholder="e.g. Ghaziabad Modi Transport">
            <?php if (isset($errors['fleet_name'])): ?><div class="err"><?= esc($errors['fleet_name']) ?></div><?php endif; ?>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
                <div>
                    <label style="margin-top:8px">Vehicles</label>
                    <input type="number" name="vehicle_count" value="<?= val($old, 'vehicle_count') ? esc($old['vehicle_count']) : '1' ?>" min="1">
                </div>
                <div>
                    <label style="margin-top:8px">Route From</label>
                    <input type="text" name="route_from" value="<?= val($old, 'route_from') ?>" placeholder="Ghaziabad">
                </div>
                <div>
                    <label style="margin-top:8px">Route To</label>
                    <input type="text" name="route_to" value="<?= val($old, 'route_to') ?>" placeholder="Delhi">
                </div>
            </div>
            <div style="font-size:12px;color:#16a34a;margin-top:8px;font-weight:600">Transporter registration is free. Verified fleets get matched to seller &amp; buyer orders.</div>
        </div>

        <label>Full Name</label>
        <input type="text" name="name" value="<?= val($old, 'name') ?>" required>
        <?php if (isset($errors['name'])): ?><div class="err"><?= esc($errors['name']) ?></div><?php endif; ?>

        <label>Mobile Number</label>
        <input type="tel" name="mobile" value="<?= val($old, 'mobile') ?>" maxlength="10" pattern="[6-9][0-9]{9}" required placeholder="10-digit mobile">
        <?php if (isset($errors['mobile'])): ?><div class="err"><?= esc($errors['mobile']) ?></div><?php endif; ?>

        <label>Email (optional)</label>
        <input type="email" name="email" value="<?= val($old, 'email') ?>" placeholder="you@company.com">
        <?php if (isset($errors['email'])): ?><div class="err"><?= esc($errors['email']) ?></div><?php endif; ?>

        <label>Password</label>
        <input type="password" name="password" required minlength="6">
        <?php if (isset($errors['password'])): ?><div class="err"><?= esc($errors['password']) ?></div><?php endif; ?>

        <button class="btn" type="submit">Create Account</button>
    </form>

    <div class="alt">Already registered? <a href="<?= esc($base) ?>/login">Sign in</a></div>
    <a class="back" href="<?= esc($base) ?>/">← Back to marketplace</a>
</div>
<script>
document.querySelectorAll('.role input').forEach(r => r.addEventListener('change', () => {
    document.getElementById('businessWrap').classList.toggle('hide', r.value !== 'seller');
    document.getElementById('fleetWrap').classList.toggle('hide', r.value !== 'transporter');
}));
</script>
</body>
</html>
