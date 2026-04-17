<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $totalProviders, $pendingProviders, $activeProviders, $avgRating, $newProviders */
/** @var array $topVendors, $cityStats, $statusBreakdown, $period */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Vendors';

$svgEye    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
$svgEdit   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
$svgBan    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>';
$svgSearch = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;color:var(--text3);flex-shrink:0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
$svgPlus   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
$svgCheck  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
$svgX      = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:#FDCB6E;}.pg-hc-val.rose{color:var(--rose);}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);cursor:pointer;transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.filter-pill .dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.vendors-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:12px;}
.vendor-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px;transition:border-color .13s;}
.vendor-card:hover{border-color:rgba(108,92,231,.35);}
.vc-head{display:flex;align-items:center;gap:11px;margin-bottom:12px;}
.vc-avatar{width:42px;height:42px;border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;background-size:cover;background-position:center;}
.vc-name{font-size:13.5px;font-weight:700;color:var(--text1);}
.vc-cat{font-size:11px;color:var(--text3);margin-top:1px;}
.vc-badge{margin-left:auto;display:inline-block;padding:3px 9px;border-radius:20px;font-size:9.5px;font-weight:700;}
.vc-badge.active{background:var(--teal-pale);color:var(--teal);}
.vc-badge.pending{background:var(--amber-pale);color:#7a5500;}
.vc-badge.suspended{background:var(--rose-pale);color:var(--rose);}
.vc-badge.rejected{background:var(--bg3);color:var(--text3);}
[data-hl-t="dark"] .vc-badge.pending{color:var(--amber);}
.vc-stats{display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:12px;}
.vc-stat{background:var(--bg);border-radius:var(--r-sm);padding:7px 9px;}
.vc-stat-lbl{font-size:9.5px;color:var(--text3);margin-bottom:2px;}
.vc-stat-val{font-size:13px;font-weight:700;color:var(--text1);}
.vc-actions{display:flex;gap:6px;}
.vc-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:7px;border-radius:var(--r-md);font-size:11.5px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:var(--bg);color:var(--text2);transition:all .13s;text-decoration:none;}
.vc-btn:hover{background:var(--bg3);color:var(--text1);}
.vc-btn.primary{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.vc-btn svg{width:12px;height:12px;}
.vc-btn-icon{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid var(--border);background:var(--bg);cursor:pointer;transition:all .13s;}
.vc-btn-icon:hover{background:var(--bg3);}
.vc-btn-icon svg{width:13px;height:13px;}
.empty-state{text-align:center;padding:48px 0;color:var(--text3);}
.empty-state svg{width:40px;height:40px;margin:0 auto 12px;display:block;opacity:.35;}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
@media(max-width:560px){.pg-hero{grid-template-columns:1fr;}}
</style>

<?php
$currentStatus = Yii::$app->request->get('status');
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>

<!-- Hero KPIs -->
<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Vendors</h1>
        <div class="hl-pg-sub">Manage all marketplace vendors & approve new applications</div>
    </div>
    <a href="<?= Url::to(['/admin/providers']) ?>" class="hl-btn-p">
        <?= $svgPlus ?> Approval Queue
        <?php if ($pendingProviders > 0): ?>
            <span style="background:rgba(255,255,255,.25);padding:1px 6px;border-radius:20px;font-size:9.5px;"><?= $pendingProviders ?></span>
        <?php endif; ?>
    </a>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total vendors</div><div class="pg-hc-val"><?= number_format($totalProviders) ?></div><div class="pg-hc-sub">All registered</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Active</div><div class="pg-hc-val teal"><?= number_format($activeProviders) ?></div><div class="pg-hc-sub">Live on platform</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Pending review</div><div class="pg-hc-val amber"><?= number_format($pendingProviders) ?></div><div class="pg-hc-sub">Awaiting approval</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Avg rating</div><div class="pg-hc-val purple"><?= number_format($avgRating, 1) ?> ★</div><div class="pg-hc-sub">Platform average</div></div>
</div>

<!-- Filters -->
<div class="filter-row">
    <a href="<?= Url::to(['/admin/vendors']) ?>" class="filter-pill <?= !$currentStatus ? 'active' : '' ?>">All vendors</a>
    <a href="<?= Url::to(['/admin/vendors', 'status' => 'active']) ?>" class="filter-pill <?= $currentStatus === 'active' ? 'active' : '' ?>">
        <span class="dot" style="background:var(--teal)"></span> Active
    </a>
    <a href="<?= Url::to(['/admin/vendors', 'status' => 'pending']) ?>" class="filter-pill <?= $currentStatus === 'pending' ? 'active' : '' ?>">
        <span class="dot" style="background:#FDCB6E"></span> Pending
        <?php if ($pendingProviders > 0): ?><span style="background:var(--amber-pale);color:#7a5500;padding:1px 5px;border-radius:20px;font-size:9px;margin-left:2px;"><?= $pendingProviders ?></span><?php endif; ?>
    </a>
    <a href="<?= Url::to(['/admin/vendors', 'status' => 'suspended']) ?>" class="filter-pill <?= $currentStatus === 'suspended' ? 'active' : '' ?>">
        <span class="dot" style="background:var(--rose)"></span> Suspended
    </a>
    <a href="<?= Url::to(['/admin/vendors', 'status' => 'rejected']) ?>" class="filter-pill <?= $currentStatus === 'rejected' ? 'active' : '' ?>">
        <span class="dot" style="background:var(--text3)"></span> Rejected
    </a>
    <div style="margin-left:auto;">
        <form method="get" action="<?= Url::to(['/admin/vendors']) ?>" style="display:flex;gap:8px;align-items:center;">
            <?php if ($currentStatus): ?><input type="hidden" name="status" value="<?= Html::encode($currentStatus) ?>"><?php endif; ?>
            <div class="hl-search-wrap" style="margin:0;max-width:220px;">
                <?= $svgSearch ?>
                <input class="hl-search-input" type="text" name="q" value="<?= Html::encode(Yii::$app->request->get('q')) ?>" placeholder="Search vendors…">
            </div>
        </form>
    </div>
</div>

<!-- Vendor Cards -->
<?php if ($dataProvider->totalCount === 0): ?>
<div class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    <div style="font-size:13px;font-weight:600;color:var(--text2);margin-bottom:4px;">No vendors found</div>
    <div style="font-size:11.5px;">Try a different filter or wait for new applications</div>
</div>
<?php else: ?>
<div class="vendors-grid">
<?php foreach ($dataProvider->models as $i => $vendor):
    $init    = strtoupper(substr($vendor->business_name, 0, 2));
    $bg      = $vendor->logo ? "url('/uploads/logos/{$vendor->logo}')" : $colors[$i % count($colors)];
    $hasBg   = (bool)$vendor->logo;
    $cat     = $vendor->activeListings && $vendor->activeListings[0]->category ? $vendor->activeListings[0]->category->name : 'No category';
    $ordCnt  = count($vendor->orders);
    $listCnt = count($vendor->activeListings);
    $isSusp  = $vendor->status === 'suspended';
?>
<div class="vendor-card">
    <div class="vc-head">
        <div class="vc-avatar" style="background:<?= $bg ?>;<?= $hasBg ? 'background-size:cover;' : '' ?>"><?= $hasBg ? '' : Html::encode($init) ?></div>
        <div style="min-width:0;flex:1;">
            <div class="vc-name" title="<?= Html::encode($vendor->business_name) ?>"><?= Html::encode(mb_strimwidth($vendor->business_name, 0, 22, '…')) ?></div>
            <div class="vc-cat"><?= Html::encode($vendor->city ?: $cat) ?></div>
        </div>
        <span class="vc-badge <?= Html::encode($vendor->status) ?>"><?= ucfirst($vendor->status) ?></span>
    </div>
    <div class="vc-stats">
        <div class="vc-stat"><div class="vc-stat-lbl">Orders</div><div class="vc-stat-val"><?= $ordCnt ?></div></div>
        <div class="vc-stat"><div class="vc-stat-lbl">Listings</div><div class="vc-stat-val"><?= $listCnt ?></div></div>
        <div class="vc-stat"><div class="vc-stat-lbl">Rating</div><div class="vc-stat-val"><?= $vendor->rating ? number_format($vendor->rating, 1) : '—' ?></div></div>
    </div>
    <div class="vc-actions">
        <a href="<?= Url::to(['/admin/vendor-view', 'id' => $vendor->id]) ?>" class="vc-btn primary" title="View profile">
            <?= $svgEye ?> View
        </a>
        <a href="<?= Url::to(['/admin/vendor-edit', 'id' => $vendor->id]) ?>" class="vc-btn" title="Edit">
            <?= $svgEdit ?> Edit
        </a>
        <form method="post" action="<?= Url::to(['/admin/vendor-suspend', 'id' => $vendor->id]) ?>" style="display:contents;" onsubmit="return confirm('<?= $isSusp ? 'Re-activate' : 'Suspend' ?> this vendor?')">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <button type="submit" class="vc-btn-icon" title="<?= $isSusp ? 'Re-activate' : 'Suspend' ?>" style="color:<?= $isSusp ? 'var(--teal)' : 'var(--rose)' ?>;border-color:<?= $isSusp ? 'var(--teal-pale)' : 'var(--rose-pale)' ?>;">
                <?= $isSusp ? $svgCheck : $svgBan ?>
            </button>
        </form>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($dataProvider->pagination->pageCount > 1): ?>
<div style="display:flex;justify-content:center;margin-top:20px;gap:6px;">
    <?php for ($p = 1; $p <= $dataProvider->pagination->pageCount; $p++):
        $active = $p === $dataProvider->pagination->page + 1;
        $params = array_merge(Yii::$app->request->queryParams, ['page' => $p]);
    ?>
    <a href="?<?= http_build_query($params) ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active ? 'var(--acc)' : 'var(--border)' ?>;font-size:12px;font-weight:600;color:<?= $active ? 'var(--acc)' : 'var(--text2)' ?>;background:<?= $active ? 'var(--acc-pale)' : 'var(--bg2)' ?>;text-decoration:none;"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
