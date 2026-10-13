<?php
/**
 * Community Promotion — लोहे का कारोबार… अब अपनी लोकल कम्युनिटी के साथ!
 */
$base = rtrim(base_url(), '/');
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Promotion — LOHA Mandi</title>
    <style>
        :root{--brand:#e02735;--brand-dark:#b81f2b;--ink:#1a1d23;--muted:#6b7280;--bg:#f6f7f9;--card:#fff;--line:#e5e7eb;--teal:#0f766e;--teal-light:#14b8a6;--purple:#4338ca;--amber:#b45309;--gold:#f59e0b}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',system-ui,-apple-system,Noto Sans Devanagari,Arial,sans-serif;color:var(--ink);background:var(--bg);line-height:1.7}
        a{color:inherit;text-decoration:none}
        .container{max-width:960px;margin:0 auto;padding:0 20px}

        /* NAV */
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:60px;gap:20px}
        .brand{display:flex;align-items:center;gap:10px}
        .brand img{height:44px;width:auto}
        .nav-links{display:flex;gap:22px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:10px 22px;border-radius:8px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.2s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark);transform:translateY(-1px);box-shadow:0 4px 16px rgba(224,39,53,.35)}
        .btn-white{background:#fff;color:var(--brand)}
        .btn-white:hover{background:#f0f0f0;transform:translateY(-1px)}
        .btn-outline{background:transparent;border:2px solid #fff;color:#fff}
        .btn-outline:hover{background:#fff;color:var(--brand)}
        .hamburger{display:none;background:none;border:none;font-size:26px;cursor:pointer;color:var(--ink)}

        /* HERO */
        .hero{background:linear-gradient(135deg,#0f172a 0%,#1e293b 35%,#334155 100%);color:#fff;padding:80px 0 90px;position:relative;overflow:hidden;text-align:center}
        .hero::before{content:"";position:absolute;top:-80px;right:-80px;width:400px;height:400px;border-radius:50%;background:rgba(224,39,53,.12);filter:blur(80px)}
        .hero::after{content:"";position:absolute;bottom:-60px;left:-60px;width:300px;height:300px;border-radius:50%;background:rgba(20,184,166,.1);filter:blur(70px)}
        .hero-badge{display:inline-block;background:rgba(224,39,53,.2);border:1px solid rgba(224,39,53,.3);color:#ff8d97;font-size:13px;font-weight:700;padding:6px 18px;border-radius:20px;margin-bottom:18px;letter-spacing:.5px;position:relative;z-index:2}
        .hero h1{font-size:42px;line-height:1.2;margin-bottom:16px;position:relative;z-index:2}
        .hero h1 .hi{color:#ff6b6b}
        .hero .tagline{font-size:20px;color:#94a3b8;max-width:640px;margin:0 auto 10px;position:relative;z-index:2;font-style:italic}
        .hero .subtitle{font-size:16px;color:#64748b;max-width:560px;margin:0 auto 32px;position:relative;z-index:2}
        .hero-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative;z-index:2}

        /* VIDEO SCRIPT */
        .script-section{padding:56px 0}
        .sec-header{text-align:center;margin-bottom:36px}
        .sec-header .tag{display:inline-block;background:rgba(224,39,53,.08);color:var(--brand);font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px}
        .sec-header h2{font-size:30px;font-weight:800;margin-bottom:6px}
        .sec-header p{color:var(--muted);font-size:15px;max-width:560px;margin:0 auto}

        .script-timeline{max-width:760px;margin:0 auto;position:relative}
        .script-timeline::before{content:"";position:absolute;top:0;bottom:0;left:28px;width:3px;background:linear-gradient(180deg,var(--brand),var(--teal),var(--purple),var(--amber));border-radius:3px}
        .script-step{display:flex;gap:24px;margin-bottom:28px;position:relative}
        .step-dot{width:58px;height:58px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;z-index:2;box-shadow:0 4px 16px rgba(0,0,0,.1)}
        .step-dot.red{background:linear-gradient(135deg,var(--brand),#ff6b6b);color:#fff}
        .step-dot.teal{background:linear-gradient(135deg,var(--teal),var(--teal-light));color:#fff}
        .step-dot.purple{background:linear-gradient(135deg,var(--purple),#818cf8);color:#fff}
        .step-dot.amber{background:linear-gradient(135deg,var(--amber),#fbbf24);color:#fff}
        .step-dot.dark{background:linear-gradient(135deg,#0f172a,#334155);color:#fff}
        .step-dot.gold{background:linear-gradient(135deg,#b45309,#f59e0b);color:#fff}
        .step-card{flex:1;background:#fff;border:1px solid var(--line);border-radius:14px;padding:22px 26px;transition:.2s}
        .step-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.07);transform:translateY(-2px)}
        .step-card .scene{font-size:11px;font-weight:700;color:var(--brand);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
        .step-card h3{font-size:17px;margin-bottom:6px}
        .step-card p{font-size:14px;color:#374151;line-height:1.6}
        .step-card .vo{background:linear-gradient(90deg,rgba(224,39,53,.06),rgba(224,39,53,.02));border-left:3px solid var(--brand);padding:10px 14px;border-radius:0 8px 8px 0;margin-top:10px;font-size:13.5px;color:#1e293b;line-height:1.6}
        .step-card .highlight-box{background:linear-gradient(135deg,var(--teal),var(--teal-light));color:#fff;border-radius:10px;padding:12px 16px;margin-top:10px;font-weight:700;font-size:14px;text-align:center}

        /* FEATURES STRIP */
        .features{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;padding:48px 0}
        .feat{background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px 20px;text-align:center;transition:.2s}
        .feat:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(0,0,0,.08)}
        .feat .ico{font-size:40px;margin-bottom:10px}
        .feat h4{font-size:15px;margin-bottom:4px}
        .feat p{font-size:12.5px;color:var(--muted)}

        /* CTA BANNER */
        .cta-banner{background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;padding:60px 0;text-align:center;position:relative;overflow:hidden}
        .cta-banner::before{content:"";position:absolute;top:-60px;left:-60px;width:250px;height:250px;border-radius:50%;background:rgba(255,255,255,.08)}
        .cta-banner::after{content:"";position:absolute;bottom:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.05)}
        .cta-banner h2{font-size:32px;margin-bottom:10px;position:relative;z-index:2}
        .cta-banner .tagline-hi{font-size:18px;opacity:.9;margin-bottom:24px;font-style:italic;position:relative;z-index:2}
        .cta-banner .tagline-en{font-size:14px;opacity:.7;margin-bottom:28px;position:relative;z-index:2}
        .cta-banner .btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative;z-index:2}

        /* STATS */
        .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin:-36px auto 0;position:relative;z-index:5;max-width:800px}
        .stat-box{background:#fff;border-radius:12px;padding:20px;text-align:center;box-shadow:0 8px 30px rgba(0,0,0,.08);margin:0 8px}
        .stat-box .icon{font-size:28px;margin-bottom:6px}
        .stat-box b{font-size:24px;display:block;color:var(--ink)}
        .stat-box span{color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}

        /* VISION */
        .vision{background:linear-gradient(135deg,#0f172a,#1e293b);padding:56px 0;text-align:center;color:#fff;position:relative;overflow:hidden}
        .vision::before{content:"";position:absolute;top:-50%;left:-20%;width:400px;height:400px;border-radius:50%;background:rgba(224,39,53,.08);filter:blur(80px)}
        .vision blockquote{font-size:22px;font-weight:700;max-width:620px;margin:0 auto 14px;position:relative;z-index:2;line-height:1.4;color:#ff8d97}
        .vision .brand-link{display:inline-block;margin-top:16px;font-size:20px;font-weight:800;color:#ff6b6b;position:relative;z-index:2}

        /* STRATEGY SECTIONS */
        .strategy-section{padding:56px 0}
        .strategy-section:nth-child(even){background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
        .strategy-section .sec-header{text-align:center;margin-bottom:36px}
        .strategy-section .sec-header .tag{display:inline-block;background:rgba(224,39,53,.08);color:var(--brand);font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px}
        .strategy-section .sec-header h2{font-size:28px;font-weight:800;margin-bottom:6px}
        .strategy-section .sec-header p{color:var(--muted);font-size:15px;max-width:600px;margin:0 auto}

        .positioning-box{background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:16px;padding:36px;text-align:center;margin-bottom:30px}
        .positioning-box h3{font-size:24px;margin-bottom:12px}
        .positioning-box .sub{color:#94a3b8;font-size:15px;max-width:500px;margin:0 auto}
        .positioning-box .core-msg{background:rgba(224,39,53,.15);border:1px solid rgba(224,39,53,.3);border-radius:12px;padding:18px 24px;margin-top:20px;font-size:16px;font-weight:700;color:#ff8d97}

        .message-box{background:#fff;border:2px solid var(--brand);border-radius:16px;padding:30px;max-width:700px;margin:0 auto}
        .message-box h3{font-size:20px;margin-bottom:14px;color:var(--brand)}
        .message-box .checklist{list-style:none;margin:16px 0}
        .message-box .checklist li{padding:6px 0;font-size:15px;color:#374151}
        .message-box .checklist li::before{content:"✅ ";color:#16a34a;font-weight:700}
        .message-box .cta-line{background:var(--brand);color:#fff;text-align:center;padding:14px;border-radius:10px;font-weight:700;font-size:16px;margin-top:16px}

        .diff-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px}
        .diff-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;text-align:center}
        .diff-card h4{font-size:16px;margin-bottom:6px}
        .diff-card p{color:var(--muted);font-size:13px}
        .diff-card.national{border-color:#e5e7eb}
        .diff-card.local{border-color:var(--brand);background:#fef2f2}
        .diff-card.local h4{color:var(--brand)}

        .benefits-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
        .benefit-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;transition:.2s}
        .benefit-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.08);transform:translateY(-3px)}
        .benefit-card .ico{font-size:36px;margin-bottom:10px}
        .benefit-card h4{font-size:16px;margin-bottom:6px}
        .benefit-card p{color:var(--muted);font-size:13.5px}

        .spotlight-card{background:linear-gradient(135deg,#fef3f3,#fff);border:2px solid var(--brand);border-radius:16px;padding:30px;max-width:600px;margin:0 auto;text-align:center}
        .spotlight-card h3{font-size:18px;color:var(--brand);margin-bottom:10px}
        .spotlight-card .biz-name{font-size:24px;font-weight:800;margin-bottom:4px}
        .spotlight-card .biz-loc{color:var(--muted);font-size:14px;margin-bottom:14px}
        .spotlight-card .products{list-style:none;display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin:14px 0}
        .spotlight-card .products li{background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600}

        .whatsapp-box{background:#25d366;color:#fff;border-radius:16px;padding:30px;max-width:600px;margin:0 auto}
        .whatsapp-box h3{font-size:20px;margin-bottom:14px}
        .whatsapp-box .checklist{list-style:none;margin:16px 0}
        .whatsapp-box .checklist li{padding:5px 0;font-size:14.5px}
        .whatsapp-box .checklist li::before{content:"✅ ";font-weight:700}

        .field-sales-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;margin:16px 0}
        .field-sales-grid .item{background:#fff;border:1px solid var(--line);border-radius:10px;padding:12px;text-align:center;font-weight:600;font-size:13.5px}
        .field-sales-grid .item::before{content:"• ";color:var(--brand)}

        .qr-box{background:linear-gradient(135deg,var(--purple),#6366f1);color:#fff;border-radius:16px;padding:30px;max-width:500px;margin:0 auto;text-align:center}
        .qr-box h3{font-size:20px;margin-bottom:14px}
        .qr-box .checklist{list-style:none;margin:14px 0}
        .qr-box .checklist li{padding:4px 0;font-size:14px}

        .verified-badge{display:inline-block;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;padding:8px 20px;border-radius:20px;font-weight:700;font-size:14px;margin:10px 0}

        .social-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px}
        .social-card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:20px;text-align:center;transition:.2s}
        .social-card:hover{box-shadow:0 6px 16px rgba(0,0,0,.06);transform:translateY(-2px)}
        .social-card .letter{font-size:24px;font-weight:800;color:var(--brand);margin-bottom:6px}
        .social-card h4{font-size:14px;margin-bottom:4px}
        .social-card p{color:var(--muted);font-size:12px}

        .seo-list{list-style:none;max-width:600px;margin:16px auto}
        .seo-list li{background:#fff;border:1px solid var(--line);border-radius:8px;padding:10px 16px;margin-bottom:6px;font-family:Consolas,monospace;font-size:13px;color:var(--teal)}

        .cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
        .cat-card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px;text-align:center;transition:.2s}
        .cat-card:hover{border-color:var(--brand);transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.06)}
        .cat-card .ico{font-size:32px;margin-bottom:6px}
        .cat-card h4{font-size:14px}

        .flywheel{display:flex;flex-direction:column;align-items:center;gap:0;margin:20px auto;max-width:400px}
        .flywheel .step{background:#fff;border:2px solid var(--line);border-radius:12px;padding:12px 20px;font-weight:700;font-size:14px;text-align:center;width:100%}
        .flywheel .step.highlight{border-color:var(--brand);background:#fef2f2;color:var(--brand)}
        .flywheel .arrow{font-size:20px;color:var(--brand);margin:4px 0}

        .plan-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06);margin-top:20px}
        .plan-table thead th{background:var(--ink);color:#fff;padding:14px 16px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;text-align:left}
        .plan-table tbody td{padding:13px 16px;border-bottom:1px solid var(--line);font-size:14px}
        .plan-table tbody tr:last-child td{border-bottom:0}
        .plan-table tbody tr:hover{background:#fafbfc}

        .metrics-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px}
        .metric-card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px;text-align:center}
        .metric-card .arrow{color:#16a34a;font-size:24px;font-weight:800}
        .metric-card p{font-size:13.5px;font-weight:600;margin-top:4px}

        .cta-cards{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:600px;margin:0 auto}
        .cta-card{border-radius:14px;padding:24px;text-align:center;color:#fff;font-weight:700;font-size:16px}
        .cta-card.sell{background:linear-gradient(135deg,var(--brand),var(--brand-dark))}
        .cta-card.buy{background:linear-gradient(135deg,var(--teal),var(--teal-light))}

        .priority-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px}
        .priority-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;text-align:center}
        .priority-card .num{font-size:28px;font-weight:800;color:var(--brand);margin-bottom:8px}
        .priority-card h4{font-size:15px;margin-bottom:6px}
        .priority-card p{color:var(--muted);font-size:13px}

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

        @media(max-width:768px){
            .nav-links{display:none}
            .hamburger{display:block}
            .hero{padding:50px 0 56px}
            .hero h1{font-size:26px}
            .hero .tagline{font-size:16px}
            .stats-row{grid-template-columns:repeat(2,1fr);margin-top:-28px;gap:8px}
            .stat-box{margin:0 4px;padding:14px 10px}
            .stat-box b{font-size:20px}
            .script-timeline::before{left:22px}
            .step-dot{width:46px;height:46px;font-size:20px}
            .step-card{padding:16px 18px}
            .step-card h3{font-size:15px}
            .sec-header h2{font-size:22px}
            .cta-banner h2{font-size:24px}
            footer .container{flex-direction:column;text-align:center}
        }
        @media(max-width:480px){
            .stats-row{grid-template-columns:1fr 1fr}
            .features{grid-template-columns:1fr 1fr}
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
        <div><a class="btn btn-primary" href="<?= esc($base) ?>/register">Register Now</a></div>
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
        <a href="<?= esc($base) ?>/register">Register</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <div class="container">
        <div class="hero-badge">📢 Community Promotion</div>
        <h1>लोहे का कारोबार…<br><span class="hi">अब अपनी लोकल कम्युनिटी के साथ!</span></h1>
        <p class="tagline">"Loha Mandi — अपने लोगों के लिए, अपने कारोबार के लिए।"</p>
        <p class="subtitle">Register Now. Grow Local. Connect Directly.</p>
        <div class="hero-btns">
            <a class="btn btn-primary" href="<?= esc($base) ?>/register">अभी जुड़ें — Register</a>
            <a class="btn btn-outline" href="<?= esc($base) ?>/faq">और जानें</a>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="container">
    <div class="stats-row">
        <div class="stat-box"><div class="icon">🏭</div><b>100%</b><span>लोकल फोकस</span></div>
        <div class="stat-box"><div class="icon">🤝</div><b>Direct</b><span>बिज़नेस कनेक्ट</span></div>
        <div class="stat-box"><div class="icon">🚚</div><b>Built-in</b><span>ट्रांसपोर्ट</span></div>
        <div class="stat-box"><div class="icon">📱</div><b>24×7</b><span>डिजिटल शॉप</span></div>
    </div>
</div>

<!-- VIDEO SCRIPT / STORY -->
<div class="script-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Promotion Script</div>
            <h2>प्रमोशन स्क्रिप्ट — देखें, सुनें, जुड़ें</h2>
            <p>हर स्टेप पर क्लिक करें और पूरी स्क्रिप्ट पढ़ें</p>
        </div>

        <div class="script-timeline">

            <!-- Step 1 -->
            <div class="script-step">
                <div class="step-dot red">🎬</div>
                <div class="step-card">
                    <div class="scene">Opening — Loha Mandi Ghaziabad Visuals</div>
                    <h3>क्या आप लोहे का कारोबार करते हैं?</h3>
                    <div class="vo">🎙️ "क्या आप लोहे का कारोबार करते हैं और लोहे की मंडी, गाज़ियाबाद से जुड़े हैं? तो अब समय है अपने कारोबार को सिर्फ लोकल नहीं… डिजिटल लोकल बनाने का!"</div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="script-step">
                <div class="step-dot teal">🌐</div>
                <div class="step-card">
                    <div class="scene">Show Website / Mobile Screen</div>
                    <h3>मिलिए — LohaMandiGZB.online</h3>
                    <p>एक ऐसा B2B प्लेटफॉर्म, जिसे खास तौर पर Loha Mandi, Ghaziabad की स्थानीय बिज़नेस कम्युनिटी को ध्यान में रखकर बनाया गया है।</p>
                    <div class="vo">🎙️ "यह कोई सामान्य ऑनलाइन मार्केटप्लेस नहीं है। यह हमारा अपना लोकल बिज़नेस नेटवर्क है।"</div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="script-step">
                <div class="step-dot purple">👥</div>
                <div class="step-card">
                    <div class="scene">Show Sellers, Buyers, Enquiries</div>
                    <h3>एक ही प्लेटफॉर्म पर जुड़ें</h3>
                    <p>यहाँ Loha Mandi के <b>Manufacturers, Dealers, Traders, Suppliers</b> और <b>Buyers</b> एक ही प्लेटफॉर्म पर जुड़ सकते हैं।</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="script-step">
                <div class="step-dot amber">📬</div>
                <div class="step-card">
                    <div class="scene">Highlight — Direct Enquiries</div>
                    <h3>सबसे बड़ी बात? सीधी enquiries!</h3>
                    <div class="vo">🎙️ "आपके बिज़नेस से जुड़ी enquiries सीधे आपके account तक पहुँच सकती हैं। बीच में लंबी दूरी का नेटवर्क नहीं।"</div>
                    <div class="highlight-box">अपने शहर और अपने व्यापारिक क्षेत्र की सीधी business connectivity</div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="script-step">
                <div class="step-dot teal">🚚</div>
                <div class="step-card">
                    <div class="scene">Transportation / Delivery Visuals</div>
                    <h3>ट्रांसपोर्ट मैनेजमेंट — आने वाला फ़ायदा</h3>
                    <div class="vo">🎙️ "आने वाले समय में Transportation Management जैसी सुविधाएँ आपके व्यापार को और आसान बनाने में मदद करेंगी।"</div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="script-step">
                <div class="step-dot red">⚡</div>
                <div class="step-card">
                    <div class="scene">Urgency — Strong Visuals</div>
                    <h3>यह प्लेटफॉर्म तभी मज़बूत बनेगा जब...</h3>
                    <div class="vo">🎙️ "जितने ज्यादा local sellers जुड़ेंगे… उतने ज्यादा buyers को Loha Mandi के products और businesses एक ही जगह मिलेंगे। और उतने ही ज्यादा business opportunities हमारी पूरी community के लिए बनेंगी।"</div>
                </div>
            </div>

            <!-- Step 7 -->
            <div class="script-step">
                <div class="step-dot gold">🚀</div>
                <div class="step-card">
                    <div class="scene">Call to Action</div>
                    <h3>तो इंतज़ार क्यों? आज ही जुड़ें!</h3>
                    <div class="vo">🎙️ "अगर आपका कारोबार Loha Mandi, Ghaziabad से जुड़ा है — आज ही अपना business register करें। अपना business profile बनाइए। अपने products और services showcase कीजिए। और Loha Mandi की अपनी digital B2B community का हिस्सा बनिए।"</div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- FEATURES -->
<div class="container">
    <div class="features">
        <div class="feat"><div class="ico">🏭</div><h4>लोकल मार्केटप्लेस</h4><p>सिर्फ लोहा मंडी, घाज़ियाबाद के लिए</p></div>
        <div class="feat"><div class="ico">📩</div><h4>सीधी Enquiries</h4><p>बिचौलिया हटाओ, सीधे जुड़ो</p></div>
        <div class="feat"><div class="ico">🏪</div><h4>डिजिटल शॉप</h4><p>24×7 आपकी दुकान खुली रहे</p></div>
        <div class="feat"><div class="ico">🤝</div><h4>बिज़नेस नेटवर्क</h4><p>खरीदार, विक्रेता, ट्रांसपोर्टर सब एक जगह</p></div>
        <div class="feat"><div class="ico">🚚</div><h4>ट्रांसपोर्ट</h4><p>आने वाले समय में ट्रांसपोर्ट मैनेजमेंट</p></div>
        <div class="feat"><div class="ico">📊</div><h4>बिज़नेस डेटा</h4><p>अपने खाते में सब कुछ मैनेज करें</p></div>
    </div>
</div>

<!-- CTA BANNER -->
<div class="cta-banner">
    <div class="container">
        <h2>🚨 आज ही जुड़ें!</h2>
        <div class="tagline-hi">"Loha Mandi का अपना B2B Marketplace — अपने लोगों के लिए, अपने कारोबार के लिए।"</div>
        <div class="tagline-en">Register Now. Grow Local. Connect Directly.</div>
        <div class="btns">
            <a class="btn btn-white" href="<?= esc($base) ?>/register">अभी Register करें</a>
            <a class="btn btn-outline" href="<?= esc($base) ?>/catalog">Products देखें</a>
        </div>
    </div>
</div>

<!-- VISION -->
<div class="vision">
    <div class="container">
        <blockquote>"हर लोहा मंडी विक्रेता की एक डिजिटल पहचान होनी चाहिए।"</blockquote>
        <p style="color:#94a3b8;font-size:15px;max-width:560px;margin:0 auto">और हर खरीदार कह सके: "अगर मुझे लोहा मंडी घाज़ियाबाद से स्टील या लोहा चाहिए, तो मुझे पता है कहां देखना है।"</p>
        <div class="brand-link">🌐 LOHAMANDIGZB.ONLINE</div>
    </div>
</div>

<!-- ============================================ -->
<!-- SELLER MARKETING STRATEGY — NEW SECTIONS     -->
<!-- ============================================ -->

<!-- CORE POSITIONING -->
<div class="strategy-section">
    <div class="container">
        <div class="positioning-box">
            <h3>Loha Mandi's Digital B2B Marketplace</h3>
            <div class="sub">Where Loha Mandi Businesses Meet Buyers Online</div>
            <div class="core-msg">Your business is already in Loha Mandi. Now take your business online with LohaMandiGzb.online.</div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;max-width:700px;margin:0 auto;text-align:center">
            <div style="background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px"><div style="font-size:28px;margin-bottom:6px">🏭</div><b>Local Businesses</b></div>
            <div style="background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px"><div style="font-size:28px;margin-bottom:6px">🌐</div><b>Online Visibility</b></div>
            <div style="background:#fff;border:1px solid var(--line);border-radius:12px;padding:18px"><div style="font-size:28px;margin-bottom:6px">📩</div><b>Buyer Enquiries</b></div>
        </div>
    </div>
</div>

<!-- MAIN SELLER MESSAGE -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Seller Acquisition Campaign</div>
            <h2>🏭 LOHA Mandi — GO DIGITAL!</h2>
            <p>Your business is already part of one of Ghaziabad's important industrial markets. Now make your business discoverable online.</p>
        </div>
        <div class="message-box">
            <h3>LOHA Mandi</h3>
            <p style="color:var(--muted);font-size:14px">A dedicated B2B marketplace focused on connecting buyers with businesses from Loha Mandi, Ghaziabad.</p>
            <ul class="checklist">
                <li>Create your business profile</li>
                <li>Showcase your products</li>
                <li>Reach potential buyers</li>
                <li>Receive business enquiries</li>
                <li>Connect directly with customers</li>
                <li>Build your online presence</li>
                <li>Promote your products and services</li>
            </ul>
            <p style="font-weight:700;color:var(--ink);font-size:15px;margin-top:14px">📍 Your Market. Your Business. Your Online Marketplace.</p>
            <p style="color:var(--muted);font-size:14px;margin-top:6px">Don't wait for customers to find your competitor online.</p>
            <div class="cta-line">👉 REGISTER YOUR BUSINESS TODAY — 🌐 www.lohamandigzb.online</div>
        </div>
    </div>
</div>

<!-- BIGGEST DIFFERENTIATOR -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Your Biggest Differentiator</div>
            <h2>"LOCAL FOCUS"</h2>
            <p>A marketplace built specifically around the Loha Mandi business community.</p>
        </div>
        <div class="diff-grid" style="max-width:700px;margin:0 auto">
            <div class="diff-card national">
                <h4>IndiaMandi</h4>
                <p>India-wide · Serves businesses everywhere</p>
            </div>
            <div class="diff-card local">
                <h4>LohaMandiGzb.online</h4>
                <p>Loha Mandi-focused · Built for our community</p>
            </div>
        </div>
        <p style="text-align:center;color:var(--muted);font-size:14px;margin-top:20px;max-width:600px;margin-left:auto;margin-right:auto">Large national marketplaces try to serve businesses everywhere. LohaMandigzb.online is focused on <b style="color:var(--ink)">Loha Mandi businesses</b> — giving you a very specific identity.</p>
    </div>
</div>

<!-- SELLER BENEFITS -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Why Should Your Business Join?</div>
            <h2>Seller Benefits</h2>
        </div>
        <div class="benefits-grid">
            <div class="benefit-card"><div class="ico">🔎</div><h4>Get Discovered Online</h4><p>Potential buyers can discover your business, products and contact information online.</p></div>
            <div class="benefit-card"><div class="ico">🏭</div><h4>Showcase Your Products</h4><p>Create product listings with specifications, images, sizes, brands and other relevant information.</p></div>
            <div class="benefit-card"><div class="ico">📲</div><h4>Receive Enquiries</h4><p>Give potential customers a simple way to contact your business.</p></div>
            <div class="benefit-card"><div class="ico">🤝</div><h4>Connect Directly</h4><p>Buyers can connect with sellers rather than simply browsing generic product information.</p></div>
            <div class="benefit-card"><div class="ico">📍</div><h4>Promote Your Loha Mandi Presence</h4><p>Show buyers that your business operates from Loha Mandi, Ghaziabad.</p></div>
            <div class="benefit-card"><div class="ico">🚚</div><h4>Transportation Management</h4><p>Provide transportation/dispatch-related functionality to help manage delivery requirements.</p></div>
            <div class="benefit-card"><div class="ico">🌐</div><h4>Build Your Digital Identity</h4><p>Give your traditional offline business a stronger online presence.</p></div>
        </div>
    </div>
</div>

<!-- SELLER SPOTLIGHT -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Organic Marketing</div>
            <h2>🏭 Seller Spotlight Program</h2>
            <p>Every week select several sellers and feature them across social media. The seller shares the post with their customers — free secondary distribution.</p>
        </div>
        <div class="spotlight-card">
            <h3>🏭 SELLER SPOTLIGHT</h3>
            <div class="biz-name">ABC Steel Traders</div>
            <div class="biz-loc">📍 Loha Mandi, Ghaziabad</div>
            <p style="font-size:14px;color:var(--muted)">Products:</p>
            <ul class="products">
                <li>MS Sheets</li>
                <li>MS Plates</li>
                <li>Steel Pipes</li>
                <li>Angles</li>
                <li>Channels</li>
            </ul>
            <p style="font-size:13px;color:var(--brand);font-weight:700;margin-top:10px">Find this business on LohaMandiGzb.online</p>
        </div>
    </div>
</div>

<!-- WHATSAPP CAMPAIGN -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Major Channel</div>
            <h2>📱 WhatsApp Seller Campaign</h2>
            <p>Create a dedicated seller-acquisition WhatsApp campaign.</p>
        </div>
        <div class="whatsapp-box">
            <h3>🏭 LOHA Mandi BUSINESS OWNERS — GO DIGITAL!</h3>
            <p style="font-size:14px;opacity:.9">Is your business in Loha Mandi, Ghaziabad?</p>
            <p style="font-size:14px;opacity:.9">Now showcase your business and products online through Loha Mandi.</p>
            <ul class="checklist">
                <li>Business profile</li>
                <li>Product listings</li>
                <li>Buyer enquiries</li>
                <li>Direct customer connection</li>
                <li>Online business visibility</li>
            </ul>
            <p style="font-size:13px;opacity:.8;margin-top:10px">📍 Built around the Loha Mandi business community.</p>
            <p style="font-weight:700;font-size:16px;margin-top:14px">👉 Register your business today: www.lohamandigzb.online</p>
        </div>
    </div>
</div>

<!-- FIELD SALES -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Go Directly Into Loha Mandi</div>
            <h2>🚶 Field Sales — Seller Acquisition Team</h2>
            <p>Don't rely entirely on Facebook advertising. Visit businesses in person and register them immediately.</p>
        </div>
        <div class="field-sales-grid" style="max-width:800px;margin:0 auto">
            <div class="item">Steel Traders</div>
            <div class="item">Iron Traders</div>
            <div class="item">MS Product Dealers</div>
            <div class="item">Pipe Suppliers</div>
            <div class="item">Sheet Suppliers</div>
            <div class="item">TMT Dealers</div>
            <div class="item">Hardware Businesses</div>
            <div class="item">Fabricators</div>
            <div class="item">Manufacturers</div>
            <div class="item">Stockists</div>
            <div class="item">Transporters</div>
            <div class="item">Industrial Suppliers</div>
            <div class="item">Other B2B Businesses</div>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;max-width:700px;margin:20px auto 0;text-align:center">
            <p style="font-size:15px;color:var(--ink);font-weight:600;font-style:italic">"Sir, we are creating the online B2B marketplace specifically for Loha Mandi businesses. We can put your business and products online and help buyers discover you."</p>
            <p style="color:var(--muted);font-size:13px;margin-top:10px">Show them the website on a phone. Don't just give them a brochure. Register them immediately.</p>
        </div>
    </div>
</div>

<!-- QR CAMPAIGN -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Offline Marketing</div>
            <h2>📲 QR Registration Campaign</h2>
            <p>Print cards and posters. Put them in shops, offices and other permitted local locations.</p>
        </div>
        <div class="qr-box">
            <h3>IS YOUR BUSINESS ON LOHAMANDIGZB.ONLINE?</h3>
            <p style="font-size:16px;font-weight:700;margin:10px 0">📲 SCAN & REGISTER</p>
            <ul class="checklist">
                <li>List your business</li>
                <li>Showcase your products</li>
                <li>Connect with buyers</li>
                <li>Grow your online presence</li>
            </ul>
            <div style="background:#fff;color:var(--purple);padding:20px;border-radius:12px;margin-top:16px;font-weight:800;font-size:18px">[ QR CODE ]</div>
            <p style="margin-top:14px;font-weight:700">www.lohamandigzb.online</p>
        </div>
    </div>
</div>

<!-- VERIFIED BADGE -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Trust & Verification</div>
            <h2>✓ Verified Loha Mandi Business</h2>
            <p>Create trust between buyers and sellers with a verification badge.</p>
        </div>
        <div style="text-align:center">
            <div class="verified-badge">✓ VERIFIED LOHA Mandi BUSINESS</div>
            <p style="color:var(--muted);font-size:14px;margin-top:14px;max-width:500px;margin-left:auto;margin-right:auto">This badge appears on verified seller profiles. Only use "Verified" when you actually verify the business according to clearly defined criteria.</p>
        </div>
    </div>
</div>

<!-- SOCIAL MEDIA STRATEGY -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Content Marketing</div>
            <h2>📱 Social Media Strategy</h2>
            <p>Don't make every post "Register now!" — use five content categories to create an ecosystem.</p>
        </div>
        <div class="social-grid" style="max-width:800px;margin:0 auto">
            <div class="social-card"><div class="letter">A</div><h4>Seller Spotlight</h4><p>Meet today's Loha Mandi business.</p></div>
            <div class="social-card"><div class="letter">B</div><h4>Product Discovery</h4><p>Looking for MS Sheets in Ghaziabad?</p></div>
            <div class="social-card"><div class="letter">C</div><h4>Market Content</h4><p>Products and businesses available in Loha Mandi.</p></div>
            <div class="social-card"><div class="letter">D</div><h4>Educational Content</h4><p>How to choose the right steel grade?</p></div>
            <div class="social-card"><div class="letter">E</div><h4>Marketplace Promotion</h4><p>Find Loha Mandi suppliers online.</p></div>
        </div>
    </div>
</div>

<!-- GOOGLE / SEO STRATEGY -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Google Strategy</div>
            <h2>🔍 SEO & Search Strategy</h2>
            <p>Build SEO pages around specific searches. Create a searchable directory.</p>
        </div>
        <ul class="seo-list">
            <li>/steel-suppliers-loha-mandi-ghaziabad</li>
            <li>/iron-dealers-loha-mandi-ghaziabad</li>
            <li>/ms-steel-suppliers-ghaziabad</li>
            <li>/steel-traders-ghaziabad</li>
            <li>/tmt-dealers-ghaziabad</li>
            <li>/ms-pipe-suppliers-ghaziabad</li>
            <li>/steel-sheet-suppliers-ghaziabad</li>
            <li>/sellers/ — searchable directory</li>
        </ul>
    </div>
</div>

<!-- CATEGORY PAGES -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Build Category Pages</div>
            <h2>📂 Product Categories</h2>
            <p>Each category creates a natural path: Google Search → Product Page → Seller → Enquiry</p>
        </div>
        <div class="cat-grid" style="max-width:800px;margin:0 auto">
            <div class="cat-card"><div class="ico">🔩</div><h4>Steel Products</h4></div>
            <div class="cat-card"><div class="ico">🏗️</div><h4>Structural Steel</h4></div>
            <div class="cat-card"><div class="ico">📦</div><h4>MS Sheets</h4></div>
            <div class="cat-card"><div class="ico">🛠️</div><h4>MS Pipes</h4></div>
            <div class="cat-card"><div class="ico">🔧</div><h4>Angles</h4></div>
            <div class="cat-card"><div class="ico">🏭</div><h4>Channels</h4></div>
            <div class="cat-card"><div class="ico">⚙️</div><h4>Industrial Products</h4></div>
            <div class="cat-card"><div class="ico">🔥</div><h4>TMT Bars</h4></div>
            <div class="cat-card"><div class="ico">🚚</div><h4>Transportation</h4></div>
            <div class="cat-card"><div class="ico">🏗️</div><h4>Fabrication Services</h4></div>
        </div>
    </div>
</div>

<!-- SELLERS AS MARKETING PARTNERS -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Marketing Partners</div>
            <h2>🔗 Make Sellers Your Marketing Partners</h2>
            <p>Give every registered seller a unique profile URL. They share it — you get free marketing.</p>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px;max-width:600px;margin:0 auto">
            <p style="font-family:Consolas,monospace;font-size:14px;color:var(--teal);text-align:center;margin-bottom:16px">lohamandigzb.online/seller/abc-steels</p>
            <p style="font-weight:700;text-align:center;margin-bottom:12px">Sellers can share their profile on:</p>
            <div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center">
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">💬 WhatsApp</span>
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">🃏 Business Cards</span>
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">📧 Email Signatures</span>
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">📘 Facebook</span>
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">📸 Instagram</span>
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">🌐 Website</span>
                <span style="background:#f3f4f6;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600">📄 Quotations</span>
            </div>
        </div>
    </div>
</div>

<!-- DIGITAL COMMUNITY -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Community Concept</div>
            <h2>🤝 Join Loha Mandi Digital Community</h2>
        </div>
        <div style="text-align:center;max-width:600px;margin:0 auto">
            <p style="font-size:18px;font-weight:700;color:var(--ink);margin-bottom:14px">JOIN THE LOHA Mandi DIGITAL BUSINESS COMMUNITY</p>
            <p style="color:var(--muted);font-size:15px;margin-bottom:16px">Connecting Loha Mandi businesses with buyers, suppliers and business opportunities through one digital marketplace.</p>
            <a class="btn btn-primary" href="<?= esc($base) ?>/register">Join the Community</a>
        </div>
    </div>
</div>

<!-- FLYWHEEL -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Growth Engine</div>
            <h2>🔄 Buyer + Seller Flywheel</h2>
            <p>Build a self-reinforcing growth cycle.</p>
        </div>
        <div class="flywheel">
            <div class="step highlight">SELLERS</div>
            <div class="arrow">⬇️</div>
            <div class="step">Products Uploaded</div>
            <div class="arrow">⬇️</div>
            <div class="step">LohaMandi.online</div>
            <div class="arrow">⬇️</div>
            <div class="step">SEO + Social + Google</div>
            <div class="arrow">⬇️</div>
            <div class="step highlight">BUYERS</div>
            <div class="arrow">⬇️</div>
            <div class="step">Enquiries</div>
            <div class="arrow">⬇️</div>
            <div class="step">Sellers Receive Leads</div>
            <div class="arrow">⬇️</div>
            <div class="step">Sellers Tell Other Businesses</div>
            <div class="arrow">⬇️</div>
            <div class="step highlight">MORE SELLERS</div>
        </div>
    </div>
</div>

<!-- 90-DAY PLAN -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Execution</div>
            <h2>📅 90-Day Marketing Plan</h2>
        </div>
        <div style="overflow-x:auto">
            <table class="plan-table">
                <thead>
                    <tr><th>Period</th><th>Main Objective</th><th>Activities</th></tr>
                </thead>
                <tbody>
                    <tr><td><b>Days 1–30</b></td><td>Seller acquisition</td><td>Field visits, WhatsApp, direct registration</td></tr>
                    <tr><td><b>Days 31–60</b></td><td>Product expansion</td><td>Get sellers to upload products</td></tr>
                    <tr><td><b>Days 61–90</b></td><td>Buyer acquisition</td><td>Google SEO, Google Ads, social media</td></tr>
                    <tr><td><b>After 90 days</b></td><td>Marketplace growth</td><td>Seller referrals + buyer campaigns + SEO</td></tr>
                </tbody>
            </table>
        </div>
        <p style="text-align:center;color:var(--muted);font-size:14px;margin-top:16px">⚠️ Don't wait until you have hundreds of sellers before starting buyer marketing. Start SEO and content immediately.</p>
    </div>
</div>

<!-- TRACK THE RIGHT NUMBERS -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Analytics</div>
            <h2>📊 Track the Right Numbers</h2>
            <p>The most important metric isn't simply number of sellers. It is: Active Sellers + Products + Buyer Enquiries.</p>
        </div>
        <div class="metrics-grid" style="max-width:800px;margin:0 auto">
            <div class="metric-card"><div class="arrow">↑</div><p>Registered Sellers</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Verified Sellers</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Sellers With Products</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Total Products</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Buyer Registrations</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Buyer Enquiries</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Seller Responses</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>Repeat Buyers</p></div>
            <div class="metric-card"><div class="arrow">↑</div><p>WhatsApp Enquiries</p></div>
        </div>
    </div>
</div>

<!-- STRONG WEBSITE CTA -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Strong CTAs</div>
            <h2>🎯 Website Call-to-Action</h2>
        </div>
        <div class="cta-cards">
            <div class="cta-card sell">LIST YOUR BUSINESS<br><span style="font-size:13px;opacity:.8;font-weight:400">Join Loha Mandi's Digital Marketplace</span></div>
            <div class="cta-card buy">FIND SUPPLIERS<br><span style="font-size:13px;opacity:.8;font-weight:400">Browse Loha Mandi Products</span></div>
        </div>
    </div>
</div>

<!-- LONG-TERM BRAND MESSAGE -->
<div class="strategy-section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Brand Vision</div>
            <h2>🎯 Long-Term Brand Message</h2>
        </div>
        <div class="positioning-box" style="margin:0 auto">
            <h3>LOHAMANDI.ONLINE</h3>
            <p style="color:#94a3b8;font-size:16px;margin-bottom:4px">The Digital Marketplace for Loha Mandi</p>
            <p style="color:#ff8d97;font-size:14px;font-weight:600">Businesses. Products. Buyers. Opportunities.</p>
            <div class="core-msg">From Loha Mandi to the Digital Market.</div>
        </div>
    </div>
</div>

<!-- IMMEDIATE PRIORITY -->
<div class="strategy-section">
    <div class="container">
        <div class="sec-header">
            <div class="tag">Immediate Priority</div>
            <h2>🎯 The Next Phase — Focus on Three Things</h2>
        </div>
        <div class="priority-grid" style="max-width:900px;margin:0 auto">
            <div class="priority-card">
                <div class="num">1</div>
                <h4>Seller Acquisition</h4>
                <p>Physically visit Loha Mandi businesses and register them.</p>
            </div>
            <div class="priority-card">
                <div class="num">2</div>
                <h4>Seller Activation</h4>
                <p>Don't stop at registration; help them upload products and complete profiles.</p>
            </div>
            <div class="priority-card">
                <div class="num">3</div>
                <h4>Buyer Acquisition</h4>
                <p>Start SEO, Google Search and social content around actual products and suppliers.</p>
            </div>
        </div>
        <div style="text-align:center;margin-top:30px;background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;border-radius:14px;padding:24px;max-width:700px;margin-left:auto;margin-right:auto">
            <p style="font-size:16px;font-weight:700;margin-bottom:8px">The ultimate goal is not merely "more registrations."</p>
            <p style="font-size:15px;opacity:.9">It is to make a buyer think:</p>
            <p style="font-size:18px;font-weight:800;margin-top:10px;color:#ff8d97">"If I need a steel/iron/industrial supplier from Loha Mandi, I should search LohaMandigzb.online."</p>
            <p style="font-size:14px;opacity:.8;margin-top:10px">That is the brand habit worth building.</p>
        </div>
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
            <a href="<?= esc($base) ?>/faq" style="display:block;margin-bottom:4px">Why LOHA Mandi</a>
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

</body>
</html>
