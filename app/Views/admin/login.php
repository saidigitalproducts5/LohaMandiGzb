<?php
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — LOHA MANDI</title>
<style>
:root{--brand:#e02735;--brand-dark:#b81f2b;--ink:#1a1d23;--muted:#6b7280;--line:#e5e7eb}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;background:linear-gradient(135deg,#171a21,#2a2f3a 60%,#3a2430);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.box{background:#fff;border-radius:16px;padding:36px 34px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.35)}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:6px}
.logo img{height:52px;width:auto}
.logo small{display:block;font-size:10px;color:var(--muted);letter-spacing:1px;font-weight:700}
h1{font-size:20px;margin:16px 0 4px}
p{color:var(--muted);font-size:13.5px;margin-bottom:20px}
label{display:block;font-weight:600;font-size:13px;margin:14px 0 5px}
input{width:100%;padding:11px 12px;border:1px solid var(--line);border-radius:9px;font-size:14px;outline:0}
input:focus{border-color:var(--brand)}
button{width:100%;margin-top:22px;padding:12px;background:var(--brand);color:#fff;border:0;border-radius:9px;font-weight:700;font-size:15px;cursor:pointer}
button:hover{background:var(--brand-dark)}
.err{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:10px 12px;border-radius:8px;font-size:13px;margin-bottom:6px}
.back{display:block;text-align:center;margin-top:18px;font-size:13px;color:var(--muted)}
</style>
</head>
<body>
<div class="box">
    <div class="logo"><img src="<?= esc(rtrim(base_url(), '/')) ?>/images/logo-LohaMandi2.png" alt="LOHA MANDI"><small>GHAZIABAD · ADMIN</small></div>
    <h1>Sign in</h1>
    <p>Access the marketplace admin panel.</p>
    <?php foreach ($errors as $e): ?>
        <div class="err"><?= esc($e) ?></div>
    <?php endforeach; ?>
    <?php if (session('flash_error')): ?>
        <div class="err"><?= esc(session('flash_error')) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= esc(base_url('admin/login')) ?>">
        <label>Mobile Number</label>
        <input type="tel" name="mobile" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Sign in</button>
    </form>
    <a class="back" href="<?= esc(base_url()) ?>">← Back to marketplace</a>
</div>
</body>
</html>
