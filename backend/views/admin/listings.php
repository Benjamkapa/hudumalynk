<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Listings';
$currentStatus = Yii::$app->request->get('status');
$currentQ = Yii::$app->request->get('q');
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];

$svgStar = '<svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:#FDCB6E;stroke:#FDCB6E;stroke-width:1;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
$svgStarE= '<svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:none;stroke:var(--border);stroke-width:1.5;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:#FDCB6E;}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);cursor:pointer;transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.action-form{display:inline;}
.feat-star{cursor:pointer;background:none;border:none;padding:3px 6px;border-radius:var(--r-sm);transition:background .13s;}
.feat-star:hover{background:var(--amber-pale);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Listings</h1>
        <div class="hl-pg-sub">Moderate and manage all marketplace listings</div>
    </div>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">All time</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Active</div><div class="pg-hc-val teal"><?= number_format($stats['active'] ?? 0) ?></div><div class="pg-hc-sub">Visible to buyers</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Draft</div><div class="pg-hc-val amber"><?= number_format($stats['draft'] ?? 0) ?></div><div class="pg-hc-sub">Not published</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">New this week</div><div class="pg-hc-val purple"><?= number_format($stats['new_week'] ?? 0) ?></div><div class="pg-hc-sub">Recently added</div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/listings']) ?>" class="filter-pill <?= !$currentStatus ? 'active' : '' ?>">All</a>
    <a href="<?= Url::to(['/admin/listings', 'status' => 'active']) ?>" class="filter-pill <?= $currentStatus === 'active' ? 'active' : '' ?>">Active</a>
    <a href="<?= Url::to(['/admin/listings', 'status' => 'draft']) ?>" class="filter-pill <?= $currentStatus === 'draft' ? 'active' : '' ?>">Draft</a>
    <a href="<?= Url::to(['/admin/listings', 'status' => 'inactive']) ?>" class="filter-pill <?= $currentStatus === 'inactive' ? 'active' : '' ?>">Inactive</a>
    <div style="margin-left:auto;display:flex;gap:8px;">
        <form method="get" action="<?= Url::to(['/admin/listings']) ?>" style="display:flex;gap:8px;">
            <?php if ($currentStatus): ?><input type="hidden" name="status" value="<?= Html::encode($currentStatus) ?>"><?php endif; ?>
            <div class="hl-search-wrap" style="margin:0;max-width:220px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;color:var(--text3);flex-shrink:0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="hl-search-input" type="text" name="q" value="<?= Html::encode($currentQ) ?>" placeholder="Search listings…">
            </div>
        </form>
    </div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">All listings</span>
        <span style="font-size:11px;color:var(--text3);"><?= $dataProvider->totalCount ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Listing</th><th>Vendor</th><th>Category</th><th>Price</th><th>Status</th><th>Featured</th><th>Added</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $listing):
            $bg     = $colors[$i % count($colors)];
            $title  = $listing->title ?? 'Untitled';
            $init   = strtoupper(substr($title, 0, 2));
            $status = $listing->status ?? 'active';
            $badgeCls = $status === 'active' ? 'paid' : ($status === 'draft' ? 'pend' : 'new');
            $isFeat = (bool)$listing->is_featured;
        ?>
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:34px;height:34px;border-radius:var(--r-md);background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($init) ?></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode(mb_strimwidth($title, 0, 30, '…')) ?></div>
                        <div style="font-size:10.5px;color:var(--text3);">#<?= $listing->id ?></div>
                    </div>
                </div>
            </td>
            <td style="color:var(--text2);font-size:12px;"><?= Html::encode($listing->provider->business_name ?? '—') ?></td>
            <td style="color:var(--text2);font-size:12px;"><?= Html::encode($listing->category->name ?? '—') ?></td>
            <td style="font-weight:700;color:var(--text1);font-size:12px;">KES <?= number_format($listing->price ?? 0) ?></td>
            <td><span class="hl-badge <?= $badgeCls ?>"><?= ucfirst($status) ?></span></td>
            <td>
                <form class="action-form" method="post" action="<?= Url::to(['/admin/toggle-featured', 'id' => $listing->id]) ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <button type="submit" class="feat-star" title="<?= $isFeat ? 'Remove featured' : 'Mark featured' ?>">
                        <?= $isFeat ? $svgStar : $svgStarE ?>
                    </button>
                </form>
            </td>
            <td style="color:var(--text3);font-size:11.5px;"><?= date('d M Y', strtotime($listing->created_at ?? 'now')) ?></td>
            <td>
                <div style="display:flex;gap:5px;align-items:center;">
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/toggle-listing', 'id' => $listing->id]) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-g" style="padding:5px 9px;font-size:11px;<?= $status === 'active' ? 'color:var(--rose);border-color:var(--rose-pale);' : 'color:var(--teal);border-color:var(--teal-pale);' ?>">
                            <?= $status === 'active' ? 'Deactivate' : 'Activate' ?>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No listings found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php if ($dataProvider->pagination->pageCount > 1): ?>
    <div style="display:flex;justify-content:center;padding:14px;gap:6px;">
        <?php for ($p = 1; $p <= $dataProvider->pagination->pageCount; $p++):
            $active = $p === $dataProvider->pagination->page + 1;
            $params = array_merge(Yii::$app->request->queryParams, ['page' => $p]);
        ?>
        <a href="?<?= http_build_query($params) ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active?'var(--acc)':'var(--border)'?>;font-size:12px;font-weight:600;color:<?= $active?'var(--acc)':'var(--text2)'?>;background:<?= $active?'var(--acc-pale)':'var(--bg2)'?>;text-decoration:none;"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>