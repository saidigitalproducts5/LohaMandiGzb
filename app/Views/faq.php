<?php
/**
 * Why LOHA Mandi — FAQ page with English/Hindi toggle
 */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Why LOHA Mandi — The Digital Marketplace of Loha Mandi, Ghaziabad</title>
    <style>
        :root{--brand:#e02735;--brand-dark:#b81f2b;--ink:#1a1d23;--muted:#6b7280;--bg:#f6f7f9;--card:#fff;--line:#e5e7eb;--teal:#0f766e;--teal-light:#14b8a6;--purple:#4338ca;--amber:#b45309}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;color:var(--ink);background:var(--bg);line-height:1.6}
        a{color:inherit;text-decoration:none}
        .container{max-width:960px;margin:0 auto;padding:0 20px}

        /* NAV */
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:60px;gap:20px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px}
        .brand img{height:44px;width:auto}
        .nav-links{display:flex;gap:22px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:10px 20px;border-radius:8px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.15s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark);transform:translateY(-1px);box-shadow:0 4px 12px rgba(224,39,53,.3)}
        .btn-white{background:#fff;color:var(--brand);font-weight:700}
        .btn-white:hover{background:#f0f0f0}
        .hamburger{display:none;background:none;border:none;font-size:26px;cursor:pointer;color:var(--ink)}
        .lang-toggle{display:flex;border:2px solid var(--line);border-radius:8px;overflow:hidden;margin-left:12px}
        .lang-toggle button{padding:6px 14px;font-weight:700;font-size:13px;border:0;cursor:pointer;background:#fff;color:var(--muted);transition:.15s}
        .lang-toggle button.active{background:var(--brand);color:#fff}
        .nav-right{display:flex;align-items:center;gap:0}

        /* HERO */
        .hero{background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#334155 100%);color:#fff;padding:70px 0 80px;position:relative;overflow:hidden;text-align:center}
        .hero::before{content:"";position:absolute;top:-60px;right:-60px;width:300px;height:300px;border-radius:50%;background:rgba(224,39,53,.15);filter:blur(60px)}
        .hero::after{content:"";position:absolute;bottom:-40px;left:-40px;width:200px;height:200px;border-radius:50%;background:rgba(20,184,166,.12);filter:blur(50px)}
        .hero h1{font-size:40px;line-height:1.2;margin-bottom:14px;position:relative;z-index:2}
        .hero h1 span{color:#ff6b6b}
        .hero p{font-size:18px;color:#94a3b8;max-width:620px;margin:0 auto 28px;position:relative;z-index:2}
        .hero-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative;z-index:2}

        /* STATS STRIP */
        .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin:-40px auto 0;position:relative;z-index:5;max-width:800px}
        .stat-item{background:#fff;border-radius:12px;padding:20px;text-align:center;box-shadow:0 8px 30px rgba(0,0,0,.08);margin:0 8px}
        .stat-item .icon{font-size:28px;margin-bottom:6px}
        .stat-item b{font-size:24px;display:block;color:var(--ink)}
        .stat-item span{color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}

        /* SECTION HEADERS */
        .sec-header{text-align:center;margin-bottom:36px}
        .sec-header .tag{display:inline-block;background:rgba(224,39,53,.08);color:var(--brand);font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px}
        .sec-header h2{font-size:30px;font-weight:800;margin-bottom:6px}
        .sec-header p{color:var(--muted);font-size:15px;max-width:500px;margin:0 auto}

        .content-section{padding:56px 0}

        /* ACCORDION */
        .accordion{max-width:800px;margin:0 auto}
        .acc-item{background:#fff;border:1px solid var(--line);border-radius:14px;margin-bottom:12px;overflow:hidden;transition:.2s}
        .acc-item:hover{box-shadow:0 4px 16px rgba(0,0,0,.06)}
        .acc-item.open{box-shadow:0 6px 20px rgba(0,0,0,.08);border-color:var(--brand)}
        .acc-head{display:flex;align-items:center;gap:14px;padding:18px 22px;cursor:pointer;user-select:none;transition:.15s}
        .acc-head:hover{background:#fafbfc}
        .acc-num{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;flex-shrink:0;color:#fff}
        .acc-num.n1{background:linear-gradient(135deg,var(--brand),#ff6b6b)}
        .acc-num.n2{background:linear-gradient(135deg,var(--teal),var(--teal-light))}
        .acc-num.n3{background:linear-gradient(135deg,var(--purple),#818cf8)}
        .acc-num.n4{background:linear-gradient(135deg,var(--amber),#f59e0b)}
        .acc-num.n5{background:linear-gradient(135deg,#0369a1,#38bdf8)}
        .acc-num.n6{background:linear-gradient(135deg,#7c2d12,#ea580c)}
        .acc-num.n7{background:linear-gradient(135deg,#166534,#22c55e)}
        .acc-num.n8{background:linear-gradient(135deg,#7e22ce,#a855f7)}
        .acc-num.n9{background:linear-gradient(135deg,#be123c,#fb7185)}
        .acc-num.n10{background:linear-gradient(135deg,#0e7490,#22d3ee)}
        .acc-title{flex:1;font-size:16px;font-weight:700}
        .acc-icon{font-size:20px;color:var(--muted);transition:.25s;flex-shrink:0}
        .acc-item.open .acc-icon{transform:rotate(180deg);color:var(--brand)}
        .acc-body{max-height:0;overflow:hidden;transition:max-height .35s ease,padding .25s ease;padding:0 22px}
        .acc-item.open .acc-body{max-height:800px;padding:0 22px 20px}
        .acc-body p{color:#374151;font-size:14.5px;margin-bottom:8px}
        .acc-body ul{margin:6px 0 8px 20px;font-size:14px;color:#374151}
        .acc-body ul li{margin-bottom:4px}
        .acc-body .flow{background:linear-gradient(90deg,var(--brand),var(--brand-dark));color:#fff;display:inline-block;padding:8px 18px;border-radius:8px;font-weight:700;font-size:13px;margin:8px 0;letter-spacing:.3px}
        .acc-body .formula{background:#f0fdf4;border-left:4px solid var(--teal);padding:10px 16px;border-radius:0 8px 8px 0;font-weight:700;font-size:14px;color:var(--teal);margin:8px 0}

        /* FEATURE GRID */
        .feature-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin:16px 0}
        .feature-card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:16px;text-align:center;transition:.2s}
        .feature-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.07)}
        .feature-card .fc-icon{font-size:32px;margin-bottom:8px}
        .feature-card h4{font-size:14px;margin-bottom:2px}
        .feature-card p{font-size:12px;color:var(--muted)}

        /* COMPARISON */
        .compare-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
        table.compare{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06)}
        table.compare thead th{background:var(--ink);color:#fff;padding:14px 16px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;text-align:left}
        table.compare tbody td{padding:13px 16px;border-bottom:1px solid var(--line);font-size:14px}
        table.compare tbody tr:last-child td{border-bottom:0}
        table.compare tbody tr:hover{background:#fafbfc}
        table.compare .yes{color:var(--teal);font-weight:700}
        table.compare .no{color:var(--muted)}
        table.compare .gen{color:#6b7280}
        table.compare .us{color:var(--brand);font-weight:700}

        /* VISION */
        .vision-section{background:linear-gradient(135deg,#0f172a,#1e293b);padding:60px 0;text-align:center;color:#fff;position:relative;overflow:hidden}
        .vision-section::before{content:"";position:absolute;top:-50%;left:-20%;width:400px;height:400px;border-radius:50%;background:rgba(224,39,53,.1);filter:blur(80px)}
        .vision-section blockquote{font-size:22px;font-weight:700;font-style:italic;max-width:640px;margin:0 auto 16px;position:relative;z-index:2;line-height:1.4}
        .vision-section p{color:#94a3b8;font-size:16px;max-width:560px;margin:0 auto;position:relative;z-index:2}
        .vision-section .brand-big{display:inline-block;margin-top:20px;font-size:22px;font-weight:800;color:#ff6b6b;position:relative;z-index:2}

        /* CTA */
        .cta-section{text-align:center;padding:60px 0;background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;position:relative;overflow:hidden}
        .cta-section::before{content:"";position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.08)}
        .cta-section h2{font-size:30px;margin-bottom:10px;position:relative;z-index:2}
        .cta-section p{font-size:16px;opacity:.9;max-width:500px;margin:0 auto 24px;position:relative;z-index:2}
        .cta-section .btn{position:relative;z-index:2}

        /* FOOTER */
        footer{background:#0f172a;color:#94a3b8;padding:34px 0;font-size:14px}
        footer .container{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}
        footer a{color:#94a3b8;transition:.15s}
        footer a:hover{color:var(--brand)}

        /* MOBILE NAV */
        .mobile-nav{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:30}
        .mobile-nav.open{display:block}
        .mobile-nav .drawer{position:fixed;top:0;left:0;width:280px;height:100%;background:#fff;z-index:31;padding:24px 20px;overflow-y:auto;box-shadow:4px 0 24px rgba(0,0,0,.2)}
        .mobile-nav .drawer a{display:block;padding:13px 0;font-weight:600;font-size:15px;border-bottom:1px solid var(--line)}
        .mobile-nav .drawer a:hover{color:var(--brand)}
        .mobile-nav .close-btn{background:none;border:none;font-size:28px;cursor:pointer;position:absolute;top:16px;right:16px;color:var(--muted)}

        [data-lang="hi"]{display:none}
        body.hi [data-lang="en"]{display:none}
        body.hi [data-lang="hi"]{display:initial}
        body.hi td[data-lang="hi"],body.hi th[data-lang="hi"]{display:table-cell}
        body.hi .hi-block{display:block}
        body.hi .hi-flex{display:flex}

        @media(max-width:768px){
            .nav-links{display:none}
            .hamburger{display:block}
            .hero{padding:44px 0 50px}
            .hero h1{font-size:26px}
            .hero p{font-size:15px}
            .stats-strip{grid-template-columns:repeat(2,1fr);margin-top:-30px;gap:8px}
            .stat-item{margin:0 4px;padding:14px 10px}
            .stat-item b{font-size:20px}
            .sec-header h2{font-size:22px}
            .acc-head{padding:14px 16px;gap:10px}
            .acc-title{font-size:14px}
            .feature-grid{grid-template-columns:1fr 1fr}
            .vision-section blockquote{font-size:20px}
            .cta-section h2{font-size:24px}
            footer .container{flex-direction:column;text-align:center}
        }
        @media(max-width:480px){
            .stats-strip{grid-template-columns:1fr 1fr}
            .feature-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<header>
    <div class="container nav">
        <a class="brand" href="<?= esc($base) ?>/"><img src="<?= esc($base) ?>/images/logo-LohaMandi2.png" alt="LOHA Mandi"></a>
        <button class="hamburger" onclick="document.getElementById('mobileNav').classList.add('open')">&#9776;</button>
        <nav class="nav-links">
            <a href="<?= esc($base) ?>/catalog">Buy on Loha Mandi</a>
            <a href="<?= esc($base) ?>/suppliers">Sell on Loha Mandi</a>
            <a href="<?= esc($base) ?>/faq">Why LOHA Mandi</a>
            <a href="<?= esc($base) ?>/post-rfq">Post RFQ</a>
        </nav>
        <div class="nav-right">
            <div class="lang-toggle">
                <button id="btnEn" class="active" onclick="setLang('en')">EN</button>
                <button id="btnHi" onclick="setLang('hi')">HI</button>
            </div>
            <a class="btn btn-primary" href="<?= esc($base) ?>/register" style="margin-left:10px">
                <span data-lang="en">Join Loha Mandi</span>
                <span data-lang="hi">लोहा मार्ट से जुड़ें</span>
            </a>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobileNav" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="drawer">
        <button class="close-btn" onclick="document.getElementById('mobileNav').classList.remove('open')">&times;</button>
        <a href="<?= esc($base) ?>/"><span data-lang="en">Home</span><span data-lang="hi">होम</span></a>
        <a href="<?= esc($base) ?>/catalog"><span data-lang="en">Buy on Loha Mandi</span><span data-lang="hi">लोहा मार्ट से खरीदें</span></a>
        <a href="<?= esc($base) ?>/suppliers"><span data-lang="en">Sell on Loha Mandi</span><span data-lang="hi">लोहा मार्ट पर बेचें</span></a>
        <a href="<?= esc($base) ?>/faq"><span data-lang="en">Why LOHA Mandi</span><span data-lang="hi">लोहा मार्ट क्यों</span></a>
        <a href="<?= esc($base) ?>/post-rfq"><span data-lang="en">Post RFQ</span><span data-lang="hi">RFQ भेजें</span></a>
        <a href="<?= esc($base) ?>/transport"><span data-lang="en">Transport</span><span data-lang="hi">ट्रांसपोर्ट</span></a>
        <a href="<?= esc($base) ?>/login"><span data-lang="en">Login</span><span data-lang="hi">लॉगिन</span></a>
        <a href="<?= esc($base) ?>/register"><span data-lang="en">Join Loha Mandi</span><span data-lang="hi">लोहा मार्ट से जुड़ें</span></a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <div class="container">
        <h1>
            <span data-lang="en">Why <span>LOHA Mandi?</span></span>
            <span data-lang="hi">लोहा मार्ट <span>क्यों?</span></span>
        </h1>
        <p>
                <span data-lang="en">IndiaMart is for Everyone. LOHA Mandi is ONLY for Loha Mandi Ghaziabad. Why compete with thousands of sellers across India when your real business opportunity is right here?</span>
                <span data-lang="hi">IndiaMart सबके लिए है। LOHA Mandi केवल लोहा मंडी घाज़ियाबाद के लिए है। पूरे भारत के हज़ारों विक्रेताओं से क्यों प्रतिस्पर्धा करें जब आपका असली व्यापारिक अवसर यहीं है?</span>
        </p>
        <div class="hero-btns">
            <a class="btn btn-primary" href="<?= esc($base) ?>/register">
                <span data-lang="en">Register Now</span>
                <span data-lang="hi">रजिस्टर करें</span>
            </a>
            <a class="btn btn-white" href="<?= esc($base) ?>/catalog">
                <span data-lang="en">Browse Products</span>
                <span data-lang="hi">उत्पाद देखें</span>
            </a>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="container">
    <div class="stats-strip">
        <div class="stat-item">
            <div class="icon">🏭</div><b>100%</b>
            <span data-lang="en">Loha Mandi Focus</span>
            <span data-lang="hi">लोहा मंडी केंद्रित</span>
        </div>
        <div class="stat-item">
            <div class="icon">🤝</div><b>Direct</b>
            <span data-lang="en">Buyer-Seller</span>
            <span data-lang="hi">सीधा खरीदार-विक्रेता</span>
        </div>
        <div class="stat-item">
            <div class="icon">🚚</div><b>Built-in</b>
            <span data-lang="en">Transport</span>
            <span data-lang="hi">ट्रांसपोर्ट</span>
        </div>
        <div class="stat-item">
            <div class="icon">📱</div><b>24&times;7</b>
            <span data-lang="en">Digital Shop</span>
            <span data-lang="hi">डिजिटल दुकान</span>
        </div>
    </div>
</div>

<!-- 10 REASONS — ACCORDION -->
<div class="content-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">
                <span data-lang="en">10 Reasons</span>
                <span data-lang="hi">10 कारण</span>
            </div>
            <h2>
                <span data-lang="en">Why Sellers Choose LOHA Mandi</span>
                <span data-lang="hi">विक्रेता लोहा मंडी क्यों चुनते हैं</span>
            </h2>
            <p>
                <span data-lang="en">Click any reason to learn more about how we're different</span>
                <span data-lang="hi">हम कैसे अलग हैं जानने के लिए किसी भी कारण पर क्लिक करें</span>
            </p>
        </div>

        <div class="accordion">

            <!-- 01 -->
            <div class="acc-item open">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n1">01</span>
                    <span class="acc-title">
                        <span data-lang="en">A Marketplace Dedicated to Loha Mandi</span>
                        <span data-lang="hi">लोहा मंडी को समर्पित बाज़ार</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                <span data-lang="en">On a general marketplace, your business competes with sellers from Delhi, Mumbai, Kolkata and hundreds of other cities. On LOHA Mandi, the marketplace is built around <b>Loha Mandi Ghaziabad</b>.</span>
                <span data-lang="hi">सामान्य बाज़ार में आपका व्यापार दिल्ली, मुंबई, कोलकाता और सैकड़ों अन्य शहरों के विक्रेताओं से प्रतिस्पर्धा करता है। LOHA Mandi पर बाज़ार <b>लोहा मंडी घाज़ियाबाद</b> के इर्द-गिर्द बना है।</span>
                    </p>
                    <div class="feature-grid">
                        <div class="feature-card"><div class="fc-icon">🔩</div><h4>TMT Bars</h4><p data-lang="hi">TMT सरिया</p></div>
                        <div class="feature-card"><div class="fc-icon">📋</div><h4>MS Plates</h4><p data-lang="hi">MS प्लेट</p></div>
                        <div class="feature-card"><div class="fc-icon">📜</div><h4>HR/CR Sheets</h4><p data-lang="hi">HR/CR शीट</p></div>
                        <div class="feature-card"><div class="fc-icon">📐</div><h4>Angles &amp; Channels</h4><p data-lang="hi">एंगल और चैनल</p></div>
                        <div class="feature-card"><div class="fc-icon">🏗</div><h4>Beams</h4><p data-lang="hi">बीम</p></div>
                        <div class="feature-card"><div class="fc-icon">🔧</div><h4>Pipes &amp; Coils</h4><p data-lang="hi">पाइप और कॉइल</p></div>
                        <div class="feature-card"><div class="fc-icon">⚙️</div><h4>Structural Steel</h4><p data-lang="hi">स्ट्रक्चरल स्टील</p></div>
                        <div class="feature-card"><div class="fc-icon">🛠</div><h4>Hardware &amp; Scrap</h4><p data-lang="hi">हार्डवेयर और स्क्रैप</p></div>
                    </div>
                </div>
            </div>

            <!-- 02 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n2">02</span>
                    <span class="acc-title">
                        <span data-lang="en">Direct Business — Directly to Your Account</span>
                        <span data-lang="hi">सीधा व्यापार — सीधा आपके खाते में</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">A buyer submits a requirement. The requirement is routed directly to relevant sellers.</span>
                        <span data-lang="hi">खरीदार अपनी ज़रूरत भेजता है। ज़रूरत सीधे संबंधित विक्रेताओं तक पहुँचती है।</span>
                    </p>
                    <div class="flow">
                        <span data-lang="en">Buyer → RFQ → Seller → Quotation → Deal</span>
                        <span data-lang="hi">खरीदार → RFQ → विक्रेता → कोटेशन → डील</span>
                    </div>
                    <p>
                        <span data-lang="en">No need to search through hundreds of unrelated suppliers. <b>Your leads should reach YOU.</b></span>
                        <span data-lang="hi">सैकड़ों असंबंधित आपूर्तिकर्ताओं में खोजने की ज़रूरत नहीं। <b>आपके लीड सिर्फ आप तक पहुँचने चाहिए।</b></span>
                    </p>
                </div>
            </div>

            <!-- 03 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n3">03</span>
                    <span class="acc-title">
                        <span data-lang="en">Local Seller + Local Stock = Faster Business</span>
                        <span data-lang="hi">स्थानीय विक्रेता + स्थानीय स्टॉक = तेज़ व्यापार</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">A buyer in Ghaziabad, Noida, Delhi, Sahibabad, Greater Noida or nearby areas often needs material quickly. Why search across the entire country?</span>
                        <span data-lang="hi">घाज़ियाबाद, नोएडा, दिल्ली, साहिबाबाद, ग्रेटर नोएडा या आस-पास के इलाकों में खरीदार को अक्सर जल्दी सामान चाहिए। पूरे देश में क्यों खोजें?</span>
                    </p>
                    <div class="formula">
                        <span data-lang="en">Local Supplier + Local Stock + Local Knowledge + Local Transport = Faster Fulfilment</span>
                        <span data-lang="hi">स्थानीय आपूर्तिकर्ता + स्थानीय स्टॉक + स्थानीय जानकारी + स्थानीय ट्रांसपोर्ट = तेज़ डिलीवरी</span>
                    </div>
                </div>
            </div>

            <!-- 04 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n4">04</span>
                    <span class="acc-title">
                        <span data-lang="en">Transportation Management — A Major Advantage</span>
                        <span data-lang="hi">ट्रांसपोर्ट प्रबंधन — एक बड़ा फ़ायदा</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">Steel business is not only about finding the right price. It is also about getting material to site.</span>
                        <span data-lang="hi">स्टील का व्यापार सिर्फ सही दाम खोजने के बारे में नहीं है। यह सामान को साइट तक पहुँचाने के बारे में भी है।</span>
                    </p>
                    <div class="flow">
                        <span data-lang="en">BUYER → SELLER → TRANSPORTER → DELIVERY</span>
                        <span data-lang="hi">खरीदार → विक्रेता → ट्रांसपोर्टर → डिलीवरी</span>
                    </div>
                    <ul>
                        <li>
                            <span data-lang="en">Transporter registration &amp; vehicle availability</span>
                            <span data-lang="hi">ट्रांसपोर्टर रजिस्ट्रेशन और गाड़ी उपलब्धता</span>
                        </li>
                        <li>
                            <span data-lang="en">Vehicle type selection &amp; freight calculation</span>
                            <span data-lang="hi">गाड़ी का प्रकार चुनें और फ़्रेट की गणना</span>
                        </li>
                        <li>
                            <span data-lang="en">Loading &amp; delivery location management</span>
                            <span data-lang="hi">लोडिंग और डिलीवरी लोकेशन प्रबंधन</span>
                        </li>
                        <li>
                            <span data-lang="en">Dispatch status &amp; delivery tracking</span>
                            <span data-lang="hi">डिस्पैच स्टेटस और डिलीवरी ट्रैकिंग</span>
                        </li>
                        <li>
                            <span data-lang="en">Delivery confirmation</span>
                            <span data-lang="hi">डिलीवरी की पुष्टि</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 05 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n5">05</span>
                    <span class="acc-title">
                        <span data-lang="en">Build a Strong Loha Mandi Business Network</span>
                        <span data-lang="hi">लोहा मंडी का मज़बूत व्यापारिक नेटवर्क बनाएं</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">LOHA Mandi is the Digital Business Network of Loha Mandi Ghaziabad, bringing together:</span>
                        <span data-lang="hi">LOHA Mandi लोहा मंडी घाज़ियाबाद का डिजिटल व्यापारिक नेटवर्क है, जो इन्हें जोड़ता है:</span>
                    </p>
                    <div class="feature-grid" style="grid-template-columns:repeat(2,1fr)">
                        <div class="feature-card"><div class="fc-icon">🏪</div><h4>
                            <span data-lang="en">Sellers</span><span data-lang="hi">विक्रेता</span>
                        </h4><p>
                            <span data-lang="en">Traders, Stockists, Distributors, Manufacturers</span>
                            <span data-lang="hi">व्यापारी, स्टॉकिस्ट, वितरक, निर्माता</span>
                        </p></div>
                        <div class="feature-card"><div class="fc-icon">👷</div><h4>
                            <span data-lang="en">Buyers</span><span data-lang="hi">खरीदार</span>
                        </h4><p>
                            <span data-lang="en">Builders, Contractors, Fabricators, Dealers</span>
                            <span data-lang="hi">बिल्डर, ठेकेदार, फैब्रिकेटर, डीलर</span>
                        </p></div>
                        <div class="feature-card"><div class="fc-icon">🚛</div><h4>
                            <span data-lang="en">Transporters</span><span data-lang="hi">ट्रांसपोर्टर</span>
                        </h4><p>
                            <span data-lang="en">Truck Owners, Fleet Operators</span>
                            <span data-lang="hi">ट्रक मालिक, फ्लीट ऑपरेटर</span>
                        </p></div>
                        <div class="feature-card"><div class="fc-icon">🔧</div><h4>
                            <span data-lang="en">Services</span><span data-lang="hi">सेवाएं</span>
                        </h4><p>
                            <span data-lang="en">Cutting, Bending, Fabrication, Loading</span>
                            <span data-lang="hi">कटाई, बेंडिंग, फैब्रिकेशन, लोडिंग</span>
                        </p></div>
                    </div>
                </div>
            </div>

            <!-- 06 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n6">06</span>
                    <span class="acc-title">
                        <span data-lang="en">Your Digital Shop is Open 24&times;7</span>
                        <span data-lang="hi">आपकी डिजिटल दुकान 24&times;7 खुली है</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">Your physical shop has fixed working hours. Your online business profile does not. Customers can see your Company, Products, Brands, Stock, Location, Contact Details and Business Information <b>anytime</b>.</span>
                        <span data-lang="hi">आपकी भौतिक दुकान का समय निर्धारित है। आपकी ऑनलाइन प्रोफ़ाइल का नहीं। ग्राहक आपकी कंपनी, उत्पाद, ब्रांड, स्टॉक, लोकेशन, संपर्क विवरण और व्यापारिक जानकारी <b>कभी भी</b> देख सकते हैं।</span>
                    </p>
                </div>
            </div>

            <!-- 07 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n7">07</span>
                    <span class="acc-title">
                        <span data-lang="en">Less Competition from Unrelated Sellers</span>
                        <span data-lang="hi">असंबंधित विक्रेताओं से कम प्रतिस्पर्धा</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">On a general marketplace, your product appears beside sellers from completely different markets.</span>
                        <span data-lang="hi">सामान्य बाज़ार में आपका उत्पाद पूरी तरह अलग बाज़ार के विक्रेताओं के बगल में दिखता है।</span>
                    </p>
                    <div class="formula">
                        <span data-lang="en">Right Buyer + Right Seller + Right Location = Relevant Business Enquiries</span>
                        <span data-lang="hi">सही खरीदार + सही विक्रेता + सही लोकेशन = प्रासंगिक व्यापारिक पूछताछ</span>
                    </div>
                </div>
            </div>

            <!-- 08 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n8">08</span>
                    <span class="acc-title">
                        <span data-lang="en">Better Local Buyer-Seller Connection</span>
                        <span data-lang="hi">बेहतर स्थानीय खरीदार-विक्रेता संबंध</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">A buyer doesn't always want the cheapest seller 1,000 km away. Sometimes the buyer wants:</span>
                        <span data-lang="hi">खरीदार को हमेशा 1,000 किमी दूर का सबसे सस्ता विक्रेता नहीं चाहिए। कभी-कभी खरीदार को चाहिए:</span>
                    </p>
                    <div class="feature-grid" style="grid-template-columns:repeat(3,1fr)">
                        <div class="feature-card"><div class="fc-icon">⚡</div><h4>
                            <span data-lang="en">Fast Delivery</span><span data-lang="hi">तेज़ डिलीवरी</span>
                        </h4></div>
                        <div class="feature-card"><div class="fc-icon">📦</div><h4>
                            <span data-lang="en">Available Stock</span><span data-lang="hi">उपलब्ध स्टॉक</span>
                        </h4></div>
                        <div class="feature-card"><div class="fc-icon">🤝</div><h4>
                            <span data-lang="en">Trusted Supplier</span><span data-lang="hi">विश्वसनीय आपूर्तिकर्ता</span>
                        </h4></div>
                        <div class="feature-card"><div class="fc-icon">👁</div><h4>
                            <span data-lang="en">Easy Inspection</span><span data-lang="hi">आसान निरीक्षण</span>
                        </h4></div>
                        <div class="feature-card"><div class="fc-icon">💰</div><h4>
                            <span data-lang="en">Quick Quote</span><span data-lang="hi">तुरंत कोटेशन</span>
                        </h4></div>
                        <div class="feature-card"><div class="fc-icon">🚚</div><h4>
                            <span data-lang="en">Lower Transport</span><span data-lang="hi">कम ट्रांसपोर्ट खर्च</span>
                        </h4></div>
                    </div>
                </div>
            </div>

            <!-- 09 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n9">09</span>
                    <span class="acc-title">
                        <span data-lang="en">Your Business Data, Your Account</span>
                        <span data-lang="hi">आपका व्यापारिक डेटा, आपका खाता</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">Each seller gets their own digital business presence. Your enquiries, quotations and business activities are managed through your seller account — designed around your local business ecosystem.</span>
                        <span data-lang="hi">हर विक्रेता को अपनी डिजिटल व्यापारिक उपस्थिति मिलती है। आपकी पूछताछ, कोटेशन और व्यापारिक गतिविधियां आपके विक्रेता खाते के माध्यम से प्रबंधित होती हैं — आपके स्थानीय व्यापारिक पारिस्थितिकी तंत्र के अनुरूप डिज़ाइन की गई।</span>
                    </p>
                </div>
            </div>

            <!-- 10 -->
            <div class="acc-item">
                <div class="acc-head" onclick="toggleAcc(this)">
                    <span class="acc-num n10">10</span>
                    <span class="acc-title">
                        <span data-lang="en">Become an Early Digital Seller</span>
                        <span data-lang="hi">पहले डिजिटल विक्रेता बनें</span>
                    </span>
                    <span class="acc-icon">▾</span>
                </div>
                <div class="acc-body">
                    <p>
                        <span data-lang="en">The digital transformation of Loha Mandi is only beginning. Sellers who establish their online presence early can build stronger digital visibility as the marketplace grows.</span>
                        <span data-lang="hi">लोहा मंडी का डिजिटल रूपांतरण अभी शुरू ही हुआ है। जो विक्रेता जल्दी अपनी ऑनलाइन उपस्थिति बनाते हैं, बाज़ार बढ़ने के साथ उनकी डिजिटल दृश्यता मज़बूत होती है।</span>
                    </p>
                    <p style="font-weight:700;color:var(--brand);font-size:15px;margin-top:10px">
                        <span data-lang="en">Don't wait for buyers to find you. Make it easy for buyers to find YOU.</span>
                        <span data-lang="hi">खरीदारों का आपको खोजने का इंतज़ार न करें। खरीदारों के लिए आपको ढूंढना आसान बनाएं।</span>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- COMPARISON TABLE -->
<div class="content-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">
                <span data-lang="en">Comparison</span>
                <span data-lang="hi">तुलना</span>
            </div>
            <h2>
                <span data-lang="en">IndiaMart vs LOHA Mandi</span>
                <span data-lang="hi">IndiaMart बनाम लोहा मार्ट</span>
            </h2>
            <p>
                <span data-lang="en">See how a dedicated marketplace beats a general one</span>
                <span data-lang="hi">देखें कैसे एक समर्पित बाज़ार सामान्य बाज़ार से बेहतर है</span>
            </p>
        </div>
        <div class="compare-wrap">
            <table class="compare">
                <thead>
                    <tr>
                        <th><span data-lang="en">Feature</span><span data-lang="hi">विशेषता</span></th>
                        <th><span data-lang="en">General Marketplace</span><span data-lang="hi">सामान्य बाज़ार</span></th>
                        <th style="background:var(--brand)">LOHA Mandi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><span data-lang="en">Market Focus</span><span data-lang="hi">बाज़ार का फोकस</span></td><td class="gen"><span data-lang="en">Pan-India / Multiple categories</span><span data-lang="hi">पूरे भारत / कई श्रेणियां</span></td><td class="us"><span data-lang="en">Loha Mandi Ghaziabad</span><span data-lang="hi">लोहा मंडी घाज़ियाबाद</span></td></tr>
                    <tr><td><span data-lang="en">Seller Competition</span><span data-lang="hi">विक्रेता प्रतिस्पर्धा</span></td><td class="gen"><span data-lang="en">Sellers from many locations</span><span data-lang="hi">कई जगहों के विक्रेता</span></td><td class="us"><span data-lang="en">Focused local seller ecosystem</span><span data-lang="hi">केंद्रित स्थानीय विक्रेता प्रणाली</span></td></tr>
                    <tr><td><span data-lang="en">Buyer Focus</span><span data-lang="hi">खरीदार फोकस</span></td><td class="gen"><span data-lang="en">Broad</span><span data-lang="hi">व्यापक</span></td><td class="us"><span data-lang="en">Steel &amp; Iron buyers</span><span data-lang="hi">स्टील और लोहा खरीदार</span></td></tr>
                    <tr><td><span data-lang="en">Transportation</span><span data-lang="hi">ट्रांसपोर्ट</span></td><td class="gen"><span data-lang="en">Usually separate</span><span data-lang="hi">आमतौर पर अलग</span></td><td class="us"><span data-lang="en">Integrated into marketplace</span><span data-lang="hi">बाज़ार में एकीकृत</span></td></tr>
                    <tr><td><span data-lang="en">Buyer → Seller</span><span data-lang="hi">खरीदार → विक्रेता</span></td><td class="gen"><span data-lang="en">General enquiry model</span><span data-lang="hi">सामान्य पूछताछ मॉडल</span></td><td class="us"><span data-lang="en">Direct seller-focused RFQ</span><span data-lang="hi">सीधा विक्रेता केंद्रित RFQ</span></td></tr>
                    <tr><td><span data-lang="en">Loha Mandi Identity</span><span data-lang="hi">लोहा मंडी पहचान</span></td><td class="no">No</td><td class="yes">YES</td></tr>
                    <tr><td><span data-lang="en">Local Seller Community</span><span data-lang="hi">स्थानीय विक्रेता समुदाय</span></td><td class="no"><span data-lang="en">No dedicated ecosystem</span><span data-lang="hi">कोई समर्पित प्रणाली नहीं</span></td><td class="yes">YES</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- VISION -->
<div class="vision-section">
    <div class="container">
        <blockquote>
            <span data-lang="en">"Every Loha Mandi Seller Should Have a Digital Identity."</span>
            <span data-lang="hi">"हर लोहा मंडी विक्रेता की एक डिजिटल पहचान होनी चाहिए।"</span>
        </blockquote>
        <p>
            <span data-lang="en">And every buyer should be able to say: "If I need Steel or Iron from Loha Mandi Ghaziabad, I know exactly where to look."</span>
            <span data-lang="hi">और हर खरीदार कह सके: "अगर मुझे लोहा मंडी घाज़ियाबाद से स्टील या लोहा चाहिए, तो मुझे पता है कहां देखना है।"</span>
        </p>
        <div class="brand-big">🌐 LOHA Mandi</div>
    </div>
</div>

<!-- CTA -->
<div class="cta-section">
    <div class="container">
        <h2>
            <span data-lang="en">Join the Digital Marketplace</span>
            <span data-lang="hi">डिजिटल बाज़ार से जुड़ें</span>
        </h2>
        <p>
            <span data-lang="en">Your shop is already in Loha Mandi. Your customers are already here. Now bring your business ONLINE.</span>
            <span data-lang="hi">आपकी दुकान पहले से लोहा मंडी में है। आपके ग्राहक पहले से यहां हैं। अब अपना व्यापार ऑनलाइन लाएं।</span>
        </p>
        <a class="btn btn-white" href="<?= esc($base) ?>/register">
            <span data-lang="en">Join Loha Mandi</span>
            <span data-lang="hi">लोहा मार्ट से जुड़ें</span>
        </a>
    </div>
</div>

<footer>
    <div class="container">
        <div><b>LOHA Mandi</b>The Digital Marketplace of Loha Mandi, Ghaziabad.</div>
        <div>
            <b>Quick Links</b>
            <a href="<?= esc($base) ?>/catalog" style="display:block;margin-bottom:4px">Buy on Loha Mandi</a>
            <a href="<?= esc($base) ?>/suppliers" style="display:block;margin-bottom:4px">Sell on Loha Mandi</a>
            <a href="<?= esc($base) ?>/post-rfq" style="display:block;margin-bottom:4px">Post RFQ</a>
            <a href="<?= esc($base) ?>/register" style="display:block;margin-bottom:4px">Join Loha Mandi</a>
            <a href="<?= esc($base) ?>/privacy-policy" style="display:block;margin-bottom:4px">Privacy Policy</a>
            <a href="<?= esc($base) ?>/terms-of-service" style="display:block;margin-bottom:4px">Terms of Service</a>
        </div>
        <div><b>Contact</b>
            Loha Mandi, Ghaziabad, UP<br>
            <a href="mailto:info@lohamandigzb.online" style="color:var(--brand)">info@lohamandigzb.online</a>
        </div>
        <div><b>Developed & Maintained by</b>VDS INFOTECH</div>
    </div>
    <div class="container" style="border-top:1px solid rgba(255,255,255,.08);margin-top:20px;padding-top:16px;justify-content:center;font-size:12px">
        &copy; 2026 LOHA Mandi. All rights reserved.
    </div>
</footer>

<script>
function toggleAcc(head){
    var item=head.parentElement;
    var wasOpen=item.classList.contains('open');
    document.querySelectorAll('.acc-item').forEach(function(i){i.classList.remove('open')});
    if(!wasOpen) item.classList.add('open');
}

function setLang(lang){
    if(lang==='hi'){
        document.body.classList.add('hi');
        document.getElementById('btnHi').classList.add('active');
        document.getElementById('btnEn').classList.remove('active');
    } else {
        document.body.classList.remove('hi');
        document.getElementById('btnEn').classList.add('active');
        document.getElementById('btnHi').classList.remove('active');
    }
    localStorage.setItem('lm_lang', lang);
}

// Restore saved language
(function(){
    var saved = localStorage.getItem('lm_lang');
    if(saved === 'hi') setLang('hi');
})();
</script>
</body>
</html>
