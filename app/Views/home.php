<?php
/**
 * LOHA Mandi — Home / landing page.
 * The Digital Marketplace of Loha Mandi, Ghaziabad.
 * Renders live catalog data and provides instant product search via the API.
 */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOHA Mandi — The Digital Marketplace of Loha Mandi, Ghaziabad</title>
    <meta name="description" content="LOHA Mandi — Buy and sell iron, steel, TMT bars, MS plates, pipes and structural steel from verified traders in Loha Mandi, Ghaziabad. Post RFQs, compare quotes, connect directly.">
    <style>
        :root{
            --brand:#e02735; --brand-dark:#b81f2b; --ink:#1a1d23; --muted:#6b7280;
            --bg:#f6f7f9; --card:#ffffff; --line:#e5e7eb;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;color:var(--ink);background:var(--bg);line-height:1.55}
        a{color:inherit;text-decoration:none}
        img{max-width:100%;display:block}

        .container{max-width:1180px;margin:0 auto;padding:0 20px}

        /* Header */
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:66px;gap:24px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:var(--brand);display:inline-block}
        .brand small{display:block;font-weight:600;font-size:11px;color:var(--muted);letter-spacing:.5px}
        .nav-links{display:flex;gap:22px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:10px 18px;border-radius:8px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.15s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark)}
        .btn-outline{background:#fff;color:var(--brand);border:1px solid var(--brand)}
        .btn-outline:hover{background:#fff5f5}

        /* Hero */
        .hero{background:linear-gradient(135deg,#1a1d23 0%,#2a2f3a 55%,#3a2430 100%);color:#fff;padding:70px 0 80px;position:relative;overflow:hidden}
        .hero::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at 80% 20%,rgba(224,39,53,.35),transparent 45%);pointer-events:none}
        .hero .container{position:relative;z-index:2;display:flex;align-items:center;gap:40px}
        .hero .hero-text{flex:1;min-width:0}
        .hero .hero-img{flex-shrink:0;width:380px;max-width:40%}
        .hero .hero-img img{width:100%;height:auto;border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.35)}
        .hero .hero-logo{position:absolute;top:24px;left:20px;width:170px;max-width:28%;z-index:3}
        .hero .hero-logo img{width:100%;height:auto;border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.35)}
        .hero h1{font-size:42px;line-height:1.15;max-width:640px;margin-bottom:16px}
        .hero h1 span{color:#ff6b6b}
        .hero p{max-width:560px;font-size:17px;color:#d6d9e0;margin-bottom:28px}
        .searchbar{display:flex;background:#fff;border-radius:12px;padding:6px;max-width:640px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
        .searchbar input{flex:1;border:0;outline:0;padding:12px 14px;font-size:15px;color:var(--ink);border-radius:8px}
        .searchbar button{background:var(--brand);color:#fff;border:0;border-radius:8px;padding:12px 26px;font-weight:700;font-size:15px;cursor:pointer}
        .searchbar button:hover{background:var(--brand-dark)}
        .hero-tags{margin-top:20px;display:flex;flex-wrap:wrap;gap:8px;font-size:13px;color:#cfd3da}
        .hero-tags span{background:rgba(255,255,255,.08);padding:6px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.15)}

        /* Stats strip */
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin:-36px auto 0;position:relative;z-index:5}
        .stat{background:#fff;border-radius:12px;padding:18px;text-align:center;box-shadow:0 6px 24px rgba(0,0,0,.07);border-top:3px solid var(--brand)}
        .stat b{font-size:26px;display:block;color:var(--ink)}
        .stat span{color:var(--muted);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}

        /* Sections */
        section{padding:56px 0}
        .sec-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:22px}
        .sec-head h2{font-size:26px}
        .sec-head a{color:var(--brand);font-weight:700;font-size:14px}

        /* Category chips */
        .cats{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:12px}
        .cat{background:#fff;border:1px solid var(--line);border-radius:12px;padding:14px 16px;font-weight:700;font-size:15px;transition:.15s;display:flex;align-items:center;gap:10px}
        .cat:hover{border-color:var(--brand);color:var(--brand);transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.06)}
        .cat .ico{width:36px;height:36px;border-radius:9px;background:#fdeaea;color:var(--brand);display:flex;align-items:center;justify-content:center;font-weight:800}

        /* Products */
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px}
        .card{background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden;transition:.15s}
        .card:hover{box-shadow:0 10px 28px rgba(0,0,0,.09);transform:translateY(-3px)}
        .card .thumb{height:170px;background:#f0f1f4;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-size:38px}
        .card .thumb img{width:100%;height:100%;object-fit:cover}
        .card .body{padding:14px 16px}
        .card .name{font-weight:700;font-size:15px;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:40px}
        .card .meta{font-size:12px;color:var(--muted);display:flex;gap:10px;margin-bottom:8px}
        .card .price{font-weight:800;font-size:17px;color:var(--ink)}
        .card .price small{color:var(--muted);font-weight:600;font-size:12px}
        .card .offer{color:#16a34a;font-size:12px;font-weight:600}
        .store-link{display:inline-block;margin-top:8px;color:var(--brand);font-weight:700;font-size:12.5px}

        /* Ad banner */
        .hero-ad{background:linear-gradient(135deg,#7c2d12 0%,#b45309 100%);border-radius:14px;padding:22px 28px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:26px;box-shadow:0 8px 24px rgba(180,83,9,.25)}
        .hero-ad h2{font-size:22px;margin-bottom:4px}
        .hero-ad p{font-size:14px;opacity:.92}
        .hero-ad .btn{background:#fff;color:#b45309;flex-shrink:0}
        .banner{position:relative;border-radius:14px;overflow:hidden;margin-bottom:32px;box-shadow:0 10px 30px rgba(0,0,0,.12)}
        .banner img{width:100%;height:300px;object-fit:cover}
        .banner .cap{position:absolute;left:0;right:0;bottom:0;background:linear-gradient(transparent,rgba(0,0,0,.72));color:#fff;padding:34px 26px 20px}
        .banner .cap h2{font-size:24px}
        .banner .cap a{display:inline-block;margin-top:8px;background:var(--brand);color:#fff;padding:9px 18px;border-radius:8px;font-weight:700;font-size:13.5px}
        .banner-tag{position:absolute;top:14px;left:14px;background:rgba(224,39,53,.95);color:#fff;font-size:11.5px;font-weight:700;padding:5px 10px;border-radius:20px;letter-spacing:.5px;text-transform:uppercase}
        .seller-chip{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid var(--line);border-radius:12px;padding:16px;transition:.15s}
        .seller-chip:hover{border-color:var(--brand);box-shadow:0 6px 16px rgba(0,0,0,.06)}
        .seller-chip .av{width:46px;height:46px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:18px;flex-shrink:0;overflow:hidden}
        .seller-chip .sn{font-weight:700;font-size:14.5px}
        .seller-chip .sm{font-size:12.5px;color:var(--muted)}

        /* How it works */
        .hiw{background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
        .step{background:var(--bg);border-radius:14px;padding:24px}
        .step .num{font-size:26px;font-weight:800;color:var(--brand);margin-bottom:10px}
        .step h3{font-size:17px;margin-bottom:6px}
        .step p{color:var(--muted);font-size:14px}

        /* Roles CTA */
        .roles{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
        .role{border-radius:14px;padding:26px;color:#fff}
        .role.buyer{background:linear-gradient(135deg,#0f766e,#14b8a6)}
        .role.seller{background:linear-gradient(135deg,var(--brand-dark),var(--brand))}
        .role.transporter{background:linear-gradient(135deg,#4338ca,#6366f1)}
        .role h3{font-size:18px;margin-bottom:8px}
        .role p{font-size:14px;opacity:.95;margin-bottom:16px}
        .role .btn{background:#fff;color:#be0b0b}

        footer{background:#16181d;color:#b6bac3;padding:34px 0;font-size:14px}
        footer .container{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}

        .empty{grid-column:1/-1;text-align:center;color:var(--muted);padding:30px 0}
        .hamburger{display:none;background:none;border:none;font-size:26px;cursor:pointer;color:var(--ink);padding:4px}
        .mobile-nav{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:30}
        .mobile-nav.open{display:block}
        .mobile-nav .drawer{position:fixed;top:0;left:0;width:280px;height:100%;background:#fff;z-index:31;padding:24px 20px;overflow-y:auto;box-shadow:4px 0 20px rgba(0,0,0,.15)}
        .mobile-nav .drawer a{display:block;padding:14px 0;font-weight:600;font-size:15px;border-bottom:1px solid var(--line)}
        .mobile-nav .drawer a:hover{color:var(--brand)}
        .mobile-nav .close-btn{background:none;border:none;font-size:28px;cursor:pointer;position:absolute;top:16px;right:16px;color:var(--muted)}

        @media(max-width:720px){
            .hero h1{font-size:30px}
            .hero .container{flex-direction:column}
            .hero .hero-img{width:100%;max-width:100%;margin-top:20px}
            .hero .hero-logo{position:static;width:140px;margin:0 auto 16px}
            .stats{grid-template-columns:repeat(2,1fr);margin-top:0}
            .steps,.roles{grid-template-columns:1fr}
            .nav-links{display:none}
            .hamburger{display:block}
            .nav .btn{display:none}
            header .container{padding:0 16px}
            .hero{padding:40px 0 50px}
            .cats{grid-template-columns:repeat(auto-fill,minmax(140px,1fr))}
            .grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr))}
            .card .thumb{height:120px}
            .card .name{font-size:13px;min-height:auto}
            .card .price{font-size:15px}
            section{padding:36px 0}
            .sec-head h2{font-size:20px}
            .hero-ad{flex-direction:column;text-align:center}
            .banner img{height:180px}
            footer .container{flex-direction:column;text-align:center}
        }
    </style>
</head>
<body>

<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/">
            <img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:54px;width:auto">
        </a>
        <button class="hamburger" onclick="document.getElementById('mobileNav').classList.add('open')">&#9776;</button>
        <nav class="nav-links">
            <a href="<?= esc($base) ?>/catalog">Buy on Loha Mandi</a>
            <a href="<?= esc($base) ?>/suppliers">Sell on Loha Mandi</a>
            <a href="<?= esc($base) ?>/post-rfq">Post RFQ</a>
            <a href="<?= esc($base) ?>/faq">Why LOHA Mandi</a>
            <a href="#how-it-works">How It Works</a>
            <a href="<?= esc($base) ?>/admin/login" style="color:var(--brand);font-size:13px">⚙ Admin</a>
            <?php if (!empty($auth['logged_in'])): ?>
                <a href="<?= esc($base) ?>/account">Dashboard</a>
                <a href="<?= esc($base) ?>/logout">Sign out</a>
            <?php else: ?>
                <a href="<?= esc($base) ?>/login">Login</a>
                <a href="<?= esc($base) ?>/register">Register</a>
            <?php endif; ?>
        </nav>
        <div>
            <?php if (!empty($auth['logged_in'])): ?>
                <a class="btn btn-primary" href="<?= esc($base) ?>/account"><?= esc($auth['name']) ?></a>
            <?php else: ?>
                <a class="btn btn-outline" href="#" onclick="scrollSearch()">Post RFQ</a>
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
        <a href="<?= esc($base) ?>/post-rfq">Post RFQ</a>
        <a href="<?= esc($base) ?>/faq">Why LOHA Mandi</a>
        <a href="<?= esc($base) ?>/transport">Transport</a>
        <a href="#how-it-works" onclick="document.getElementById('mobileNav').classList.remove('open')">How It Works</a>
        <?php if (!empty($auth['logged_in'])): ?>
            <a href="<?= esc($base) ?>/account">Dashboard</a>
            <a href="<?= esc($base) ?>/logout">Sign out</a>
        <?php else: ?>
            <a href="<?= esc($base) ?>/login">Login</a>
            <a href="<?= esc($base) ?>/register">Join Loha Mandi</a>
        <?php endif; ?>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <!-- <div class="hero-logo">
        <img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi">
    </div> -->
    <div class="container">
        <div class="hero-text">
            <h1>Buy &amp; Sell Iron &amp; Steel at <span>Loha Mandi</span></h1>
            <p>The Digital Marketplace of Loha Mandi, Ghaziabad. Discover verified steel traders, TMT suppliers, pipe &amp; plate dealers. Compare prices, post RFQs and get quotations from multiple sellers.</p>
            <form class="searchbar" action="<?= esc($base) ?>/catalog" method="get">
                <input type="text" name="q" id="searchQ" placeholder="Search TMT bars, MS plates, GI pipes, steel scrap…" autocomplete="off">
                <button type="submit">Search</button>
            </form>
            <div class="hero-tags">
                <span>TMT Bars</span><span>MS Plate</span><span>GI Pipe</span><span>Steel Scrap</span><span>Square Pipe</span><span>Structural Steel</span>
            </div>
        </div>
        <div class="hero-img">
            <img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi — Digital Marketplace of Loha Mandi, Ghaziabad">
        </div>
    </div>
</div>

<!-- STATS -->
<div class="container">
    <div class="stats">
        <div class="stat"><b><?= number_format($stats['products']) ?></b><span>Products</span></div>
        <div class="stat"><b><?= number_format($stats['sellers']) ?></b><span>Active Sellers</span></div>
        <div class="stat"><b><?= number_format($stats['categories']) ?></b><span>Categories</span></div>
        <div class="stat"><b><?= number_format($stats['rfqs']) ?></b><span>RFQs Posted</span></div>
    </div>
</div>

<!-- FEATURED SELLERS (ads) -->
<?php if (!empty($featuredSellers)): ?>
<section id="featured-sellers">
    <div class="container">
        <div class="sec-head"><h2>Featured Sellers</h2><a href="<?= esc($base) ?>/suppliers">View all sellers →</a></div>
        <div class="cats">
            <?php foreach ($featuredSellers as $fs): ?>
                <a class="seller-chip" href="<?= esc($base) ?>/ad/view/<?= (int)$fs['id'] ?>">
                    <?php if (!empty($fs['image'])): ?>
                        <img src="<?= esc(base_url('uploads/' . $fs['image'])) ?>" alt="<?= esc($fs['business_name'] ?? '') ?>" style="width:46px;height:46px;border-radius:50%;object-fit:cover;flex-shrink:0">
                    <?php else: ?>
                        <div class="av"><?php if (!empty($fs['logo'])): ?><img src="<?= esc(base_url('uploads/' . $fs['logo'])) ?>" alt="<?= esc($fs['business_name']) ?> logo" style="width:100%;height:100%;object-fit:cover"><?php else: ?><?= esc(strtoupper(substr($fs['business_name'] ?? 'S', 0, 1))) ?><?php endif; ?></div>
                    <?php endif; ?>
                    <div>
                        <div class="sn"><?= esc($fs['business_name']) ?></div>
                        <div class="sm"><?= esc($fs['city'] ?? '') ?> · ★ <?= number_format((float)$fs['rating_avg'], 1) ?> <?= $fs['gst_verified'] ? '· GST ✓' : '' ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- HERO BANNER (ads) -->
<section id="hero-banner">
    <div class="container">
        <?php if (!empty($heroBanners)): foreach ($heroBanners as $b):
            $src = $b['image'] ? base_url('uploads/' . $b['image']) : null; ?>
            <a class="banner" href="<?= esc($base) ?>/ad/go/<?= (int)$b['id'] ?>">
                <span class="banner-tag">Ad</span>
                <?php if ($src): ?>
                    <img src="<?= esc($src) ?>" alt="<?= esc($b['title']) ?>" loading="lazy">
                <?php else: ?>
                    <div style="width:100%;height:300px;background:linear-gradient(135deg,#7c2d12,#b45309)"></div>
                <?php endif; ?>
                <div class="cap">
                    <h2><?= esc($b['title']) ?></h2>
                    <?php if ($b['link']): ?><a href="<?= esc($base) ?>/ad/go/<?= (int)$b['id'] ?>">Learn more →</a><?php endif; ?>
                </div>
            </a>
        <?php endforeach; endif; ?>
    </div>
</section>

<!-- CATEGORIES -->
<section id="categories">
    <div class="container">
        <div class="sec-head">
            <h2>Browse by Category</h2>
            <a href="<?= esc($base) ?>/catalog">View all</a>
        </div>
        <div class="cats">
            <?php foreach (array_slice($categories, 0, 12) as $c): ?>
                <a class="cat" href="<?= esc($base) ?>/catalog/category/<?= esc($c['slug']) ?>">
                    <span class="ico"><?= esc(strtoupper(substr($c['name'], 0, 1))) ?></span>
                    <?= esc($c['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SPONSORED PRODUCTS (ads) -->
<?php if (!empty($sponsored)): ?>
<section id="sponsored">
    <div class="container">
        <div class="sec-head">
            <h2>Sponsored Products</h2>
            <span class="pill" style="color:var(--muted);font-size:12.5px">Sponsored</span>
        </div>
        <div class="grid">
            <?php foreach ($sponsored as $sp): ?>
                <a class="card" href="<?= esc($base) ?>/ad/go/<?= (int)$sp['id'] ?>">
                    <div class="thumb">
                        <?php if (!empty($sp['primary_image_path'])): ?>
                            <img src="<?= esc(base_url('uploads/' . $sp['primary_image_path'])) ?>" alt="<?= esc($sp['product_name']) ?>" loading="lazy">
                        <?php else: ?>
                            ⚙️
                        <?php endif; ?>
                    </div>
                    <div class="body">
                        <div class="name"><?= esc($sp['product_name'] ?? $sp['title']) ?></div>
                        <div class="meta"><span class="offer">★ Sponsored</span><span><?= esc($sp['business_name'] ?? '') ?></span></div>
                        <?php if (!empty($sp['offer_price']) && (float) $sp['offer_price'] > 0): ?>
                            <div class="price">₹<?= number_format((float)$sp['offer_price']) ?> <small>/<?= esc($sp['unit']) ?></small></div>
                        <?php elseif (!empty($sp['price']) && (float) $sp['price'] > 0): ?>
                            <div class="price">₹<?= number_format((float)$sp['price']) ?> <small>/<?= esc($sp['unit']) ?></small></div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- PRODUCTS -->
<section id="products">
    <div class="container">
        <div class="sec-head">
            <h2>Featured Products</h2>
            <a href="<?= esc($base) ?>/catalog">Browse all products →</a>
        </div>
        <div class="grid">
            <?php if (empty($featured)): ?>
                <div class="empty">No products yet. Sellers can add products once verified.</div>
            <?php else: ?>
                <?php foreach ($featured as $p): ?>
                    <a class="card" href="<?= esc($base) ?>/product/<?= esc($p['slug']) ?>">
                        <div class="thumb">
                            <?php if (!empty($p['primary_image'])): ?>
                                <img src="<?= esc(base_url('uploads/' . $p['primary_image'])) ?>" alt="<?= esc($p['name']) ?>" loading="lazy">
                            <?php else: ?>
                                ⚙️
                            <?php endif; ?>
                        </div>
                        <div class="body">
                            <div class="name"><?= esc($p['name']) ?></div>
                            <div class="meta">
                                <span><?= esc($p['category_name'] ?? $p['grade'] ?? '') ?></span>
                                <span><?= esc($p['location'] ?? '') ?></span>
                            </div>
                            <?php if (!empty($p['offer_price']) && (float) $p['offer_price'] > 0): ?>
                                <div class="price">₹<?= number_format((float)$p['offer_price']) ?> <small>/<?= esc($p['unit']) ?></small></div>
                                <?php if (!empty($p['price']) && $p['price'] > $p['offer_price']): ?>
                                    <div class="offer">List ₹<?= number_format((float)$p['price']) ?> · Save <?= round((1 - $p['offer_price'] / $p['price']) * 100) ?>%</div>
                                <?php endif; ?>
                            <?php elseif (!empty($p['price']) && (float) $p['price'] > 0): ?>
                                <div class="price">₹<?= number_format((float)$p['price']) ?> <small>/<?= esc($p['unit']) ?></small></div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how-it-works" class="hiw">
    <div class="container">
        <div class="sec-head"><h2>How LOHA Mandi Works</h2></div>
        <div class="steps">
            <div class="step"><div class="num">01</div><h3>Discover</h3><p>Search products and find verified steel suppliers in Ghaziabad by category, brand or location.</p></div>
            <div class="step"><div class="num">02</div><h3>Post RFQ</h3><p>Send a Request for Quotation with your quantity &amp; budget. Matching sellers are notified instantly.</p></div>
            <div class="step"><div class="num">03</div><h3>Compare &amp; Buy</h3><p>Receive multiple quotations, compare GST-inclusive pricing, accept the best deal and close the order.</p></div>
        </div>
    </div>
</section>

<!-- ROLES -->
<section>
    <div class="container">
        <div class="sec-head"><h2>Built for the Steel Ecosystem</h2></div>
        <div class="roles">
            <div class="role buyer"><h3>Buyers</h3><p>Builders, fabricators, contractors, industries &amp; retail shops sourcing steel with RFQ &amp; price comparison.</p><a class="btn" href="<?= esc($base) ?>/post-rfq">Post an RFQ</a></div>
            <div class="role seller"><h3>Sellers</h3><p>Steel traders, TMT/pipe/plate dealers &amp; manufacturers showcasing products and managing leads.</p><a class="btn" href="<?= esc($base) ?>/suppliers">Browse Suppliers</a></div>
            <div class="role transporter"><h3>Transporters</h3><p>Vehicle listing, freight quotations and live tracking for steel movement. (Coming in a later phase.)</p><a class="btn" href="<?= esc($base) ?>/transport">Get Listed</a></div>
        </div>
    </div>
</section>

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
            <b>Developed & Maintained by</b>
            VDS INFOTECH
        </div>
    </div>
    <div class="container" style="border-top:1px solid rgba(255,255,255,.08);margin-top:20px;padding-top:16px;justify-content:center;font-size:12px">
        &copy; 2026 LOHA Mandi. All rights reserved.
    </div>
</footer>

<script>
function scrollSearch(){
    const el = document.getElementById('searchQ');
    if(el){ el.focus(); window.scrollTo({top:0,behavior:'smooth'}); }
    return false;
}
</script>
</body>
</html>
