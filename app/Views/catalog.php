<?php
/** Public product catalog listing page. */
$base = rtrim(base_url(), '/');
$isCat = !empty($selected);
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
        img{max-width:100%;display:block}
        .container{max-width:1180px;margin:0 auto;padding:0 20px}
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:62px;gap:20px;flex-wrap:wrap}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:19px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:var(--brand);display:inline-block}
        .nav form.fb{display:flex;flex:1;max-width:420px;min-width:200px;background:var(--bg);border:1px solid var(--line);border-radius:9px;overflow:hidden}
        .nav form.fb input{flex:1;border:0;outline:0;padding:9px 12px;font-size:14px;background:transparent}
        .nav form.fb button{background:var(--brand);color:#fff;border:0;padding:0 18px;font-weight:700;cursor:pointer}
        .nav form.fb button:hover{background:var(--brand-dark)}

        .crumb{background:#fff;border-bottom:1px solid var(--line);padding:13px 0;font-size:13px}
        .crumb a{color:var(--muted)}.crumb a:hover{color:var(--brand)}
        .crumb b{color:var(--ink)}

        .layout{display:grid;grid-template-columns:250px 1fr;gap:26px;padding:26px 0 60px;align-items:start}
        .filters{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px;position:sticky;top:78px}
        .filters h3{font-size:15px;margin-bottom:12px}
        .filters .fgroup{margin-bottom:16px}
        .filters .fgroup label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px}
        .filters a.fitem{display:block;padding:6px 10px;border-radius:8px;font-size:13.5px;color:var(--muted);margin-bottom:2px}
        .filters a.fitem:hover{background:var(--bg);color:var(--brand)}
        .filters a.fitem.on{background:#fdeaea;color:var(--brand);font-weight:700}

        .topbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:16px}
        .topbar h1{font-size:22px}
        .topbar .count{color:var(--muted);font-size:13px;font-weight:600}
        .topbar select{border:1px solid var(--line);border-radius:8px;padding:8px 12px;font-size:13.5px;background:#fff;color:var(--ink)}
        .topbar select:focus{outline:2px solid var(--brand)}

        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px}
        .card{background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden;transition:.15s;display:flex;flex-direction:column}
        .card:hover{box-shadow:0 10px 28px rgba(0,0,0,.09);transform:translateY(-3px)}
        .card .thumb{height:160px;background:#f0f1f4;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-size:34px}
        .card .thumb img{width:100%;height:100%;object-fit:cover}
        .card .body{padding:14px 16px;display:flex;flex-direction:column;flex:1}
        .card .name{font-weight:700;font-size:15px;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:40px}
        .card .meta{font-size:12px;color:var(--muted);display:flex;gap:10px;margin-bottom:8px}
        .card .price{font-weight:800;font-size:17px}
        .card .price small{color:var(--muted);font-weight:600;font-size:12px}
        .card .offer{color:#16a34a;font-size:12px;font-weight:600}
        .card .seller{margin-top:auto;padding-top:8px;color:var(--brand);font-weight:700;font-size:12.5px}
        .card .seller:hover{text-decoration:underline}

        .empty{grid-column:1/-1;text-align:center;color:var(--muted);padding:40px 0}

        .pager{display:flex;gap:8px;justify-content:center;margin-top:26px}
        .pager a,.pager span{min-width:38px;padding:8px 12px;text-align:center;border:1px solid var(--line);border-radius:8px;background:#fff;font-size:13.5px;font-weight:600}
        .pager a:hover{border-color:var(--brand);color:var(--brand)}
        .pager .cur{background:var(--brand);color:#fff;border-color:var(--brand)}

        footer{background:#16181d;color:#b6bac3;padding:26px 0;font-size:13.5px}
        footer .container{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}

        @media(max-width:820px){.layout{grid-template-columns:1fr}.filters{position:static}.nav-form{order:3;width:100%}}
    </style>
</head>
<body>

<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:48px;width:auto"></a>
        <form class="fb" action="<?= esc($base) ?>/catalog" method="get">
            <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search products, brands, sellers…" autocomplete="off">
            <button type="submit">Search</button>
        </form>
        <a class="brand" href="<?= esc($base) ?>/" style="font-size:14px;font-weight:600;color:var(--muted)">Home</a>
    </div>
</header>

<div class="crumb">
    <div class="container">
        <a href="<?= esc($base) ?>/">Home</a> / <a href="<?= esc($base) ?>/catalog">Catalog</a>
        <?php if ($isCat): ?> / <b><?= esc($selected['name']) ?></b><?php endif; ?>
        <?php if ($q !== ''): ?> / <b>Search: "<?= esc($q) ?>"</b><?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="layout">

        <!-- FILTERS -->
        <aside class="filters">
            <h3>Categories</h3>
            <div class="fgroup">
                <a class="fitem<?= !$isCat && $category_id === 0 ? ' on' : '' ?>" href="<?= esc($base) ?>/catalog">All Products</a>
                <?php foreach ($categories as $c): ?>
                    <a class="fitem<?= $isCat && $c['id'] == $selected['id'] ? ' on' : '' ?>" href="<?= esc($base) ?>/catalog/category/<?= esc($c['slug']) ?>">
                        <?= esc($c['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <!-- RESULTS -->
        <main>
            <div class="topbar">
                <div>
                    <h1>
                        <?php if ($isCat): ?><?= esc($selected['name']) ?>
                        <?php elseif ($q !== ''): ?>Results for "<?= esc($q) ?>"
                        <?php else: ?>Browse Products<?php endif; ?>
                    </h1>
                    <div class="count"><?= number_format((int) $total) ?> product<?= (int) $total === 1 ? '' : 's' ?></div>
                </div>
                <select onchange="if(this.value) location.href=this.value;">
                    <option value="">Sort by</option>
                    <?php $qs = ($category_id > 0 ? 'category=' . (int) $category_id . '&' : '') . ($q !== '' ? 'q=' . urlencode($q) . '&' : ''); ?>
                    <option value="<?= esc($base) ?>/catalog?<?= $qs ?>sort=featured&page=1">Featured</option>
                    <option value="<?= esc($base) ?>/catalog?<?= $qs ?>sort=price_asc&page=1">Price: Low → High</option>
                    <option value="<?= esc($base) ?>/catalog?<?= $qs ?>sort=price_desc&page=1">Price: High → Low</option>
                    <option value="<?= esc($base) ?>/catalog?<?= $qs ?>sort=newest&page=1">Newest</option>
                </select>
            </div>

            <?php if (!empty($banners)): foreach ($banners as $b):
                $src = $b['image'] ? base_url('uploads/' . $b['image']) : null; ?>
                <a href="<?= esc($base) ?>/ad/go/<?= (int)$b['id'] ?>" style="display:block;border-radius:12px;overflow:hidden;margin-bottom:18px;box-shadow:0 6px 18px rgba(0,0,0,.08)">
                    <?php if ($src): ?>
                        <img src="<?= esc($src) ?>" alt="<?= esc($b['title']) ?>" style="width:100%;height:140px;object-fit:cover" loading="lazy">
                    <?php else: ?>
                        <div style="height:140px;background:linear-gradient(135deg,#7c2d12,#b45309);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px"><?= esc($b['title']) ?></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; endif; ?>

            <div class="grid">
                <?php if (empty($products)): ?>
                    <div class="empty">No products found. <a href="<?= esc($base) ?>/catalog" style="color:var(--brand);font-weight:700">Clear filters</a></div>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <a class="card" href="<?= esc($p['product_url']) ?>">
                            <div class="thumb">
                                <?php if (!empty($p['primary_image'])): ?>
                                    <img src="<?= esc(base_url('uploads/' . $p['primary_image'])) ?>" alt="<?= esc($p['name']) ?>" loading="lazy">
                                <?php else: ?>
                                    🏗️
                                <?php endif; ?>
                            </div>
                            <div class="body">
                                <div class="name"><?= esc($p['name']) ?></div>
                                <div class="meta">
                                    <span><?= esc($p['category_name'] ?? $p['grade'] ?? '') ?></span>
                                    <span><?= esc($p['location'] ?? '') ?></span>
                                </div>
                                <?php if (!empty($p['offer_price']) && (float) $p['offer_price'] > 0): ?>
                                    <div class="price">₹<?= number_format((float) $p['offer_price']) ?> <small>/<?= esc($p['unit']) ?></small></div>
                                    <?php if (!empty($p['price']) && $p['price'] > $p['offer_price']): ?>
                                        <div class="offer">List ₹<?= number_format((float) $p['price']) ?> · Save <?= round((1 - $p['offer_price'] / $p['price']) * 100) ?>%</div>
                                    <?php endif; ?>
                                <?php elseif (!empty($p['price']) && (float) $p['price'] > 0): ?>
                                    <div class="price">₹<?= number_format((float) $p['price']) ?> <small>/<?= esc($p['unit']) ?></small></div>
                                <?php endif; ?>
                                <div class="seller"><?= esc($p['business_name'] ?? 'Seller') ?> →</div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1):
                $qs = [];
                if ($category_id > 0) $qs[] = 'category=' . (int) $category_id;
                if ($q !== '')      $qs[] = 'q=' . urlencode($q);
                $qs[] = 'sort=' . urlencode($sort);
                $qs = implode('&', $qs);
                ?>
                <nav class="pager">
                    <?php if ($page > 1): ?>
                        <a href="?<?= $qs ?>&page=<?= $page - 1 ?>">‹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="cur"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= $qs ?>&page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= $qs ?>&page=<?= $page + 1 ?>">›</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </main>
    </div>
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
