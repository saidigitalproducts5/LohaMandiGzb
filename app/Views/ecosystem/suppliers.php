<?php
/** Public suppliers directory page. */
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
        .brand small{display:block;font-weight:600;font-size:11px;color:var(--muted);letter-spacing:.5px}
        .nav-links{display:flex;gap:20px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:10px 18px;border-radius:8px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.15s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark)}
        .btn-outline{background:#fff;color:var(--brand);border:1px solid var(--brand)}
        .btn-outline:hover{background:#fff5f5}
        .hero{background:linear-gradient(135deg,#1a1d23 0%,#2a2f3a 60%,#3a2430 100%);color:#fff;padding:56px 0 84px;position:relative;overflow:hidden}
        .hero::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at 78% 30%,rgba(224,39,53,.35),transparent 45%);pointer-events:none}
        .hero .container{position:relative;z-index:2}
        .hero h1{font-size:34px;margin-bottom:10px}
        .hero p{max-width:640px;font-size:16px;color:#d6d9e0}
        .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:-40px auto 0;position:relative;z-index:5}
        .stat{background:#fff;border-radius:12px;padding:18px;text-align:center;box-shadow:0 6px 24px rgba(0,0,0,.07);border-top:3px solid var(--brand)}
        .stat b{font-size:26px;display:block}
        .stat span{color:var(--muted);font-size:12.5px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
        section{padding:52px 0}
        .sec-head{margin-bottom:20px}
        .sec-head h2{font-size:22px}
        .list{display:grid;gap:14px}
        .seller{display:flex;align-items:center;gap:16px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px 20px;transition:.15s}
        .seller:hover{border-color:var(--brand);box-shadow:0 10px 28px rgba(0,0,0,.08);transform:translateY(-2px)}
        .av{width:54px;height:54px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px;flex-shrink:0;overflow:hidden}
        .seller .info{flex:1;min-width:0}
        .seller .sn{font-weight:800;font-size:16px}
        .seller .sm{color:var(--muted);font-size:13px;margin-top:2px}
        .badges{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
        .badges span{font-size:11.5px;font-weight:600;padding:3px 9px;border-radius:14px;border:1px solid var(--line);background:var(--bg)}
        .badges span.ok{background:#ecfdf5;border-color:#a7f3d0;color:#047857}
        .seller .extras{text-align:right;flex-shrink:0}
        .seller .extras b{font-size:17px;display:block}
        .seller .extras span{color:var(--muted);font-size:12px}
        .empty{grid-column:1/-1;text-align:center;color:var(--muted);padding:40px 0;background:#fff;border:1px dashed var(--line);border-radius:14px}
        .top-cta{text-align:center;margin:0 auto 30px}
        footer{background:#16181d;color:#b6bac3;padding:30px 0;font-size:14px}
        footer .container{display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}
        @media(max-width:700px){
            .stats{grid-template-columns:1fr;margin-top:0}
            .nav-links{display:none}
            .seller{flex-wrap:wrap}
            .seller .extras{text-align:left}
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
        <h1>Verified Steel Suppliers in Ghaziabad</h1>
        <p>Browse steel traders, TMT/pipe/plate dealers and manufacturers. Compare ratings, see verified badges and open their storefront to contact them directly.</p>
    </div>
</div>

<div class="container">
    <div class="stats">
        <div class="stat"><b><?= number_format($stats['sellers']) ?></b><span>Active Suppliers</span></div>
        <div class="stat"><b><?= number_format($stats['products']) ?></b><span>Live Listings</span></div>
        <div class="stat"><b><?= esc($stats['city']) ?></b><span>Marketplace Hub</span></div>
    </div>
</div>

<section>
    <div class="container">
        <div class="sec-head"><h2>All Suppliers</h2></div>
        <?php if (empty($suppliers)): ?>
            <div class="empty">No active suppliers yet. Be the first to join the marketplace.</div>
        <?php else: ?>
            <div class="list">
                <?php foreach ($suppliers as $r): ?>
                    <a class="seller" href="<?= esc($r['storefront_url']) ?>">
                        <div class="av"><?php if (!empty($r['logo'])): ?><img src="<?= esc(base_url('uploads/' . $r['logo'])) ?>" alt="<?= esc($r['business_name']) ?> logo" style="width:100%;height:100%;object-fit:cover"><?php else: ?><?= esc(strtoupper(substr($r['business_name'] ?? 'S', 0, 1))) ?><?php endif; ?></div>
                        <div class="info">
                            <div class="sn"><?= esc($r['business_name']) ?></div>
                            <div class="sm"><?= esc($r['city'] ?? 'Ghaziabad') ?><?= $r['state'] ? ', ' . esc($r['state']) : '' ?></div>
                            <div class="badges">
                                <?php if ($r['gst_verified']): ?><span class="ok">✓ GST Verified</span><?php endif; ?>
                                <?php if ($r['is_verified']): ?><span class="ok">✓ Verified</span><?php endif; ?>
                                <?php if ($r['is_premium']): ?><span class="ok">★ Premium</span><?php endif; ?>
                                <?php if ((float) $r['rating_avg'] > 0): ?><span>★ <?= number_format((float) $r['rating_avg'], 1) ?> (<?= (int) $r['rating_count'] ?>)</span><?php endif; ?>
                            </div>
                        </div>
                        <div class="extras">
                            <b><?= (int) $r['product_count'] ?></b><span>Listings</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

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
