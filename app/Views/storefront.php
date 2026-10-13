<?php
/** Public seller storefront page. */
$base = rtrim(base_url(), '/');
$s = $seller;
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
        .nav{display:flex;align-items:center;justify-content:space-between;height:62px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:19px}
        .brand .dot{width:12px;height:12px;border-radius:3px;background:var(--brand);display:inline-block}

        .hero{background:linear-gradient(135deg,#1a1d23 0%,#2a2f3a 60%,#3a2430 100%);color:#fff;padding:46px 0}
        .hero .top{display:flex;gap:22px;align-items:center;flex-wrap:wrap}
        .logo{width:84px;height:84px;border-radius:18px;background:var(--brand);display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:800;color:#fff;flex-shrink:0}
        .hero h1{font-size:30px;margin-bottom:6px}
        .hero .sub{color:#cfd3da;margin-bottom:14px}
        .badges{display:flex;flex-wrap:wrap;gap:8px}
        .badges span{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);padding:5px 12px;border-radius:20px;font-size:12.5px;font-weight:600}
        .badges span.ok{background:rgba(22,163,74,.22);border-color:rgba(22,163,74,.5);color:#7ee2a8}

        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin:-24px auto 0;position:relative;z-index:5}
        .stat{background:#fff;border-radius:12px;padding:16px;text-align:center;box-shadow:0 6px 24px rgba(0,0,0,.07);border-top:3px solid var(--brand)}
        .stat b{font-size:24px;display:block}
        .stat span{color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}

        section{padding:48px 0}
        .sec-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px}
        .sec-head h2{font-size:22px}
        .sec-head a{color:var(--brand);font-weight:700;font-size:14px}

        .about{background:#fff;border:1px solid var(--line);border-radius:14px;padding:22px}
        .about h3{margin-bottom:10px;font-size:16px}
        .about p{color:var(--muted);font-size:14px}
        .about .info{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-top:16px}
        .about .info div{font-size:13px;color:var(--muted)}
        .about .info b{color:var(--ink);display:block;font-size:13.5px}

        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px}
        .card{background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden;transition:.15s}
        .card:hover{box-shadow:0 10px 28px rgba(0,0,0,.09);transform:translateY(-3px)}
        .card .thumb{height:160px;background:#f0f1f4;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-size:34px}
        .card .thumb img{width:100%;height:100%;object-fit:cover}
        .card .body{padding:14px 16px}
        .card .name{font-weight:700;font-size:15px;margin-bottom:6px}
        .card .price{font-weight:800;font-size:17px}
        .card .price small{color:var(--muted);font-weight:600;font-size:12px}
        .card .offer{color:#16a34a;font-size:12px;font-weight:600}

        .empty{text-align:center;color:var(--muted);padding:30px 0}
        .review-card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:16px;margin-bottom:12px}
        .review-card .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
        .review-card .stars{color:#f59e0b;font-size:15px}
        .review-cta{margin-bottom:18px}
        .review-form{background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px;margin-bottom:18px}
        .review-form h4{font-size:15px;margin-bottom:12px}
        .star-pick{display:flex;gap:6px;font-size:30px;line-height:1;user-select:none;cursor:pointer;margin-bottom:12px}
        .star-pick span{color:#d1d5db;transition:.12s}
        .star-pick span.on,.star-pick span.hover{color:#f59e0b;transform:scale(1.08)}
        .review-form textarea{width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;outline:0}
        .review-form textarea:focus{border-color:var(--brand)}
        .btn-review{background:var(--brand);color:#fff;border:0;padding:10px 22px;border-radius:8px;font-weight:700;font-size:14px;cursor:pointer}
        .btn-review:hover{background:var(--brand-dark)}
        footer{background:#16181d;color:#b6bac3;padding:26px 0;font-size:13.5px}
        footer .container{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
        .btn{display:inline-block;padding:9px 18px;border-radius:8px;font-weight:700;font-size:13.5px;transition:.15s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark)}
        .btn-outline{background:transparent;color:#fff;border:1px solid rgba(255,255,255,.4)}
        .btn-outline:hover{background:rgba(255,255,255,.1)}

        @media(max-width:720px){.hero h1{font-size:24px}}
    </style>
</head>
<body>

<?php foreach (['success' => '#ecfdf5|#047857|#a7f3d0', 'error' => '#fef2f2|#b91c1c|#fecaca', 'info' => '#eff6ff|#1d4ed8|#bfdbfe'] as $fk => $fcolors):
    $fmsg = session()->getFlashdata('flash_' . $fk);
    if ($fmsg):
        [$fbg, $ffc, $fbd] = explode('|', $fcolors); ?>
        <div style="max-width:1180px;margin:14px auto 0;padding:0 20px">
            <div style="background:<?= $fbg ?>;color:<?= $ffc ?>;border:1px solid <?= $fbd ?>;border-radius:8px;padding:11px 15px;font-weight:600;font-size:13.5px"><?= esc($fmsg) ?></div>
        </div>
    <?php endif;
endforeach; ?>

<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi" style="height:48px;width:auto"></a>
        <a class="btn btn-outline" style="color:var(--brand);border-color:var(--brand)" href="<?= esc($base) ?>/">← Back to Marketplace</a>
    </div>
</header>

<div class="hero">
    <div class="container">
        <div class="top">
            <?php if (!empty($s['logo'])): ?>
                <div class="logo" style="background:#fff;padding:0;overflow:hidden">
                    <img src="<?= esc(base_url('uploads/' . $s['logo'])) ?>" alt="<?= esc($s['business_name']) ?> logo" style="width:100%;height:100%;object-fit:cover">
                </div>
            <?php else: ?>
                <div class="logo"><?= esc(strtoupper(substr($s['business_name'], 0, 1))) ?></div>
            <?php endif; ?>
            <div>
                <h1><?= esc($s['business_name']) ?></h1>
                <div class="sub">
                    <?= esc($s['city'] ?? 'Ghaziabad') ?><?= $s['state'] ? ', ' . esc($s['state']) : '' ?>
                    <?= $s['owner_name'] ? ' · ' . esc($s['owner_name']) : '' ?>
                </div>
                <div class="badges">
                    <?php if ($s['gst_verified']): ?><span class="ok">✓ GST Verified</span><?php endif; ?>
                    <?php if ($s['is_verified']): ?><span class="ok">✓ Verified Seller</span><?php endif; ?>
                    <?php if ($s['is_premium']): ?><span class="ok">★ Premium</span><?php endif; ?>
                    <span><?= (int)$s['years_in_business'] ? (int)$s['years_in_business'] . '+ yrs' : 'Established' ?></span>
                </div>
                <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap">
                    <?php if (!empty($auth['logged_in']) && $auth['role'] !== 'seller'): ?>
                        <a class="btn btn-primary" href="<?= esc($base) ?>/account/chats/open/<?= (int)$s['id'] ?>">💬 Chat with seller</a>
                    <?php elseif (empty($auth['logged_in'])): ?>
                        <a class="btn btn-primary" href="<?= esc($base) ?>/login">💬 Chat with seller</a>
                    <?php endif; ?>
                    <a class="btn btn-outline" href="<?= esc($base) ?>/">← Back</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="stats">
        <div class="stat"><b><?= (int)$stats['products'] ?></b><span>Products</span></div>
        <div class="stat"><b><?= number_format((float)$s['rating_avg'], 1) ?></b><span>Rating (<?= (int)$s['rating_count'] ?>)</span></div>
        <div class="stat"><b><?= (int)$stats['orders'] ?></b><span>Orders</span></div>
        <div class="stat"><b><?= (int)$stats['leads'] ?></b><span>Buyers Served</span></div>
    </div>
</div>

<section id="about">
    <div class="container">
        <div class="about">
            <h3>About <?= esc($s['business_name']) ?></h3>
            <p><?= nl2br(esc($s['about'] ?? 'Verified steel supplier serving the Loha Mandi Ghaziabad market with quality Iron &amp; Steel products at competitive prices.')) ?></p>
            <div class="info">
                <?php if ($s['gstin']): ?><div><b>GSTIN</b><?= esc($s['gstin']) ?></div><?php endif; ?>
                <?php if ($s['owner_phone']): ?><div><b>Contact</b><?= esc($s['owner_phone']) ?></div><?php endif; ?>
                <?php if ($s['address_line1']): ?><div><b>Address</b><?= esc($s['address_line1']) ?><?= $s['city'] ? ', ' . esc($s['city']) : '' ?></div><?php endif; ?>
                <?php if ($s['website']): ?><div><b>Website</b><a href="<?= esc($s['website']) ?>" target="_blank" style="color:var(--brand)"><?= esc($s['website']) ?></a></div><?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section id="products">
    <div class="container">
        <div class="sec-head">
            <h2>Products</h2>
        </div>
        <?php if (empty($products)): ?>
            <div class="empty">No products listed yet. Check back soon.</div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $p): ?>
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
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="reviews">
    <div class="container">
        <div class="sec-head">
            <h2>Reviews &amp; Ratings</h2>
            <span class="muted" style="font-size:13px"><?= number_format((float)$s['rating_avg'], 1) ?> / 5 (<?= (int)$s['rating_count'] ?>)</span>
        </div>

        <?php if (($reviewState ?? 'guest') === 'ok'): ?>
            <div class="review-cta">
                <button class="btn-review" type="button" onclick="toggleReviewForm()">★ Write a Review</button>
                <form class="review-form" id="reviewForm" method="post"
                      action="<?= esc(base_url('seller/' . $s['slug'] . '/review')) ?>" style="display:none;margin-top:14px">
                    <h4>Rate your experience with <?= esc($s['business_name']) ?></h4>
                    <input type="hidden" name="rating" id="ratingValue" value="">
                    <div class="star-pick" id="starPick" title="Click to rate">
                        <span data-v="1">★</span><span data-v="2">★</span><span data-v="3">★</span><span data-v="4">★</span><span data-v="5">★</span>
                    </div>
                    <div class="muted" id="starHint" style="font-size:12.5px;margin:-6px 0 10px">Select 1 to 5 stars</div>
                    <textarea name="review" rows="4" placeholder="Share your experience with quality, pricing, delivery and communication… (optional)"></textarea>
                    <div style="margin-top:12px">
                        <button class="btn-review" type="submit">Submit Review</button>
                    </div>
                </form>
            </div>
        <?php elseif (($reviewState ?? 'guest') === 'guest'): ?>
            <div class="review-cta">
                <a class="btn-review" style="display:inline-block;text-decoration:none" href="<?= esc($base) ?>/login">★ Sign in to write a review</a>
            </div>
        <?php elseif (($reviewState ?? 'guest') === 'done'): ?>
            <div class="review-cta"><span class="muted" style="font-size:13.5px">✓ You have already reviewed this seller. Thank you!</span></div>
        <?php endif; ?>

        <?php if (empty($reviews)): ?>
            <div class="empty">No reviews yet. Be the first to share your experience.</div>
        <?php else: ?>
            <?php foreach ($reviews as $rv): ?>
                <div class="review-card">
                    <div class="head">
                        <b><?= esc($rv['buyer_name'] ?? 'Buyer') ?></b>
                        <span class="stars"><?= str_repeat('★', (int)$rv['rating']) ?><?= str_repeat('☆', 5 - (int)$rv['rating']) ?></span>
                    </div>
                    <div class="muted" style="font-size:12px;margin-bottom:8px"><?= esc(date('d M Y', strtotime($rv['created_at']))) ?></div>
                    <p><?= nl2br(esc($rv['review'] ?? '')) ?></p>
                    <?php if ($rv['seller_reply']): ?>
                        <div style="background:#f3f4f6;border-radius:8px;padding:10px;margin-top:10px;font-size:13.5px">
                            <b style="font-size:12px;color:var(--brand)"><?= esc($s['business_name']) ?> replied</b>
                            <p style="margin-top:4px"><?= nl2br(esc($rv['seller_reply'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer>
    <div class="container">
        <div>
            <b style="color:#fff">LOHA Mandi</b>
            The Digital Marketplace of Loha Mandi, Ghaziabad.
        </div>
        <div>
            <b style="color:#fff">Quick Links</b>
            <a href="<?= esc($base) ?>/catalog" style="display:block;margin-bottom:4px">Buy on Loha Mandi</a>
            <a href="<?= esc($base) ?>/suppliers" style="display:block;margin-bottom:4px">Sell on Loha Mandi</a>
            <a href="<?= esc($base) ?>/post-rfq" style="display:block;margin-bottom:4px">Post RFQ</a>
            <a href="<?= esc($base) ?>/register" style="display:block;margin-bottom:4px">Join Loha Mandi</a>
            <a href="<?= esc($base) ?>/privacy-policy" style="display:block;margin-bottom:4px">Privacy Policy</a>
            <a href="<?= esc($base) ?>/terms-of-service" style="display:block;margin-bottom:4px">Terms of Service</a>
        </div>
        <div>
            <b style="color:#fff">Contact</b>
            Loha Mandi, Ghaziabad, UP<br>
            <a href="mailto:info@lohamandigzb.online" style="color:var(--brand)">info@lohamandigzb.online</a>
        </div>
        <div>
            <b style="color:#fff">Developed &amp; Maintained by</b>
            VDS INFOTECH
        </div>
    </div>
    <div class="container" style="border-top:1px solid rgba(255,255,255,.08);margin-top:20px;padding-top:16px;justify-content:center;font-size:12px">
        &copy; 2026 LOHA Mandi. All rights reserved.
    </div>
</footer>
<script>
function toggleReviewForm(){
    var f = document.getElementById('reviewForm');
    if (!f) return;
    f.style.display = (f.style.display === 'none') ? 'block' : 'none';
    if (f.style.display === 'block') { f.scrollIntoView({behavior:'smooth', block:'center'}); }
}
(function(){
    var pick = document.getElementById('starPick');
    if (!pick) return;
    var stars = pick.querySelectorAll('span');
    var input = document.getElementById('ratingValue');
    var hint  = document.getElementById('starHint');
    var labels = ['', '1 — Poor', '2 — Fair', '3 — Good', '4 — Very Good', '5 — Excellent'];
    var current = 0;

    function paint(n){
        stars.forEach(function(s){
            s.classList.toggle('on', parseInt(s.dataset.v,10) <= n);
        });
    }
    stars.forEach(function(s){
        s.addEventListener('mouseenter', function(){ paint(parseInt(s.dataset.v,10)); });
        s.addEventListener('click', function(){
            current = parseInt(s.dataset.v,10);
            input.value = current;
            paint(current);
            hint.textContent = labels[current];
        });
    });
    pick.addEventListener('mouseleave', function(){ paint(current); });

    var form = document.getElementById('reviewForm');
    if (form) {
        form.addEventListener('submit', function(e){
            if (!input.value) {
                e.preventDefault();
                hint.textContent = 'Please select a star rating first.';
                hint.style.color = '#b91c1c';
            }
        });
    }
})();
</script>
</body>
</html>
