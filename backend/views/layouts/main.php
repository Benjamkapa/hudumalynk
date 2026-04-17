<?php
/** @var yii\web\View $this */
/** @var string $content */
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Provider;

AppAsset::register($this);

// Minimal wrapper when rendering via AJAX to avoid nested html tags.
if (Yii::$app->request->isAjax) {
    echo "<div class='hl-ajax-content'>" . $content . "</div>";
    return;
}

$user      = Yii::$app->user->identity;
$isAdmin   = $user && $user->isAdmin();
$isProvider = $user && $user->isProvider();
$provider  = $isProvider ? Provider::findOne(['user_id' => $user->id]) : null;
$unreadCount = $user ? $user->getUnreadNotificationsCount() : 0;
$firstName = explode(' ', $user->getFullName())[0];
$initials  = strtoupper(substr($user->getFullName(), 0, 2));

$navGroups = $isAdmin ? [
    'Overview' => [
        ['Dashboard',      'grid',       '/admin/dashboard'],
        ['Analytics',      'chart-bar',  '/admin/analytics'],
        ['Nairobi Map',    'map-pin',    '/admin/map',         'Live',    'teal'],
    ],
    'Marketplace' => [
        ['Vendors',        'users',      '/admin/vendors',     '24 new',  'purple'],
        ['Listings',       'box',        '/admin/listings'],
        ['Orders',         'cart',       '/admin/orders',      '7 pend.', 'amber'],
        ['Categories',     'tag',        '/admin/categories'],
        ['Reviews',        'star',       '/admin/reviews'],
    ],
    'Finance' => [
        ['Earnings & GMV', 'dollar',     '/admin/earnings'],
        ['Subscriptions',  'card',       '/admin/subscriptions'],
        ['M-Pesa Payouts', 'activity',   '/admin/payouts'],
        ['Invoices',       'grid-3',     '/admin/invoices'],
    ],
    'People' => [
        ['Users',          'user',       '/admin/users'],
        ['Messages',       'chat',       '/admin/messages',    '3',       'purple'],
        ['Notifications',  'bell',       '/admin/notifications', $unreadCount > 0 ? $unreadCount : null, 'amber'],
    ],
    'System' => [
        ['Settings',       'settings',   '/admin/settings'],
        ['View Site',      'external',   null,                 null,      null,    true],
    ],
] : [
    'Overview' => [
        ['Dashboard',      'grid',       '/provider/dashboard'],
        ['Analytics',      'chart-bar',  '/provider/analytics'],
    ],
    'My Store' => [
        ['My Listings',    'box',        '/provider/listings'],
        ['Orders',         'cart',       '/provider/orders',   '2',       'amber'],
        ['Reviews',        'star',       '/provider/reviews'],
    ],
    'Finance' => [
        ['Earnings',       'dollar',     '/provider/earnings'],
        ['Subscription',   'card',       '/provider/subscription'],
        ['Payouts',        'activity',   '/provider/payouts'],
    ],
    'Account' => [
        ['Profile',        'user',       '/provider/profile'],
        ['Messages',       'chat',       '/provider/messages', '1',       'purple'],
        ['Notifications',  'bell',       '/provider/notifications', $unreadCount > 0 ? $unreadCount : null, 'amber'],
        ['Settings',       'settings',   '/provider/settings'],
    ],
];

$currentUrl = Yii::$app->request->url;

$svgPaths = [
    'grid'      => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    'chart-bar' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
    'map-pin'   => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
    'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    'box'       => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>',
    'cart'      => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
    'tag'       => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
    'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
    'dollar'    => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    'card'      => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
    'activity'  => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
    'grid-3'    => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/>',
    'user'      => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'chat'      => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    'bell'      => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
    'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>',
    'external'  => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>',
    'search'    => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
    'chevron-r' => '<polyline points="9 18 15 12 9 6"/>',
    'chevron-d' => '<polyline points="6 9 12 15 18 9"/>',
    'menu'      => '<line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>',
    'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
    'dots-v'    => '<circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>',
    'x'         => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
];

