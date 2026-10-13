<?php
/**
 * Terms of Service page
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
        <h1>Terms of Service</h1>
        <p>Last updated: September 2026</p>
    </div>
</div>

<div class="container content">
    <div class="section">
        <p class="muted">By accessing or using LOHA Mandi (the "Platform"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree, please do not use the Platform.</p>
    </div>

    <div class="section">
        <h2>1. Definitions</h2>
        <ul>
            <li><strong>"Platform"</strong> means LOHA Mandi (lohamandigzb.online), operated by VDS INFOTECH.</li>
            <li><strong>"Buyer"</strong> means a user who posts RFQs and purchases products.</li>
            <li><strong>"Seller"</strong> means a user who lists products and responds to RFQs.</li>
            <li><strong>"Transporter"</strong> means a user who provides logistics services.</li>
            <li><strong>"RFQ"</strong> means Request for Quotation posted by a buyer.</li>
            <li><strong>"Content"</strong> means all text, images, data, and materials on the Platform.</li>
        </ul>
    </div>

    <div class="section">
        <h2>2. Eligibility</h2>
        <p>You must be at least 18 years old and legally capable of entering into contracts. By registering, you represent that you are a legitimate business entity or individual authorized to conduct business in India.</p>
    </div>

    <div class="section">
        <h2>3. Account Registration</h2>
        <ul>
            <li>You must provide accurate, current, and complete information.</li>
            <li>You are responsible for maintaining the confidentiality of your credentials.</li>
            <li>You must notify us immediately of any unauthorized access to your account.</li>
            <li>One entity may only maintain one seller account unless otherwise approved.</li>
            <li>We reserve the right to verify business details (GSTIN, PAN, address) and reject or suspend accounts with false information.</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. Platform Services</h2>
        <h3>For Buyers</h3>
        <ul>
            <li>Post RFQs specifying product requirements, quantity, and location.</li>
            <li>Receive quotations from verified sellers.</li>
            <li>Compare offers and negotiate terms directly with sellers.</li>
            <li>Place orders and make payments through integrated payment options.</li>
        </ul>
        <h3>For Sellers</h3>
        <ul>
            <li>Create and manage product listings with pricing, specifications, and images.</li>
            <li>Receive RFQs relevant to your product categories and location.</li>
            <li>Submit quotations and manage orders.</li>
            <li>Access seller dashboard with analytics and lead management.</li>
        </ul>
        <h3>For Transporters</h3>
        <ul>
            <li>Register vehicles and offer freight services.</li>
            <li>Receive transport requests from buyers and sellers.</li>
            <li>Manage dispatch, tracking, and delivery confirmation.</li>
        </ul>
    </div>

    <div class="section">
        <h2>5. Seller Subscription & Fees</h2>
        <p>Basic registration is free. Premium features (featured listings, priority RFQ access, enhanced analytics) require a paid subscription. Fees are non-refundable except as required by law. We may modify fees with 30 days' notice.</p>
    </div>

    <div class="section">
        <h2>6. User Conduct</h2>
        <p>You agree NOT to:</p>
        <ul>
            <li>Post false, misleading, or fraudulent product information.</li>
            <li>Impersonate another business or individual.</li>
            <li>Spam users with unsolicited messages or irrelevant RFQs.</li>
            <li>Attempt to bypass platform fees by conducting transactions off-platform.</li>
            <li>Interfere with platform security, scrape data, or overload systems.</li>
            <li>List prohibited items (weapons, drugs, counterfeit goods, stolen property).</li>
            <li>Harass, threaten, or abuse other users or staff.</li>
        </ul>
        <div class="highlight">
            <strong>Violation may result in immediate account suspension, removal of listings, and legal action.</strong>
        </div>
    </div>

    <div class="section">
        <h2>7. Transactions Between Users</h2>
        <ul>
            <li>LOHA Mandi is a marketplace facilitator, not a party to transactions.</li>
            <li>All terms (price, quantity, delivery, warranty, returns) are agreed directly between buyer and seller.</li>
            <li>We do not guarantee product quality, seller reliability, or transaction completion.</li>
            <li>Payment processing is handled by third-party providers; their terms apply.</li>
            <li>Disputes should be resolved between parties; we may offer mediation but are not obligated to.</li>
        </ul>
    </div>

    <div class="section">
        <h2>8. Intellectual Property</h2>
        <ul>
            <li>All Platform content (design, code, logos, trademarks) is owned by VDS INFOTECH or licensed to us.</li>
            <li>You retain ownership of your product listings and business content.</li>
            <li>By posting content, you grant us a non-exclusive, royalty-free license to display, promote, and distribute it on the Platform.</li>
            <li>You may not reproduce, distribute, or create derivative works from Platform content without permission.</li>
        </ul>
    </div>

    <div class="section">
        <h2>9. Disclaimers</h2>
        <p>THE PLATFORM IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, NON-INFRINGEMENT, OR UNINTERRUPTED SERVICE.</p>
        <p>We do not warrant that the Platform will be error-free, secure, or free from viruses.</p>
    </div>

    <div class="section">
        <h2>10. Limitation of Liability</h2>
        <p>TO THE MAXIMUM EXTENT PERMITTED BY LAW, LOHA Mandi AND VDS INFOTECH SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING LOST PROFITS, DATA, OR BUSINESS OPPORTUNITIES, ARISING FROM YOUR USE OF THE PLATFORM.</p>
        <p>Our total liability for any claim shall not exceed the fees you paid to us in the 12 months preceding the claim (or ₹10,000, whichever is less).</p>
    </div>

    <div class="section">
        <h2>11. Indemnification</h2>
        <p>You agree to indemnify and hold harmless LOHA Mandi, VDS INFOTECH, and their officers, employees, and agents from any claims, damages, losses, or expenses (including legal fees) arising from your use of the Platform, violation of these Terms, or infringement of third-party rights.</p>
    </div>

    <div class="section">
        <h2>12. Termination</h2>
        <p>We may suspend or terminate your account at any time for violation of these Terms, fraudulent activity, or extended inactivity (12+ months). Upon termination, your access ceases, but transaction records are retained for legal compliance. You may request account closure at any time by contacting support.</p>
    </div>

    <div class="section">
        <h2>13. Governing Law & Jurisdiction</h2>
        <p>These Terms are governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction of the courts in Ghaziabad, Uttar Pradesh.</p>
    </div>

    <div class="section">
        <h2>14. Changes to Terms</h2>
        <p>We may modify these Terms at any time. Changes will be posted on this page with an updated "Last updated" date. Continued use after changes constitutes acceptance. Material changes will be notified via email or platform notification.</p>
    </div>

    <div class="section">
        <h2>15. Contact Us</h2>
        <p>For questions about these Terms:</p>
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
            <a href="<?= esc($base) ?>/privacy-policy" style="display:block;margin-bottom:4px">Privacy Policy</a>
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
