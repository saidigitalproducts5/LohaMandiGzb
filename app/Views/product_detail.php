<?php
/** Public product detail page. */
$base = rtrim(base_url(), '/');
$p = $product; $s = $seller;
$effective = ($p['offer_price'] && (float) $p['offer_price'] > 0) ? $p['offer_price'] : $p['price'];
$discount = 0;
if ($p['offer_price'] && $p['price'] && $p['price'] > $p['offer_price']) {
    $discount = round((1 - $p['offer_price'] / $p['price']) * 100);
}
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
        .container{max-width:1160px;margin:0 auto;padding:0 20px}
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:62px;gap:20px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:19px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:var(--brand);display:inline-block}
        .btn{padding:10px 18px;border-radius:9px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.15s;text-align:center}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark)}
        .btn-outline{background:#fff;color:var(--brand);border:1px solid var(--brand)}
        .btn-outline:hover{background:#fff5f5}
        .crumb{background:#fff;border-bottom:1px solid var(--line);padding:12px 0;font-size:13px}
        .crumb a{color:var(--muted)}.crumb a:hover{color:var(--brand)}
        .crumb b{color:var(--ink)}

        .layout{display:grid;grid-template-columns:1.15fr 1fr;gap:30px;padding:30px 0 56px;align-items:start}
        .gallery{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;position:sticky;top:78px}
        .gallery .main{height:340px;background:#f0f1f4;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-size:64px}
        .gallery .main img{width:100%;height:100%;object-fit:contain}
        .gallery .thumbs{display:flex;gap:8px;padding:10px;border-top:1px solid var(--line);overflow-x:auto}
        .gallery .thumbs .t{width:74px;height:74px;flex-shrink:0;border:2px solid var(--line);border-radius:10px;padding:4px;background:#fafafa;cursor:pointer}
        .gallery .thumbs .t.on{border-color:var(--brand)}

        .panel{background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px}
        .cat{color:var(--brand);font-weight:700;font-size:12.5px;text-transform:uppercase;letter-spacing:.5px}
        .panel h1{font-size:24px;margin:6px 0;line-height:1.25}
        .brandline{color:var(--muted);font-size:14px;margin-bottom:12px}
        .price{margin:14px 0}
        .price .cur{font-size:30px;font-weight:800}
        .price .cur small{color:var(--muted);font-weight:600;font-size:14px}
        .price .mrp{color:var(--muted);text-decoration:line-through;font-size:16px;margin-left:8px}
        .price .off{color:#16a34a;font-weight:700;font-size:14px;margin-left:6px}
        .tax-line{font-size:12.5px;color:var(--muted);margin-bottom:14px}
        .specs{white-space:pre-wrap;color:var(--muted);font-size:13.5px;margin-bottom:16px}

        .btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
        .cta-line{text-align:center;color:var(--muted);font-size:13px}

        .detail-box{background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px}
        .detail-box h2{font-size:17px;margin-bottom:14px}
        .dtable{width:100%;border-collapse:collapse;font-size:13.5px}
        .dtable td{padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top}
        .dtable td:first-child{color:var(--muted);width:38%;font-weight:600}
        .dtable td:last-child{color:var(--ink)}
        .dtable tr:last-child td{border-bottom:0}
        .spec-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 22px}
        .spec-grid td:first-child{width:44%}
        .bulk-title{font-weight:800;color:var(--ink);background:var(--bg)}
        .bulk-title td{padding-bottom:4px}

        .seller-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;margin-top:26px;display:flex;gap:16px;align-items:flex-start}
        .seller-card .avatar{width:64px;height:64px;border-radius:14px;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;flex-shrink:0;overflow:hidden}
        .seller-card h3{font-size:16px;margin-bottom:4px}
        .seller-card .badges{display:flex;flex-wrap:wrap;gap:6px;margin:8px 0}
        .seller-card .badges span{background:var(--bg);padding:3px 9px;border-radius:14px;font-size:11.5px;font-weight:600;color:var(--muted)}
        .seller-card .badges span.ok{background:#e7f6ec;color:#16a34a}
        .seller-card .actions{margin-top:10px;display:flex;gap:8px;flex-wrap:wrap}

        .like-cta{background:#fff;border:1px solid var(--line);border-radius:12px;padding:12px 14px;font-size:13px;color:var(--muted);margin-bottom:14px}
        .btn.btn-block{width:100%}

        .rela{margin-bottom:26px}
        .rela h2{font-size:17px;margin-bottom:14px}
        .rel-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px}
        .rel-card{background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden;transition:.15s}
        .rel-card:hover{box-shadow:0 8px 20px rgba(0,0,0,.09);transform:translateY(-2px)}
        .rel-card .thumb{height:110px;background:#f0f1f4;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-size:26px}
        .rel-card .thumb img{width:100%;height:100%;object-fit:cover}
        .rel-card .body{padding:10px 12px}
        .rel-card .name{font-size:13px;font-weight:700;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .rel-card .price{font-size:14px;font-weight:800}

        footer{background:#16181d;color:#b6bac3;padding:26px 0;font-size:13.5px}
        footer .container{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}

        @media(max-width:820px){.layout{grid-template-columns:1fr}.gallery{position:static}.spec-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>

<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:48px;width:auto"></a>
        <a class="btn btn-outline" style="padding:7px 14px;font-size:13px" href="<?= esc($base) ?>/catalog">← Catalog</a>
    </div>
</header>

<div class="crumb">
    <div class="container">
        <a href="<?= esc($base) ?>/">Home</a> /
        <a href="<?= esc($base) ?>/catalog">Catalog</a> /
        <a href="<?= esc($p['category_url']) ?>"><?= esc($p['category_name'] ?? 'Products') ?></a> /
        <b><?= esc($p['name']) ?></b>
    </div>
</div>

<div class="container">
    <div class="layout">

        <!-- GALLERY -->
        <div class="gallery">
            <div class="main" id="mainImg">
                <?php if (!empty($p['images']) && !empty($p['primary_image'])): ?>
                    <img src="<?= esc(base_url('uploads/' . $p['primary_image'])) ?>" alt="<?= esc($p['name']) ?>" id="mainSrc">
                <?php else: ?>
                    🏗️
                <?php endif; ?>
            </div>
            <?php if (count($p['images']) > 1): ?>
                <div class="thumbs">
                    <?php foreach ($p['images'] as $i => $img): ?>
                        <div class="t<?= $i === 0 ? ' on' : '' ?>" onclick="swap(this,'<?= esc(base_url('uploads/' . $img['file_path'])) ?>')">
                            <?php if (!empty($img['file_path'])): ?><img src="<?= esc(base_url('uploads/' . $img['file_path'])) ?>" style="width:100%;height:100%;object-fit:cover"><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- INFO -->
        <div>
            <div class="panel">
                <div class="cat"><?= esc($p['category_name'] ?? '') ?></div>
                <h1><?= esc($p['name']) ?></h1>
                <?php if (!empty($p['brand_name'])): ?><div class="brand">Brand: <?= esc($p['brand_name']) ?></div><?php endif; ?>

                <div class="price">
                    <span class="cur">₹<?= number_format((float) $effective) ?> <small>/ <?= esc($p['unit']) ?></small></span>
                    <?php if ($discount > 0): ?>
                        <span class="mrp">₹<?= number_format((float) $p['price']) ?></span>
                        <span class="off"><?= $discount ?>% off</span>
                    <?php endif; ?>
                </div>
                <div class="tax-line">Inclusive of GST @ <?= number_format((float) $p['gst_rate'], 2) ?>%<?= $p['location'] ? ' · Dispatch from ' . esc($p['location']) : '' ?></div>

                <?php if (!empty($p['description'])): ?><div class="specs"><?= esc($p['description']) ?></div><?php endif; ?>

                <?php if (empty($auth['logged_in'])): ?>
                    <div class="like-cta">💬 <a href="<?= esc($base) ?>/login" style="color:var(--brand);font-weight:700">Login</a> to send an inquiry or chat with the seller.</div>
                <?php elseif ($auth['role'] !== 'seller'): ?>
                    <div class="btns">
                        <a class="btn btn-primary" href="<?= esc($base) ?>/account/chats/open/<?= (int) $s['id'] ?>">💬 Chat with seller</a>
                        <a class="btn btn-outline" href="<?= esc($base) ?>/account/rfqs/new">Post RFQ</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- DETAILS -->
            <div class="detail-box" style="margin-top:26px">
                <h2>Product Details</h2>
                <table class="dtable">
                    <tr><td>Category</td><td><?= esc($p['category_name'] ?? '-') ?></td></tr>
                    <?php if (!empty($p['brand_name'])): ?><tr><td>Brand</td><td><?= esc($p['brand_name']) ?></td></tr><?php endif; ?>
                    <?php if (!empty($p['grade'])): ?><tr><td>Grade</td><td><?= esc($p['grade']) ?></td></tr><?php endif; ?>
                    <?php if (!empty($p['weight'])): ?><tr><td>Weight</td><td><?= esc($p['weight']) ?></td></tr><?php endif; ?>
                    <?php if (!empty($p['dimensions'])): ?><tr><td>Dimensions</td><td><?= esc($p['dimensions']) ?></td></tr><?php endif; ?>
                    <tr><td>MOQ</td><td><?= number_format((float) $p['moq'], 0) ?> <?= esc($p['unit']) ?></td></tr>
                    <tr><td>Stock</td><td><?= number_format((float) $p['available_stock'], 0) ?> <?= esc($p['unit']) ?></td></tr>
                    <?php if (!empty($p['delivery_time'])): ?><tr><td>Delivery</td><td><?= esc($p['delivery_time']) ?></td></tr><?php endif; ?>
                    <?php if (!empty($p['hsn_code'])): ?><tr><td>HSN</td><td><?= esc($p['hsn_code']) ?></td></tr><?php endif; ?>
                    <tr><td>Bulk Order</td><td><?= $p['is_bulk'] ? 'Yes' : 'No' ?></td></tr>
                    <tr><td>Location</td><td><?= esc($p['location'] ?? '-') ?></td></tr>
                </table>

                <?php if (!empty($p['specifications'])): ?>
                    <h2 style="margin-top:22px">Specifications</h2>
                    <table class="dtable spec-grid">
                        <?php foreach ($specs as $k => $v): ?>
                            <tr><td><?= esc(ucfirst(str_replace('_', ' ', $k))) ?></td><td><?= esc(is_array($v) ? json_encode($v) : $v) ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>

                <?php if (!empty($p['bulk_prices'])): ?>
                    <h2 style="margin-top:22px">Bulk Pricing</h2>
                    <table class="dtable">
                        <tr class="bulk-title"><td>Quantity</td><td>Unit Price</td></tr>
                        <?php foreach ($p['bulk_prices'] as $bp): ?>
                            <tr>
                                <td><?= number_format((float) $bp['min_qty'], 0) ?><?= isset($bp['max_qty']) && $bp['max_qty'] ? ' – ' . number_format((float) $bp['max_qty'], 0) : '+' ?> <?= esc($p['unit']) ?></td>
                                <td>₹<?= number_format((float) $bp['unit_price']) ?> / <?= esc($p['unit']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>

            <!-- SELLER -->
            <div class="seller-card">
                <a class="avatar" href="<?= esc($s['slug'] ? base_url('seller/' . $s['slug']) : '#') ?>"><?php if (!empty($s['logo'])): ?><img src="<?= esc(base_url('uploads/' . $s['logo'])) ?>" alt="<?= esc($s['business_name']) ?> logo" style="width:100%;height:100%;object-fit:cover"><?php else: ?><?= esc(strtoupper(substr($s['business_name'] ?? 'S', 0, 1))) ?><?php endif; ?></a>
                <div style="flex:1">
                    <h3><a href="<?= esc($s['slug'] ? base_url('seller/' . $s['slug']) : '#') ?>"><?= esc($s['business_name'] ?? 'Seller') ?></a></h3>
                    <div style="color:var(--muted);font-size:13px"><?= esc($s['city'] ?? 'Ghaziabad') ?><?= $s['state'] ? ', ' . esc($s['state']) : '' ?></div>
                    <div class="badges">
                        <?php if ($s['gst_verified']): ?><span class="ok">✓ GST Verified</span><?php endif; ?>
                        <?php if ($s['is_verified']): ?><span class="ok">✓ Verified</span><?php endif; ?>
                        <?php if ($s['is_premium']): ?><span class="ok">★ Premium</span><?php endif; ?>
                        <?php if ($s['rating_count']): ?><span>★ <?= number_format((float) $s['rating_avg'], 1) ?> (<?= (int) $s['rating_count'] ?>)</span><?php endif; ?>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px">
                        <a href="<?= esc($s['slug'] ? base_url('seller/' . $s['slug']) : '#') ?>" class="btn btn-outline" style="padding:7px 14px;font-size:13px">View storefront</a>
                        <?php if (!empty($s['owner_phone'])): ?>
                            <a href="tel:+91<?= esc(preg_replace('/\D/', '', $s['owner_phone'])) ?>" class="btn btn-primary" style="padding:7px 14px;font-size:13px">Contact supplier · <?= esc($s['owner_phone']) ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($auth['logged_in'] && $auth['role'] !== 'seller'): ?>
                    <a class="btn btn-primary" href="<?= esc($base) ?>/account/chats/open/<?= (int) $s['id'] ?>" style="flex-shrink:0">💬</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($related)): ?>
        <div class="rela">
            <h2>Similar Products</h2>
            <div class="rel-grid">
                <?php foreach ($related as $r): ?>
                    <a class="rel-card" href="<?= esc(base_url('product/' . $r['slug'])) ?>">
                        <div class="thumb">
                            <?php if (!empty($r['primary_image'])): ?><img src="<?= esc(base_url('uploads/' . $r['primary_image'])) ?>" alt="" loading="lazy"><?php else: ?>🏗️<?php endif; ?>
                        </div>
                        <div class="body">
                            <div class="name"><?= esc($r['name']) ?></div>
                            <div class="price">₹<?= number_format((float) ($r['offer_price'] ?: $r['price'])) ?> <small>/<?= esc($r['unit']) ?></small></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
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

<script>
function swap(el, url){
    if (!url) return;
    var main = document.getElementById('mainSrc');
    var wrap = document.getElementById('mainImg');
    if (main) {
        main.src = url;
    } else if (wrap) {
        wrap.innerHTML = '<img src="' + url + '" alt="" id="mainSrc">';
    }
    var thumbs = document.querySelectorAll('.gallery .thumbs .t');
    thumbs.forEach(function(t){ t.classList.remove('on'); });
    if (el) { el.classList.add('on'); }
}
function showImg(i){
    var thumbs = document.querySelectorAll('.gallery .thumbs .t');
    if (!thumbs[i]) return;
    swap(thumbs[i], thumbs[i].querySelector('img') ? thumbs[i].querySelector('img').src : null);
}
</script>

</body>
</html>