function hlSvg(array $p, string $n, string $extra = ''): string {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' . $extra . '>' . ($p[$n] ?? '') . '</svg>';
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= Html::encode($this->title ? $this->title . ' — HudumaLynk' : 'Dashboard — HudumaLynk') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <?= Html::csrfMetaTags() ?>
  <?php $this->head() ?>
  <style>
    :root {
      /* Aesthetic: Rich Soothing Blue-Gray Blend */
      --acc: #1D4ED8; /* Deeper vibrant blue */
      --acc2: #3B82F6; 
      --acc-pale: rgba(59, 130, 246, 0.12);
      --teal: #10B981; --teal-pale: rgba(16, 185, 129, 0.12);
      --amber: #F59E0B; --amber-pale: rgba(245, 158, 11, 0.14);
      --rose: #EF4444; --rose-pale: rgba(239, 68, 68, 0.12);
      
      --sb-w: 245px; --tb-h: 60px;
      --font: 'Plus Jakarta Sans', system-ui, sans-serif;
      --r-sm: 4px; --r-md: 6px; --r-lg: 10px;
      
      --bg: #F0F4F8; /* Soft blue-tinted background to cut brightness */
      --bg2: #FFFFFF; /* White panels remain crisp */
      --bg3: #E2E8F0; /* Soft hover state */
      
      --text1: #0F172A; /* Slate 900 for absolute contrast without staring black */
      --text2: #475569; /* Slate 600 */
      --text3: #64748B; /* Slate 500 */
      
      --border: #E2E8F0; /* Slate 200 */
      
      --sb-bg: #0B1120; /* Deepest Navy sidebar */
      --sb-text: rgba(255, 255, 255, 0.6);
      --sb-hover: rgba(255, 255, 255, 0.08); 
      --sb-act: rgba(59, 130, 246, 0.2);
      --tb-bg: #FFFFFF;
    }
    
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; }
    a { text-decoration: none; }
    body { font-family: var(--font); background: var(--bg); color: var(--text1); }

    .hl-shell { display: flex; height: 100vh; overflow: hidden; }

    /* ── Sidebar ── */
    .hl-sb {
      width: var(--sb-w); min-width: var(--sb-w); height: 100vh;
      background: var(--sb-bg); display: flex; flex-direction: column;
      overflow-y: auto; scrollbar-width: none;
      z-index: 200; flex-shrink: 0; transition: transform .22s ease;
    }
    .hl-sb::-webkit-scrollbar { display: none; }
    .hl-sb-logo { display: flex; align-items: center; gap: 10px; padding: 20px 16px 16px; border-bottom: 1px solid rgba(255,255,255,.06); flex-shrink: 0; }
    .hl-logo-mark { width: 32px; height: 32px; background: var(--acc); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hl-logo-mark svg { width: 16px; height: 16px; stroke: #fff; }
    .hl-logo-name { font-size: 14px; font-weight: 800; color: #fff; letter-spacing: -.02em; line-height: 1; }
    .hl-logo-sub  { font-size: 8.5px; color: var(--sb-text); letter-spacing: .12em; text-transform: uppercase; margin-top: 2px; }
    .hl-nav-sec   { padding: 14px 10px 2px; }
    .hl-nav-lbl   { font-size: 9px; font-weight: 700; color: var(--sb-text); letter-spacing: .14em; text-transform: uppercase; padding: 0 8px; margin-bottom: 3px; }
    .hl-nav-item  { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: var(--r-md); cursor: pointer; color: var(--sb-text); font-size: 13px; font-weight: 400; position: relative; margin-bottom: 1px; transition: background .13s, color .13s; }
    .hl-nav-item:hover  { background: var(--sb-hover); color: rgba(255,255,255,.9); }
    .hl-nav-item.active { background: var(--sb-act); color: #fff; font-weight: 600; }
    .hl-nav-item.active::before { content: ''; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px; background: var(--acc2); border-radius: 0 3px 3px 0; }
    .hl-nav-item svg { width: 15px; height: 15px; flex-shrink: 0; opacity: .8; }
    .hl-nav-item:hover svg, .hl-nav-item.active svg { opacity: 1; }
    .hl-nav-badge { margin-left: auto; color: #fff; font-size: 9.5px; font-weight: 700; padding: 2px 6px; border-radius: var(--r-sm); line-height: 1.5; }
    .hl-nav-badge.purple { background: var(--acc); color: #FFF; }
    .hl-nav-badge.amber  { background: var(--amber); color: #FFF; }
    .hl-nav-badge.teal   { background: var(--teal); color: #FFF; }
    
    .hl-sb-foot   { margin-top: auto; padding: 10px 10px 14px; border-top: 1px solid rgba(255,255,255,.05); flex-shrink: 0; position: relative; }

    /* Profile popups */
    .hl-sb-popup  { display: none; position: absolute; bottom: 68px; left: 12px; right: 12px; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-md); overflow: hidden; z-index: 50; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .hl-sb-popup.open { display: block; }
    .hl-pop-item  { display: flex; align-items: center; gap: 10px; padding: 10px 14px; font-size: 12.5px; color: var(--text1); cursor: pointer; transition: background .12s; }
    .hl-pop-item:hover   { background: var(--bg3); }
    .hl-pop-item svg     { width: 14px; height: 14px; color: var(--text2); flex-shrink: 0; }
    .hl-pop-item.danger  { color: var(--rose); }
    .hl-pop-item.danger svg { color: var(--rose); }
    .hl-pop-divider      { height: 1px; background: var(--border); }
    .hl-sb-user   { display: flex; align-items: center; gap: 9px; padding: 9px 10px; border-radius: var(--r-md); cursor: pointer; transition: background .13s; }
    .hl-sb-user:hover { background: var(--sb-hover); }
    .hl-avatar    { width: 32px; height: 32px; border-radius: var(--r-md); background: var(--acc); display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 800; color: #fff; flex-shrink: 0; }
    .hl-avatar-sm { width: 25px; height: 25px; border-radius: var(--r-sm); background: var(--acc); display: flex; align-items: center; justify-content: center; font-size: 9.5px; font-weight: 800; color: #fff; flex-shrink: 0; }
    .hl-sb-uname  { font-size: 12.5px; font-weight: 600; color: #fff; line-height: 1.2; }
    .hl-sb-urole  { font-size: 9.5px; color: var(--sb-text); text-transform: capitalize; }
    .hl-sb-dots   { width: 14px; height: 14px; margin-left: auto; color: var(--sb-text); }

    /* ── Main ── */
    .hl-main { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden; }

    /* ── Topbar ── */
    .hl-topbar { height: var(--tb-h); background: var(--tb-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 20px; gap: 10px; flex-shrink: 0; }
    .hl-menu-btn { display: none; width: 33px; height: 33px; border: 1px solid var(--border); border-radius: var(--r-sm); background: transparent; cursor: pointer; align-items: center; justify-content: center; color: var(--text2); }
    .hl-menu-btn svg { width: 16px; height: 16px; }
    .hl-bc { font-size: 12.5px; font-weight: 600; color: var(--text1); letter-spacing: -.01em; display: flex; align-items: center; gap: 5px; }
    .hl-bc svg { width: 12px; height: 12px; color: var(--text3); }
    .hl-bc span { color: var(--text3); font-weight: 400; }
    .hl-search-wrap { display: flex; align-items: center; gap: 7px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 0 11px; height: 33px; max-width: 300px; flex: 1; margin-left: 10px; }
    .hl-search-wrap svg { width: 13px; height: 13px; color: var(--text3); flex-shrink: 0; }
    .hl-search-input { background: none; border: none; outline: none; font-size: 12px; color: var(--text1); font-family: var(--font); width: 100%; }
    .hl-search-input::placeholder { color: var(--text3); }
    .hl-tb-right { margin-left: auto; display: flex; align-items: center; gap: 6px; }
    .hl-loc-pill { display: inline-flex; align-items: center; gap: 4px; background: var(--acc-pale); color: var(--acc); border-radius: var(--r-sm); padding: 5px 10px; font-size: 10.5px; font-weight: 700; }
    .hl-loc-pill svg { width: 10px; height: 10px; }
    .hl-ib { width: 33px; height: 33px; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--bg2); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text2); transition: background .13s, color .13s; position: relative; }
    .hl-ib:hover { background: var(--bg3); color: var(--text1); }
    .hl-ib svg { width: 15px; height: 15px; }
    .hl-notif-dot { position: absolute; top: 6px; right: 6px; width: 6px; height: 6px; background: var(--rose); border-radius: 50%; border: 1.5px solid var(--tb-bg); }

    .hl-tb-user { display: none; align-items: center; gap: 7px; padding: 4px 10px 4px 6px; border: 1px solid var(--border); border-radius: var(--r-sm); cursor: pointer; background: var(--bg2); transition: background .13s; margin-left: 2px; position: relative; }
    .hl-tb-user:hover { background: var(--bg3); }
    .hl-tb-uname { font-size: 11.5px; font-weight: 600; color: var(--text1); }
    .hl-tb-user > svg { width: 12px; height: 12px; color: var(--text2); }
    .hl-tb-popup { display: none; position: absolute; top: 44px; right: 0; width: 200px; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-md); overflow: hidden; z-index: 50; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .hl-tb-popup.open { display: block; }

    /* ── Content ── */
    .hl-content-wrap { flex: 1; overflow-y: auto; padding: 20px 22px; scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
    .hl-content-wrap::-webkit-scrollbar { width: 5px; }
    .hl-content-wrap::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
    .hl-pg-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
    .hl-pg-title { font-size: 22px; font-weight: 800; color: var(--text1); letter-spacing: -.04em; line-height: 1; }
    .hl-pg-sub   { font-size: 11.5px; color: var(--text3); margin-top: 4px; display: flex; align-items: center; gap: 4px; }
    .hl-pg-sub svg { width: 11px; height: 11px; }
    .hl-btn-p { display: inline-flex; align-items: center; gap: 6px; background: var(--acc); color: #fff; border: none; border-radius: var(--r-sm); padding: 8px 15px; font-size: 12px; font-weight: 600; font-family: var(--font); cursor: pointer; transition: background .13s; text-decoration: none; }
    .hl-btn-p:hover { background: var(--acc2); color: #fff; }
    .hl-btn-p svg { width: 12px; height: 12px; }
    .hl-btn-g { display: inline-flex; align-items: center; gap: 6px; background: var(--bg2); color: var(--text2); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 8px 13px; font-size: 12px; font-weight: 600; font-family: var(--font); cursor: pointer; transition: background .13s, color .13s; text-decoration: none; }
    .hl-btn-g:hover { background: var(--bg3); color: var(--text1); }
    .hl-btn-g svg { width: 12px; height: 12px; }

    /* Flash messages */
    .hl-flash { padding: 11px 16px; border-radius: var(--r-sm); font-size: 13px; margin-bottom: 14px; cursor: pointer; transition: opacity .2s; }
    .hl-flash.success { background: var(--teal-pale); color: var(--teal); border: 1px solid rgba(42,157,143,.25); }
    .hl-flash.danger  { background: var(--rose-pale); color: var(--rose); border: 1px solid rgba(231,111,81,.25); }
    .hl-flash.warning { background: var(--amber-pale); color: #9A5A00; border: 1px solid rgba(226,149,120,.35); }

    /* Cards & tables */
    .hl-card       { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-md); overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .hl-card-head  { display: flex; align-items: center; justify-content: space-between; padding: 13px 15px 11px; border-bottom: 1px solid var(--border); }
    .hl-card-title { font-size: 13px; font-weight: 700; color: var(--text1); letter-spacing: -.01em; }
    .hl-card-link  { font-size: 11px; color: var(--acc); font-weight: 600; cursor: pointer; }
    .hl-card-link:hover { text-decoration: underline; }
    .hl-card-body  { padding: 16px; }
    
    .hl-badge      { display: inline-block; padding: 3px 8px; border-radius: var(--r-sm); font-size: 10px; font-weight: 700; }
    .hl-badge.paid { background: var(--teal-pale); color: var(--teal); }
    .hl-badge.pend { background: var(--amber-pale); color: #9A5A00; }
    .hl-badge.new  { background: var(--acc-pale); color: var(--acc); }

    .hl-tbl             { width: 100%; border-collapse: collapse; font-size: 12px; }
    .hl-tbl th          { text-align: left; padding: 9px 15px 8px; font-size: 10px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--text3); background: var(--bg); border-bottom: 1px solid var(--border); }
    .hl-tbl td          { padding: 9px 15px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .hl-tbl tr:last-child td { border-bottom: none; }
    .hl-tbl tr:hover td { background: var(--bg); }

    .hl-scard      { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-md); padding: 14px 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }

    /* ── Offcanvas Modal ── */
    .hl-offcanvas-backdrop {
      display: none; position: fixed; inset: 0; background: rgba(28, 38, 49, 0.4); 
      z-index: 999; opacity: 0; transition: opacity 0.2s ease;
    }
    .hl-offcanvas-backdrop.open { display: block; opacity: 1; backdrop-filter: blur(2px); }
    
    .hl-offcanvas {
      position: fixed; top: 0; right: 0; bottom: 0; width: 600px;
      max-width: 100%; background: var(--bg); z-index: 1000;
      transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex; flex-direction: column;
      box-shadow: -4px 0 15px rgba(0,0,0,0.05);
      border-left: 1px solid var(--border);
    }
    .hl-offcanvas.open { transform: translateX(0); }
    
    .hl-offcanvas-head {
      display: flex; align-items: center; justify-content: space-between;
      height: 58px; padding: 0 20px; border-bottom: 1px solid var(--border);
      background: var(--bg2); flex-shrink: 0;
    }
    .hl-offcanvas-title { font-size: 14px; font-weight: 700; color: var(--text1); }
    .hl-offcanvas-close {
      width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
      cursor: pointer; border-radius: var(--r-sm); color: var(--text2); background: var(--bg);
      border: 1px solid var(--border); transition: all 0.15s;
    }
    .hl-offcanvas-close:hover { background: var(--rose-pale); color: var(--rose); border-color: transparent; }
    .hl-offcanvas-close svg { width: 16px; height: 16px; }
    
    .hl-offcanvas-body {
      flex: 1; overflow-y: auto; padding: 20px;
    }

    .hl-loader { border: 2px solid var(--border); border-top: 2px solid var(--acc); border-radius: 50%; width: 24px; height: 24px; animation: hl-spin 0.8s linear infinite; margin: 40px auto; }
    @keyframes hl-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Mobile overlay */
    .hl-overlay { display: none; position: fixed; inset: 0; background: rgba(28,38,49,0.4); z-index: 199; }

    @media (max-width: 900px) { .hl-stats-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (max-width: 768px) {
      .hl-sb { position: fixed; top: 0; left: 0; height: 100vh; transform: translateX(-100%); }
      .hl-sb.open { transform: translateX(0); }
      .hl-overlay.open { display: block; }
      .hl-menu-btn { display: flex; }
      .hl-search-wrap { display: none; }
      .hl-loc-pill { display: none; }
      .hl-tb-user  { display: flex; }
      .hl-content-wrap { padding: 16px; }
      .hl-offcanvas { width: 100%; border-left: none; }
    }
    @media (max-width: 480px) { .hl-tb-uname { display: none; } }
  </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="hl-overlay" id="hlOverlay"></div>

<!-- Offcanvas Drawer -->
<div class="hl-offcanvas-backdrop" id="hlOcBackdrop"></div>
<div class="hl-offcanvas" id="hlOffcanvas">
  <div class="hl-offcanvas-head">
    <div class="hl-offcanvas-title" id="hlOcTitle">Loading...</div>
    <div class="hl-offcanvas-close" id="hlOcClose" aria-label="Close panel">
      <?= hlSvg($svgPaths, 'x') ?>
    </div>
  </div>
  <div class="hl-offcanvas-body" id="hlOcBody"></div>
</div>

<div class="hl-shell">

  <aside class="hl-sb" id="hlSidebar">
    <div class="hl-sb-logo">
      <?php if ($isAdmin): ?>
        <!-- Admin: Show HudumaLynk logo -->
        <div class="hl-logo-mark"><?= hlSvg($svgPaths, 'box') ?></div>
        <div>
          <div class="hl-logo-name">HudumaLynk</div>
          <div class="hl-logo-sub">Nairobi Marketplace</div>
        </div>
      <?php elseif ($isProvider && $provider): ?>
        <!-- Provider: Show business logo -->
        <?php if ($provider->logo): ?>
          <img src="<?= Html::encode(rtrim(Yii::$app->params['frontendUrl'], '/') . '/uploads/' . $provider->logo) ?>" alt="<?= Html::encode($provider->business_name) ?>" style="width:40px;height:40px;border-radius:4px;object-fit:cover;flex-shrink:0;" onerror="this.style.display='none'">
        <?php else: ?>
          <div class="hl-logo-mark" style="width:40px;height:40px;font-size:16px;"><?= strtoupper(substr($provider->business_name, 0, 2)) ?></div>
        <?php endif; ?>
        <div>
          <div class="hl-logo-name" style="font-size:13px;"><?= Html::encode(substr($provider->business_name, 0, 20)) ?></div>
          <div class="hl-logo-sub"><?= Html::encode($provider->city ?? 'Provider Portal') ?></div>
        </div>
      <?php endif; ?>
    </div>

    <?php foreach ($navGroups as $groupName => $items): ?>
      <div class="hl-nav-sec">
        <div class="hl-nav-lbl"><?= Html::encode($groupName) ?></div>
        <?php foreach ($items as $item):
          [$title, $icon, $route] = $item;
          $badgeText  = $item[3] ?? null;
          $badgeColor = $item[4] ?? 'purple';
          $isExternal = $item[5] ?? false;
          $href       = $isExternal ? Html::encode(Yii::$app->params['frontendUrl'] ?? '/') : Url::to([$route]);
          $extAttr    = $isExternal ? 'target="_blank" rel="noopener"' : '';
          $isActive   = !$isExternal && (str_starts_with($currentUrl, $route) || $currentUrl === $route);
        ?>
          <a href="<?= $href ?>" <?= $extAttr ?> class="hl-nav-item <?= $isActive ? 'active' : '' ?>">
            <?= hlSvg($svgPaths, $icon) ?>
            <span><?= Html::encode($title) ?></span>
            <?php if ($badgeText): ?>
              <span class="hl-nav-badge <?= Html::encode($badgeColor) ?>"><?= Html::encode($badgeText) ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="hl-sb-foot">
      <div class="hl-sb-popup" id="hlSbPopup">
        <a href="<?= Url::to([$isAdmin ? '/admin/profile' : '/provider/profile']) ?>" class="hl-pop-item offcanvas-link">
          <?= hlSvg($svgPaths, 'user') ?> View profile
        </a>
        <a href="<?= Url::to([$isAdmin ? '/admin/settings' : '/provider/settings']) ?>" class="hl-pop-item offcanvas-link">
          <?= hlSvg($svgPaths, 'settings') ?> Account settings
        </a>
        <div class="hl-pop-divider"></div>
        <a href="<?= Url::to(['/site/logout']) ?>" data-method="post" class="hl-pop-item danger">
          <?= hlSvg($svgPaths, 'logout') ?> Sign out
        </a>
      </div>
      <div class="hl-sb-user" id="hlSbUser">
        <div class="hl-avatar"><?= Html::encode($initials) ?></div>
        <div>
          <div class="hl-sb-uname"><?= Html::encode($user->getFullName()) ?></div>
          <div class="hl-sb-urole"><?= Html::encode($user->role) ?></div>
        </div>
        <?= hlSvg($svgPaths, 'dots-v', 'class="hl-sb-dots"') ?>
      </div>
    </div>
  </aside>

  <div class="hl-main">
    <header class="hl-topbar">
      <button class="hl-menu-btn" id="hlMenuBtn" aria-label="Open menu">
        <?= hlSvg($svgPaths, 'menu') ?>
      </button>
      <div class="hl-bc">
        <?= hlSvg($svgPaths, 'chevron-r') ?>
        <span>Platform</span> / <?= Html::encode($this->title ?? 'Panel') ?>
      </div>
      <div class="hl-search-wrap">
        <?= hlSvg($svgPaths, 'search') ?>
        <input class="hl-search-input" type="text" placeholder="Search vendors, orders, listings…">
      </div>
      <div class="hl-tb-right">
        <div class="hl-loc-pill">
          <?= hlSvg($svgPaths, 'map-pin') ?> Nairobi, KE
        </div>
        <div class="hl-ib dropdown-trigger" id="hlNotifBtn" title="Notifications" tabindex="0">
          <?= hlSvg($svgPaths, 'bell') ?>
          <?php if ($unreadCount > 0): ?><div class="hl-notif-dot"></div><?php endif; ?>
          <div class="hl-tb-popup" id="hlNotifPopup" style="width:280px;right:-10px;">
            <div style="padding:12px 14px;border-bottom:1px solid var(--border);font-size:12px;font-weight:700;color:var(--text1);display:flex;justify-content:space-between;">
              Notifications <span style="color:var(--acc);font-size:10px;cursor:pointer;" onclick="markAllBackendNotifsRead()">Mark read</span>
            </div>
            <div style="padding:18px 10px;text-align:center;font-size:11.5px;color:var(--text3);">No new alerts at this time.</div>
            <div style="border-top:1px solid var(--border);padding:10px;">
              <a href="<?= Url::to([$isAdmin ? '/admin/notifications' : '/provider/notifications']) ?>" style="display:block;text-align:center;font-size:11.5px;color:var(--acc);font-weight:600;text-decoration:none;">View all notifications</a>
            </div>
          </div>
        </div>
          </div>
        </div>
        <div class="hl-tb-user" id="hlTbUser">
          <div class="hl-avatar-sm"><?= Html::encode($initials) ?></div>
          <span class="hl-tb-uname"><?= Html::encode($firstName) ?></span>
          <?= hlSvg($svgPaths, 'chevron-d') ?>
          <div class="hl-tb-popup" id="hlTbPopup">
            <a href="<?= Url::to([$isAdmin ? '/admin/profile' : '/provider/profile']) ?>" class="hl-pop-item offcanvas-link">
              <?= hlSvg($svgPaths, 'user') ?> View profile
            </a>
            <a href="<?= Url::to([$isAdmin ? '/admin/settings' : '/provider/settings']) ?>" class="hl-pop-item offcanvas-link">
              <?= hlSvg($svgPaths, 'settings') ?> Account settings
            </a>
            <div class="hl-pop-divider"></div>
            <a href="<?= Url::to(['/site/logout']) ?>" data-method="post" class="hl-pop-item danger">
              <?= hlSvg($svgPaths, 'logout') ?> Sign out
            </a>
          </div>
        </div>
      </div>
    </header>

    <div class="hl-content-wrap">
      <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msgs): ?>
        <?php foreach ((array)$msgs as $msg): ?>
          <div class="hl-flash <?= Html::encode(in_array($type, ['success','danger','warning']) ? $type : 'success') ?>">
            <?= Html::encode($msg) ?>
          </div>
        <?php endforeach; ?>
      <?php endforeach; ?>

      <?= $content ?>
    </div>
  </div>

</div>

<script>
(function(){
  // --- Standard Layout Logic ---
  var sidebar=document.getElementById('hlSidebar'),overlay=document.getElementById('hlOverlay'),menuBtn=document.getElementById('hlMenuBtn');
  var sbUser=document.getElementById('hlSbUser'),sbPopup=document.getElementById('hlSbPopup');
  var tbUser=document.getElementById('hlTbUser'),tbPopup=document.getElementById('hlTbPopup');
  var notifBtn=document.getElementById('hlNotifBtn'),notifPopup=document.getElementById('hlNotifPopup');
  function openSb(){sidebar.classList.add('open');overlay.classList.add('open');document.body.style.overflow='hidden'}
  function closeSb(){sidebar.classList.remove('open');overlay.classList.remove('open');document.body.style.overflow=''}
  menuBtn&&menuBtn.addEventListener('click',openSb);
  overlay&&overlay.addEventListener('click',closeSb);
  sidebar&&sidebar.querySelectorAll('.hl-nav-item').forEach(function(el){el.addEventListener('click',function(){if(window.innerWidth<=768)closeSb()})});
  function closeAll(){
      sbPopup&&sbPopup.classList.remove('open');
      tbPopup&&tbPopup.classList.remove('open');
      notifPopup&&notifPopup.classList.remove('open');
  }
  sbUser&&sbUser.addEventListener('click',function(e){e.stopPropagation();var o=sbPopup.classList.contains('open');closeAll();if(!o)sbPopup.classList.add('open')});
  tbUser&&tbUser.addEventListener('click',function(e){e.stopPropagation();var o=tbPopup.classList.contains('open');closeAll();if(!o)tbPopup.classList.add('open')});
  notifBtn&&notifBtn.addEventListener('click',function(e){e.stopPropagation();var o=notifPopup.classList.contains('open');closeAll();if(!o)notifPopup.classList.add('open')});
  document.addEventListener('click',closeAll);
  sbPopup&&sbPopup.addEventListener('click',function(e){e.stopPropagation()});
  tbPopup&&tbPopup.addEventListener('click',function(e){e.stopPropagation()});
  notifPopup&&notifPopup.addEventListener('click',function(e){e.stopPropagation()});
  document.querySelectorAll('.hl-flash').forEach(function(el){el.addEventListener('click',function(){el.style.opacity='0';setTimeout(function(){el.remove()},220)})});

  // --- Offcanvas Intercept Logic ---
  var ocCanvas   = document.getElementById('hlOffcanvas');
  var ocBackdrop = document.getElementById('hlOcBackdrop');
  var ocBody     = document.getElementById('hlOcBody');
  var ocTitle    = document.getElementById('hlOcTitle');
  var ocClose    = document.getElementById('hlOcClose');

  function closeOc() {
      ocCanvas.classList.remove('open');
      ocBackdrop.classList.remove('open');
      setTimeout(function(){ ocBody.innerHTML = ''; }, 300);
      document.body.style.overflow = '';
  }

  function openOc(url, title) {
      ocTitle.textContent = title || 'Loading...';
      ocBody.innerHTML = '<div class="hl-loader"></div>';
      ocCanvas.classList.add('open');
      ocBackdrop.classList.add('open');
      document.body.style.overflow = 'hidden';

      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(r => r.text())
          .then(html => {
              ocBody.innerHTML = html;
              // Attempt to extract title if a .hl-pg-title exists
              var tmp = document.createElement('div');
              tmp.innerHTML = html;
              var pgTitleElement = tmp.querySelector('.hl-pg-title');
              if (pgTitleElement) { ocTitle.textContent = pgTitleElement.textContent; }
              else { ocTitle.textContent = 'Details'; }
              
              // Hide any local back buttons or page headers rendered by the view itself to clean up
              var duplicateHead = ocBody.querySelector('.hl-pg-head');
              if(duplicateHead) duplicateHead.style.display = 'none';
          })
          .catch(e => {
              ocBody.innerHTML = '<div style="color:red;">Error loading content. Please try again.</div>';
          });
  }

  ocBackdrop.addEventListener('click', closeOc);
  ocClose.addEventListener('click', closeOc);

  // Global Interceptor for dynamic UI
  document.addEventListener('click', function(e) {
      if (e.target.closest('.hl-offcanvas')) return; // let items inside act naturally, except if specified
      
      var link = e.target.closest('a');
      if (!link || !link.href || link.target === '_blank' || link.hasAttribute('data-method') || link.href.includes('#')) return;
      if (e.ctrlKey || e.metaKey || e.shiftKey) return; // Allow manual new tabs

      var href = link.getAttribute('href').toLowerCase();
      var isTargetRoute = href.includes('-view') || href.includes('-edit') || href.includes('-create') || href.includes('user-edit') || href.includes('category-form');
      var isTargetClass = link.classList.contains('offcanvas-link');

      if (isTargetRoute || isTargetClass) {
          e.preventDefault();
          openOc(link.href, link.textContent.trim() || 'Loading...');
      }
  });

  // Intercept links inside offcanvas to also load inside offcanvas if they match
  ocCanvas.addEventListener('click', function(e) {
      var link = e.target.closest('a');
      if (!link || !link.href || link.target === '_blank' || link.hasAttribute('data-method') || link.href.includes('#')) return;
      var href = link.getAttribute('href').toLowerCase();
      if (!href.includes('?')) {
          var isTargetRoute = href.includes('-view') || href.includes('-edit') || href.includes('-create') || href.includes('add');
          if (isTargetRoute) {
              e.preventDefault();
              openOc(link.href, 'Loading...');
          }
      }
  });

})();
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>