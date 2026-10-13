<?php
/** Public "Post an RFQ" landing page for buyers. */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($meta['title']) ?></title>
    <meta name="description" content="<?= esc($meta['description']) ?>">
    <style>
        :root{--brand:#e02735;--brand-dark:#b81f2b;--ink:#1a1d23;--muted:#6b7280;--bg:#f6f7f9;--card:#fff;--line:#e5e7eb}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;color:var(--ink);background:var(--bg);line-height:1.55}
        a{color:inherit;text-decoration:none}
        .container{max-width:1180px;margin:0 auto;padding:0 20px}
        header{background:#fff;border-bottom:1px solid var(--line)}
        .nav{display:flex;align-items:center;justify-content:space-between;height:64px;gap:20px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:19px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:var(--brand);display:inline-block}
        .nav-links{display:flex;gap:20px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:11px 20px;border-radius:8px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.15s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark)}
        .btn-outline{background:#fff;color:var(--brand);border:1px solid var(--brand)}
        .btn-outline:hover{background:#fff5f5}
        .btn-lg{font-size:16px;padding:14px 30px}
        .hero{background:linear-gradient(135deg,#0f766e 0%,#0d9488 55%,#115e59 100%);color:#fff;padding:64px 0;position:relative;overflow:hidden}
        .hero::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at 80% 20%,rgba(255,255,255,.18),transparent 45%);pointer-events:none}
        .hero .container{position:relative;z-index:2;text-align:center}
        .hero h1{font-size:36px;margin-bottom:10px}
        .hero p{max-width:640px;margin:0 auto 24px;font-size:16px;color:#d8f0ec}
        .stats{display:flex;justify-content:center;gap:36px;margin-top:8px}
        .stats div b{font-size:24px;display:block}
        .stats div span{font-size:12.5px;text-transform:uppercase;letter-spacing:.5px;opacity:.85}
        section{padding:52px 0}
        .sec-head{margin-bottom:20px}
        .sec-head h2{font-size:22px}
        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
        .step{background:#fff;border:1px solid var(--line);border-radius:14px;padding:26px}
        .step .num{font-size:26px;font-weight:800;color:var(--brand);margin-bottom:10px}
        .step h3{font-size:16px;margin-bottom:6px}
        .step p{color:var(--muted);font-size:14px}
        .cats{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:12px;margin-top:8px}
        .cat{background:#fff;border:1px solid var(--line);border-radius:12px;padding:14px 16px;font-weight:700;font-size:14px;transition:.15s;display:flex;align-items:center;gap:10px}
        .cat:hover{border-color:var(--brand);color:var(--brand);transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.06)}
        .cat .ico{width:34px;height:34px;border-radius:9px;background:#fdeaea;color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:800}
        .final-cta{background:#fff;border:1px solid var(--line);border-radius:16px;padding:34px;text-align:center;margin-top:8px}
        .final-cta h2{margin-bottom:8px;font-size:22px}
        .final-cta p{color:var(--muted);margin-bottom:20px}
        .final-cta .btn{margin:0 6px}
        footer{background:#16181d;color:#b6bac3;padding:30px 0;font-size:14px}
        footer .container{display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}
        @media(max-width:700px){
            .steps{grid-template-columns:1fr}
            .nav-links{display:none}
            .stats{flex-wrap:wrap;gap:18px}
        }
    </style>
</head>
<body>
<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA MANDI" style="height:48px;width:auto"></a>
        <nav class="nav-links">
            <a href="<?= esc($base) ?>/catalog">Products</a>
            <a href="<?= esc($base) ?>/suppliers">Suppliers</a>
            <a href="<?= esc($base) ?>/post-rfq">Post RFQ</a>
            <a href="<?= esc($base) ?>/admin/login" style="color:var(--brand);font-size:13px">⚙ Admin</a>
        </nav>
        <div>
            <?php if (!empty($auth['logged_in'])): ?>
                <a class="btn btn-primary" href="<?= esc($base) ?>/account">Dashboard</a>
            <?php else: ?>
                <a class="btn btn-outline" href="<?= esc($base) ?>/login">Login</a>
                <a class="btn btn-primary" href="<?= esc($base) ?>/register">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="hero">
    <div class="container">
        <h1>Post an RFQ. Get Multiple Quotes.</h1>
        <p>Tell us exactly what steel you need — quantity, grade, size and delivery location. Multiple verified sellers compete to give you the best price.</p>
        <a class="btn btn-lg btn-primary" href="<?= !empty($auth['logged_in']) ? esc($base) . '/account/rfqs/new' : esc($base) . '/login' ?>">Post an RFQ Now</a>
        <div class="stats">
            <div><b><?= number_format($counts['open_rfqs']) ?></b><span>Open RFQs</span></div>
            <div><b><?= number_format($counts['sellers']) ?></b><span>Active Sellers</span></div>
        </div>
    </div>
</div>

<section>
    <div class="container">
        <div class="sec-head"><h2>How Posting an RFQ Works</h2></div>
        <div class="steps">
            <div class="step"><div class="num">01</div><h3>Describe your requirement</h3><p>Enter product, quantity, unit, grade and specifications you need.</p></div>
            <div class="step"><div class="num">02</div><h3>Sellers quote within hours</h3><p>Matched sellers with your requirement send priced quotations to your inbox.</p></div>
            <div class="step"><div class="num">03</div><h3>Compare &amp; accept</h3><p>Evaluate unit price, GST, delivery and payment terms, then accept the best quote and place your order.</p></div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="sec-head"><h2>Popular Steel Categories</h2></div>
        <div class="cats">
            <?php foreach ($categories as $c): ?>
                <a class="cat" href="<?= esc($base) ?>/catalog/category/<?= esc($c['slug']) ?>">
                    <span class="ico"><?= esc(strtoupper(substr($c['name'], 0, 1))) ?></span>
                    <?= esc($c['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="container">
    <div class="final-cta">
        <h2>Ready to source your next lot?</h2>
        <p>Posting an RFQ is free for buyers. Register or sign in to get started.</p>
        <?php if (!empty($auth['logged_in'])): ?>
            <a class="btn btn-primary btn-lg" href="<?= esc($base) ?>/account/rfqs/new">Post an RFQ Now</a>
        <?php else: ?>
            <a class="btn btn-primary btn-lg" href="<?= esc($base) ?>/register">Create a Buyer Account</a>
            <a class="btn btn-outline" href="<?= esc($base) ?>/login">Sign In</a>
        <?php endif; ?>
    </div>
</div>

<footer>
    <div class="container">
        <div>
            <b>LOHA MANDI</b>
            The Digital Marketplace of Loha Mandi, Ghaziabad.
        </div>
        <div>
            <b>Quick Links</b>
            <a href="<?= esc($base) ?>/catalog" style="display:block;margin-bottom:4px">Buy on Loha MANDI</a>
            <a href="<?= esc($base) ?>/suppliers" style="display:block;margin-bottom:4px">Sell on Loha MANDI</a>
            <a href="<?= esc($base) ?>/post-rfq" style="display:block;margin-bottom:4px">Post RFQ</a>
            <a href="<?= esc($base) ?>/register" style="display:block;margin-bottom:4px">Join Loha MANDI</a>
            <a href="<?= esc($base) ?>/privacy-policy" style="display:block;margin-bottom:4px">Privacy Policy</a>
            <a href="<?= esc($base) ?>/terms-of-service" style="display:block;margin-bottom:4px">Terms of Service</a>
        </div>
        <div>
            <b>Contact</b>
            Loha Mandi, Ghaziabad, UP<br>
            <a href="mailto:info@lohamandigzb.online" style="color:var(--brand)">info@lohamandigzb.online</a>
        </div>
        <div>
            <b>Developed &amp; Maintained by</b>
            VDS INFOTECH
        </div>
    </div>
    <div class="container" style="border-top:1px solid rgba(255,255,255,.08);margin-top:20px;padding-top:16px;justify-content:center;font-size:12px">
        &copy; 2026 LOHA MANDI. All rights reserved.
    </div>
</footer>
</body>
</html>
