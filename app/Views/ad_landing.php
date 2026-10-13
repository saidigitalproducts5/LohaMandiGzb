<?php
/**
 * Ad Landing Page — shows the uploaded ad image with seller/product info.
 */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($ad['title']) ?> — LOHA Mandi</title>
    <meta name="description" content="<?= esc($ad['title']) ?> on LOHA Mandi — The Digital Marketplace of Loha Mandi, Ghaziabad.">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <style>
        :root{--brand:#e02735;--brand-dark:#b81f2b;--ink:#1a1d23;--muted:#6b7280;--bg:#f6f7f9;--card:#fff;--line:#e5e7eb}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;color:var(--ink);background:var(--bg);line-height:1.55}
        a{color:inherit;text-decoration:none}
        img{max-width:100%;display:block}
        .container{max-width:900px;margin:0 auto;padding:0 20px}
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:64px;gap:20px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:19px}
        .brand img{height:48px;width:auto}
        .nav-links{display:flex;gap:20px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:11px 24px;border-radius:8px;font-weight:700;font-size:14.5px;border:0;cursor:pointer;display:inline-block;transition:.15s;text-align:center}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark);transform:translateY(-1px);box-shadow:0 4px 14px rgba(224,39,53,.3)}
        .btn-outline{background:#fff;color:var(--brand);border:1.5px solid var(--brand)}
        .btn-outline:hover{background:#fff5f5}
        .btn-lg{font-size:16px;padding:14px 34px}
        .hamburger{display:none;background:none;border:none;font-size:26px;cursor:pointer;color:var(--ink)}

        .hero{background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#334155 100%);color:#fff;padding:50px 0 44px;text-align:center}
        .hero h1{font-size:32px;margin-bottom:12px}
        .hero p{color:#94a3b8;font-size:16px;max-width:600px;margin:0 auto}

        .seller-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px;margin-top:28px}
        .seller-card .top{display:flex;gap:18px;align-items:center;flex-wrap:wrap}
        .seller-card .av{width:64px;height:64px;border-radius:16px;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:26px;flex-shrink:0;overflow:hidden}
        .seller-card .av img{width:100%;height:100%;object-fit:cover}
        .seller-card h3{font-size:20px}
        .seller-card .sub{color:var(--muted);font-size:14px;margin-top:2px}
        .badges{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
        .badges span{font-size:12px;font-weight:600;padding:4px 10px;border-radius:14px;border:1px solid var(--line);background:var(--bg)}
        .badges span.ok{background:#ecfdf5;border-color:#a7f3d0;color:#047857}
        .seller-card .about{color:var(--muted);font-size:14.5px;margin-top:14px}
        .seller-card .actions{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap}

        .product-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px;margin-top:20px;display:flex;gap:18px;align-items:center;flex-wrap:wrap}
        .product-card .price{font-size:26px;font-weight:800}
        .product-card .price small{color:var(--muted);font-weight:600;font-size:14px}

        footer{background:#16181d;color:#b6bac3;padding:30px 0;font-size:14px;margin-top:50px}
        footer .container{display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}
        footer a{color:#94a3b8;transition:.15s}
        footer a:hover{color:var(--brand)}

        .mobile-nav{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:30}
        .mobile-nav.open{display:block}
        .mobile-nav .drawer{position:fixed;top:0;left:0;width:280px;height:100%;background:#fff;z-index:31;padding:24px 20px;overflow-y:auto;box-shadow:4px 0 24px rgba(0,0,0,.2)}
        .mobile-nav .drawer a{display:block;padding:13px 0;font-weight:600;font-size:15px;border-bottom:1px solid var(--line)}
        .mobile-nav .drawer a:hover{color:var(--brand)}
        .mobile-nav .close-btn{background:none;border:none;font-size:28px;cursor:pointer;position:absolute;top:16px;right:16px;color:var(--muted)}

        @media(max-width:700px){
            .hero h1{font-size:24px}
            .nav-links{display:none}
            .hamburger{display:block}
            .seller-card .actions .btn{width:100%}
            footer .container{flex-direction:column;text-align:center}
        }
    </style>
</head>
<body>

<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:48px;width:auto"></a>
        <button class="hamburger" onclick="document.getElementById('mobileNav').classList.add('open')">&#9776;</button>
        <nav class="nav-links">
            <a href="<?= esc($base) ?>/catalog">Buy on Loha Mandi</a>
            <a href="<?= esc($base) ?>/suppliers">Sell on Loha Mandi</a>
            <a href="<?= esc($base) ?>/post-rfq">Post RFQ</a>
            <a href="<?= esc($base) ?>/faq">Why LOHA Mandi</a>
        </nav>
        <div>
            <?php if (!empty($auth['logged_in'])): ?>
                <a class="btn btn-primary" href="<?= esc($base) ?>/account">Dashboard</a>
            <?php else: ?>
                <a class="btn btn-outline" href="<?= esc($base) ?>/login">Login</a>
                <a class="btn btn-primary" href="<?= esc($base) ?>/register">Join Loha Mandi</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobileNav" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="drawer">
        <button class="close-btn" onclick="document.getElementById('mobileNav').classList.remove('open')">&times;</button>
        <a href="<?= esc($base) ?>/">Home</a>
        <a href="<?= esc($base) ?>/catalog">Buy on Loha Mandi</a>
        <a href="<?= esc($base) ?>/suppliers">Sell on Loha Mandi</a>
        <a href="<?= esc($base) ?>/faq">Why LOHA Mandi</a>
        <a href="<?= esc($base) ?>/post-rfq">Post RFQ</a>
        <a href="<?= esc($base) ?>/transport">Transport</a>
        <a href="<?= esc($base) ?>/login">Login</a>
        <a href="<?= esc($base) ?>/register">Join Loha Mandi</a>
    </div>
</div>

<div class="hero">
    <div class="container">
        <h1><?= esc($ad['title']) ?></h1>
        <p><?= $seller ? 'Featured seller on LOHA Mandi — ' . esc($seller['city'] ?? 'Ghaziabad') : 'Featured on LOHA Mandi — The Digital Marketplace of Loha Mandi, Ghaziabad.' ?></p>
    </div>
</div>

<div class="container">
    <?php if ($seller): ?>
    <!-- SELLER INFO -->
    <div class="seller-card">
        <div class="top">
            <div class="av"><?php if (!empty($seller['logo'])): ?><img src="<?= esc(base_url('uploads/' . $seller['logo'])) ?>" alt="<?= esc($seller['business_name']) ?> logo"><?php else: ?><?= esc(strtoupper(substr($seller['business_name'] ?? 'S', 0, 1))) ?><?php endif; ?></div>
            <div>
                <h3><?= esc($seller['business_name']) ?></h3>
                <div class="sub"><?= esc($seller['city'] ?? 'Ghaziabad') ?><?= $seller['state'] ? ', ' . esc($seller['state']) : '' ?> · ★ <?= number_format((float) $seller['rating_avg'], 1) ?> (<?= (int) $seller['rating_count'] ?>)</div>
                <div class="badges">
                    <?php if ($seller['gst_verified']): ?><span class="ok">✓ GST Verified</span><?php endif; ?>
                    <?php if ($seller['is_verified']): ?><span class="ok">✓ Verified</span><?php endif; ?>
                    <?php if ($seller['is_premium']): ?><span class="ok">★ Premium</span><?php endif; ?>
                </div>
            </div>
        </div>
        <?php if (!empty($seller['about'])): ?>
            <p class="about"><?= nl2br(esc($seller['about'])) ?></p>
        <?php endif; ?>
        <div class="actions">
            <a class="btn btn-primary btn-lg" href="<?= esc($base) ?>/seller/<?= esc($seller['slug']) ?>">Visit Storefront →</a>
            <a class="btn btn-outline" href="<?= esc($base) ?>/post-rfq">Post an RFQ</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($product): ?>
    <!-- PRODUCT INFO -->
    <div class="product-card">
        <div style="flex:1;min-width:200px">
            <div style="font-weight:700;font-size:17px;margin-bottom:4px"><?= esc($product['name']) ?></div>
            <?php $eff = ($product['offer_price'] && (float) $product['offer_price'] > 0) ? $product['offer_price'] : $product['price']; ?>
            <?php if ($eff): ?>
                <div class="price">₹<?= number_format((float) $eff) ?> <small>/<?= esc($product['unit']) ?></small></div>
            <?php endif; ?>
        </div>
        <a class="btn btn-primary" href="<?= esc($base) ?>/product/<?= esc($product['slug']) ?>">View Product →</a>
    </div>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <div>
            <b>LOHA Mandi</b>
            The Digital Marketplace of Loha Mandi, Ghaziabad.
        </div>
        <div>
            <b>Quick Links</b>
            <a href="<?= esc($base) ?>/catalog" style="display:block;margin-bottom:4px">Buy on Loha Mandi</a>
            <a href="<?= esc($base) ?>/suppliers" style="display:block;margin-bottom:4px">Sell on Loha Mandi</a>
            <a href="<?= esc($base) ?>/post-rfq" style="display:block;margin-bottom:4px">Post RFQ</a>
            <a href="<?= esc($base) ?>/register" style="display:block;margin-bottom:4px">Join Loha Mandi</a>
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
        &copy; 2026 LOHA Mandi. All rights reserved.
    </div>
</footer>

</body>
</html>
