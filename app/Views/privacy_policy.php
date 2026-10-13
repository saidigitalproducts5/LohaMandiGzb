<?php
/**
 * Privacy Policy page
 */
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
        body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;color:var(--ink);background:var(--bg);line-height:1.6}
        a{color:var(--brand);text-decoration:none}
        a:hover{text-decoration:underline}
        .container{max-width:900px;margin:0 auto;padding:0 20px}
        header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:60px;gap:20px}
        .brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px}
        .brand img{height:44px;width:auto}
        .nav-links{display:flex;gap:22px;font-weight:600;font-size:14px}
        .nav-links a:hover{color:var(--brand)}
        .btn{padding:10px 20px;border-radius:8px;font-weight:700;font-size:14px;border:0;cursor:pointer;display:inline-block;transition:.15s}
        .btn-primary{background:var(--brand);color:#fff}
        .btn-primary:hover{background:var(--brand-dark)}
        .hamburger{display:none;background:none;border:none;font-size:26px;cursor:pointer;color:var(--ink)}
        .page-header{background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#fff;padding:60px 0;text-align:center}
        .page-header h1{font-size:36px;margin-bottom:10px}
        .page-header p{color:#94a3b8;font-size:16px;max-width:600px;margin:0 auto}
        .content{padding:50px 0}
        .section{background:#fff;border:1px solid var(--line);border-radius:14px;padding:30px;margin-bottom:24px}
        .section h2{font-size:22px;margin-bottom:16px;color:var(--ink);padding-bottom:10px;border-bottom:1px solid var(--line)}
        .section h3{font-size:16px;margin:20px 0 10px;color:var(--ink)}
        .section p{margin-bottom:12px;color:#374151}
        .section ul{margin:10px 0 10px 20px;color:#374151}
        .section li{margin-bottom:6px}
        .section .muted{color:var(--muted);font-size:14px}
        .highlight{background:#fef3f3;border-left:4px solid var(--brand);padding:14px 18px;border-radius:0 8px 8px 0;margin:16px 0}
        .highlight strong{color:var(--brand)}
        footer{background:#0f172a;color:#94a3b8;padding:34px 0;font-size:14px}
        footer .container{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}
        footer b{color:#fff;display:block;margin-bottom:6px}
        footer a{color:#94a3b8;transition:.15s}
        footer a:hover{color:var(--brand)}
        .mobile-nav{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:30}
        .mobile-nav.open{display:block}
        .mobile-nav .drawer{position:fixed;top:0;left:0;width:280px;height:100%;background:#fff;z-index:31;padding:24px 20px;overflow-y:auto;box-shadow:4px 0 24px rgba(0,0,0,.2)}
        .mobile-nav .drawer a{display:block;padding:13px 0;font-weight:600;font-size:15px;border-bottom:1px solid var(--line)}
        .mobile-nav .drawer a:hover{color:var(--brand)}
        .mobile-nav .close-btn{background:none;border:none;font-size:28px;cursor:pointer;position:absolute;top:16px;right:16px;color:var(--muted)}
        @media(max-width:768px){
            .nav-links{display:none}
            .hamburger{display:block}
            .page-header h1{font-size:26px}
            .section{padding:20px}
            footer .container{flex-direction:column;text-align:center}
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
        <a class="btn btn-primary" href="<?= esc($base) ?>/register">Join Loha Mandi</a>
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

<div class="page-header">
    <div class="container">
        <h1>Privacy Policy</h1>
        <p>Last updated: September 2026</p>
    </div>
</div>

<div class="container content">
    <div class="section">
        <p class="muted">This Privacy Policy explains how LOHA Mandi ("we", "our", "us") collects, uses, and protects your personal information when you use our website and services.</p>
    </div>

    <div class="section">
        <h2>1. Information We Collect</h2>
        <p>We collect information you provide directly to us:</p>
        <ul>
            <li><strong>Account Information:</strong> Name, email, phone number, business name, GSTIN, address when you register as a buyer, seller, or transporter.</li>
            <li><strong>Business Information:</strong> Product listings, catalog data, pricing, inventory details, business verification documents.</li>
            <li><strong>Transaction Data:</strong> RFQs, quotations, orders, payment information, delivery details.</li>
            <li><strong>Communications:</strong> Messages between buyers and sellers, support tickets, inquiries.</li>
        </ul>
        <p>We also collect data automatically:</p>
        <ul>
            <li><strong>Usage Data:</strong> Pages visited, time spent, search queries, clicks, device information, IP address, browser type.</li>
            <li><strong>Cookies & Similar Technologies:</strong> Session cookies for authentication, preference cookies, analytics cookies.</li>
        </ul>
    </div>

    <div class="section">
        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>Provide and maintain the marketplace services</li>
            <li>Match buyers with relevant sellers based on location and product category</li>
            <li>Process RFQs, quotations, orders, and payments</li>
            <li>Send transactional notifications (order updates, quotes, messages)</li>
            <li>Verify seller identity and business credentials</li>
            <li>Improve our platform through analytics</li>
            <li>Comply with legal obligations</li>
            <li>Prevent fraud and ensure platform security</li>
        </ul>
    </div>

    <div class="section">
        <h2>3. Information Sharing</h2>
        <p>We do not sell your personal information. We may share data in these cases:</p>
        <ul>
            <li><strong>Between Users:</strong> Buyer contact details shared with sellers only after RFQ submission; seller details visible to buyers for listed products.</li>
            <li><strong>Service Providers:</strong> Payment processors, SMS/email services, cloud hosting (under strict confidentiality agreements).</li>
            <li><strong>Legal Requirements:</strong> When required by law, court order, or government authority.</li>
            <li><strong>Business Transfers:</strong> In case of merger, acquisition, or sale of assets (with user notification).</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. Data Security</h2>
        <p>We implement appropriate technical and organizational measures to protect your data:</p>
        <ul>
            <li>HTTPS encryption for all data in transit</li>
            <li>Encrypted storage for sensitive data (passwords hashed with bcrypt)</li>
            <li>Regular security audits and access controls</li>
            <li>Role-based access for admin and support staff</li>
        </ul>
        <p>However, no internet transmission is 100% secure. We cannot guarantee absolute security.</p>
    </div>

    <div class="section">
        <h2>5. Your Rights</h2>
        <p>Under applicable data protection laws, you may have the right to:</p>
        <ul>
            <li>Access your personal data</li>
            <li>Rectify inaccurate data</li>
            <li>Request deletion of your data (subject to legal retention requirements)</li>
            <li>Restrict or object to processing</li>
            <li>Data portability</li>
            <li>Withdraw consent where processing is based on consent</li>
        </ul>
        <p>To exercise these rights, contact us at <a href="mailto:info@lohamandigzb.online">info@lohamandigzb.online</a></p>
    </div>

    <div class="section">
        <h2>6. Data Retention</h2>
        <p>We retain your data for as long as your account is active or as needed to provide services. After account closure, we retain data for legal compliance (typically 7 years for transaction records) and then securely delete it.</p>
    </div>

    <div class="section">
        <h2>7. Cookies</h2>
        <p>We use cookies for:</p>
        <ul>
            <li><strong>Essential:</strong> Session management, authentication, security</li>
            <li><strong>Preferences:</strong> Language, display settings</li>
            <li><strong>Analytics:</strong> Understanding usage patterns (aggregated, anonymized)</li>
        </ul>
        <p>You can disable cookies in your browser settings, but some features may not work properly.</p>
    </div>

    <div class="section">
        <h2>8. Third-Party Links</h2>
        <p>Our platform may contain links to third-party websites (e.g., payment gateways, transporter services). We are not responsible for their privacy practices. Please review their policies.</p>
    </div>

    <div class="section">
        <h2>9. Children's Privacy</h2>
        <p>Our services are not directed to individuals under 18. We do not knowingly collect data from minors. If you believe a minor has provided data, contact us immediately.</p>
    </div>

    <div class="section">
        <h2>10. Changes to This Policy</h2>
        <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Last updated" date. Material changes will be notified via email or platform notification.</p>
    </div>

    <div class="section">
        <h2>11. Contact Us</h2>
        <p>For questions about this Privacy Policy or your data:</p>
        <div class="highlight">
            <strong>Email:</strong> <a href="mailto:info@lohamandigzb.online">info@lohamandigzb.online</a><br>
            <strong>Address:</strong> Loha Mandi, Ghaziabad, Uttar Pradesh, India
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
            <a href="<?= esc($base) ?>/register" style="display:block;margin-bottom:4px">Join Loha Mandi</a>
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
