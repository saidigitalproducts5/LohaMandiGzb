<?php
/**
 * Admin panel layout — sidebar + topbar + content.
 */
$nav = [
    'dashboard' => ['label' => 'Dashboard',     'icon' => '▦', 'url' => 'dashboard'],
    'users'     => ['label' => 'Users',         'icon' => '👤', 'url' => 'users'],
    'sellers'   => ['label' => 'Sellers',       'icon' => '🏪', 'url' => 'sellers'],
    'products'  => ['label' => 'Products',      'icon' => '⚙️', 'url' => 'products'],
    'categories'=> ['label' => 'Categories',    'icon' => '🗂', 'url' => 'categories'],
    'brands'    => ['label' => 'Brands',        'icon' => '🏷', 'url' => 'brands'],
    'ads'       => ['label' => 'Ads',           'icon' => '📣', 'url' => 'ads'],
    'locations' => ['label' => 'Locations',     'icon' => '📍', 'url' => 'locations'],
    'oversight' => ['label' => 'RFQs · Leads · Orders', 'icon' => '🔍', 'url' => 'oversight'],
    'reports'   => ['label' => 'Reports',       'icon' => '📊', 'url' => 'reports'],
    'ai'        => ['label' => 'AI Oversight',  'icon' => '🤖', 'url' => 'ai'],
];
$base = rtrim(base_url('admin'), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.png') ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title) ?> — LOHA MANDI Admin</title>
<style>
:root{--brand:#e02735;--brand-dark:#b81f2b;--ink:#1a1d23;--muted:#6b7280;--bg:#f3f4f6;--card:#fff;--line:#e5e7eb;--sidebar:#171a21}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;color:var(--ink);background:var(--bg);font-size:14px}
a{text-decoration:none;color:inherit}
.wrap{display:flex;min-height:100vh}
.sidebar{width:230px;background:var(--sidebar);color:#c9cdd6;flex-shrink:0;padding:20px 12px;position:fixed;inset:0 auto 0 0;overflow-y:auto}
.sidebar .brand{color:#fff;font-weight:800;font-size:17px;padding:4px 10px 18px;display:flex;align-items:center;gap:8px}
.sidebar .brand .dot{width:10px;height:10px;border-radius:3px;background:var(--brand)}
.sidebar nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;margin-bottom:2px;font-weight:600;font-size:13.5px;transition:.12s}
.sidebar nav a:hover{background:rgba(255,255,255,.06);color:#fff}
.sidebar nav a.active{background:var(--brand);color:#fff}
.sidebar nav a .ic{width:20px;text-align:center}
.sidebar .foot{margin-top:24px;padding:12px;border-top:1px solid rgba(255,255,255,.08);font-size:12px}
.main{margin-left:230px;flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:#fff;border-bottom:1px solid var(--line);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between}
.topbar h1{font-size:18px}
.topbar .user{display:flex;align-items:center;gap:12px}
.topbar .avatar{width:34px;height:34px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
.topbar .role{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.4px}
.content{padding:24px;flex:1}
.card{background:var(--card);border:1px solid var(--line);border-radius:12px;padding:20px;margin-bottom:20px}
.card h3{font-size:15px;margin-bottom:14px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
.stat{background:var(--card);border:1px solid var(--line);border-radius:12px;padding:18px}
.stat b{font-size:26px;display:block}
.stat span{color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.4px}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{text-align:left;padding:11px 12px;border-bottom:1px solid var(--line);vertical-align:middle}
th{background:#f9fafb;font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:var(--muted)}
tr:hover td{background:#fafafa}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700}
.badge.green{background:#ecfdf5;color:#047857}
.badge.red{background:#fef2f2;color:#b91c1c}
.badge.amber{background:#fffbeb;color:#b45309}
.badge.gray{background:#f3f4f6;color:#4b5563}
.badge.blue{background:#eff6ff;color:#1d4ed8}
.btn{display:inline-block;padding:7px 14px;border-radius:8px;font-weight:700;font-size:13px;border:0;cursor:pointer;transition:.12s;background:#eef0f3;color:#333}
.btn-primary{background:var(--brand);color:#fff}
.btn-primary:hover{background:var(--brand-dark)}
.btn-danger{background:#fef2f2;color:#b91c1c}
.btn-danger:hover{background:#fee2e2}
.btn-sm{padding:5px 10px;font-size:12px}
.btn-outline{background:#fff;border:1px solid var(--line);color:#374151}
input[type=text],input[type=email],input[type=password],input[type=number],input[type=date],select,textarea{width:100%;padding:9px 11px;border:1px solid var(--line);border-radius:8px;font-size:14px;outline:0;background:#fff}
input:focus,select:focus,textarea:focus{border-color:var(--brand)}
label{display:block;font-weight:600;font-size:13px;margin-bottom:5px}
.row{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:14px}
.alert{padding:11px 14px;border-radius:8px;margin-bottom:18px;font-weight:600;font-size:13.5px}
.alert.success{background:#ecfdf5;color:#047857;border:1px solid #a7f3d0}
.alert.error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
.empty{color:var(--muted);padding:20px;text-align:center}
.toolbar{display:flex;justify-content:space-between;gap:12px;margin-bottom:18px;flex-wrap:wrap}
.toolbar form{display:flex;gap:10px;flex-wrap:wrap}
.toolbar form input,.toolbar form select{min-width:0;flex:1 1 140px}
.pager{display:flex;gap:8px;align-items:center;margin-top:16px}
.pager a{padding:6px 12px;border:1px solid var(--line);border-radius:6px;background:#fff;font-weight:600;font-size:13px}
.pager a:hover{border-color:var(--brand);color:var(--brand)}
.muted{color:var(--muted)}
.mono{font-family:Consolas,monospace;font-size:12.5px}
.pill{padding:2px 8px;border-radius:10px;background:#f3f4f6;font-size:11.5px}
.hamburger{display:none;background:none;border:none;font-size:24px;cursor:pointer;padding:4px 8px;color:var(--ink)}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:99}
@media(max-width:768px){
.sidebar{transform:translateX(-100%);transition:transform .25s ease;z-index:100}
.sidebar.open{transform:translateX(0)}
.sidebar-overlay.open{display:block}
.main{margin-left:0!important}
.hamburger{display:block}
.topbar{padding:0 14px}
.content{padding:14px}
.grid{grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px}
.stat b{font-size:20px}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
.toolbar{flex-direction:column;gap:8px}
.row{grid-template-columns:1fr}
}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="wrap">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= esc(rtrim(base_url(), '/')) ?>/admin">
            <img src="<?= esc(rtrim(base_url(), '/')) ?>/images/logo-LohaMandi2.png" alt="LOHA MANDI" style="height:42px;width:auto">
            <span style="font-size:11px;font-weight:600;color:#8b90a0">ADMIN</span>
        </a>
        <nav>
            <?php foreach ($nav as $key => $item): ?>
                <a href="<?= esc($base . '/' . $item['url']) ?>" class="<?= $activeNav === $key ? 'active' : '' ?>">
                    <span class="ic"><?= $item['icon'] ?></span> <?= esc($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="foot">
            <a href="<?= esc(rtrim(base_url(), '/')) ?>" style="display:block;margin-bottom:8px;color:#8b90a0;font-weight:600;transition:.12s" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#8b90a0'">← Marketplace Home</a>
            LOHA MANDI<br>The Digital Marketplace of Loha Mandi, Ghaziabad
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:10px">
                <button class="hamburger" onclick="toggleSidebar()">&#9776;</button>
                <h1><?= esc($title) ?></h1>
            </div>
            <div class="user">
                <div class="role"><?= esc($adminRole) ?></div>
                <div class="avatar"><?= esc(strtoupper(substr((string)$adminName, 0, 1))) ?></div>
                <div>
                    <div style="font-weight:700"><?= esc($adminName) ?></div>
                    <a href="<?= esc($base) ?>/logout" style="color:var(--brand);font-size:12px;font-weight:600">Sign out</a>
                </div>
            </div>
        </div>
        <div class="content">
            <?php foreach (['success', 'error'] as $t): if (session('flash_' . $t)): ?>
                <div class="alert <?= $t ?>"><?= esc(session('flash_' . $t)) ?></div>
            <?php endif; endforeach; ?>
            <?= $content ?>
        </div>
    </div>
</div>
<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
document.querySelectorAll('.card>table').forEach(function(t){
    var w=document.createElement('div');w.className='table-wrap';t.parentNode.insertBefore(w,t);w.appendChild(t);
});
</script>
</body>
</html>
