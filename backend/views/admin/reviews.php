<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Reviews';
$filter = Yii::$app->request->get('filter');
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:var(--amber);}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.review-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px;margin-bottom:10px;transition:border-color .13s;}
.review-card:hover{border-color:rgba(108,92,231,.3);}
.review-card:last-child{margin-bottom:0;}
.rc-head{display:flex;align-items:center;gap:11px;margin-bottom:10px;}
.rc-avatar{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;}
.rc-name{font-size:13px;font-weight:700;color:var(--text1);}
.rc-date{font-size:10.5px;color:var(--text3);margin-top:2px;}
.rc-stars{display:flex;gap:2px;margin-left:auto;}
.rc-stars svg{width:14px;height:14px;}
.rc-comment{font-size:12.5px;color:var(--text2);line-height:1.55;font-style:italic;padding:10px 13px;background:var(--bg);border-radius:var(--r-md);margin-bottom:10px;}
.rc-foot{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.rc-meta{font-size:11px;color:var(--text3);}
.rc-meta strong{color:var(--text2);font-weight:600;}
.hidden-badge{display:inline-block;padding:2px 7px;border-radius:20px;font-size:9.5px;font-weight:700;background:var(--bg3);color:var(--text3);margin-left:6px;}
.action-form{display:inline;}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Reviews</h1>
        <div class="hl-pg-sub">Moderate customer reviews across the platform</div>
    </div>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total reviews</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">All platforms</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Avg rating</div><div class="pg-hc-val amber"><?= number_format($stats['avg_rating'] ?? 0, 1) ?> ★</div><div class="pg-hc-sub">Platform-wide score</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">5-star reviews</div><div class="pg-hc-val teal"><?= number_format($stats['five_star'] ?? 0) ?></div><div class="pg-hc-sub">Top ratings</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Hidden</div><div class="pg-hc-val purple"><?= number_format($stats['hidden'] ?? 0) ?></div><div class="pg-hc-sub">Hidden from public</div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/reviews']) ?>"                               class="filter-pill <?= !$filter ? 'active' : '' ?>">All</a>
    <a href="<?= Url::to(['/admin/reviews', 'filter' => '5star']) ?>"          class="filter-pill <?= $filter === '5star'  ? 'active' : '' ?>">★★★★★ 5-star</a>
    <a href="<?= Url::to(['/admin/reviews', 'filter' => '4star']) ?>"          class="filter-pill <?= $filter === '4star'  ? 'active' : '' ?>">★★★★ 4-star</a>
    <a href="<?= Url::to(['/admin/reviews', 'filter' => '3below']) ?>"         class="filter-pill <?= $filter === '3below' ? 'active' : '' ?>">★★★ 3 &amp; below</a>
    <a href="<?= Url::to(['/admin/reviews', 'filter' => 'hidden']) ?>"         class="filter-pill <?= $filter === 'hidden' ? 'active' : '' ?>" style="<?= $filter !== 'hidden' ? 'color:var(--rose);border-color:var(--rose-pale);' : '' ?>">Hidden</a>
</div>

<?php foreach ($dataProvider->getModels() as $i => $review):
    $bg     = $colors[$i % count($colors)];
    $uname  = $review->user ? $review->user->getFullName() : 'Anonymous';
    $uinit  = strtoupper(substr($uname, 0, 1) . (str_contains($uname, ' ') ? substr($uname, strpos($uname, ' ') + 1, 1) : ''));
    $rating = (int)($review->rating ?? 5);
    $vendor = $review->provider->business_name ?? '—';
    $isHidden = $review->status === 'hidden';
?>
<div class="review-card" <?= $isHidden ? 'style="opacity:.7;"' : '' ?>>
    <div class="rc-head">
        <div class="rc-avatar" style="background:<?= $bg ?>"><?= Html::encode($uinit) ?></div>
        <div>
            <div class="rc-name">
                <?= Html::encode($uname) ?>
                <?php if ($isHidden): ?><span class="hidden-badge">Hidden</span><?php endif; ?>
            </div>
            <div class="rc-date"><?= date('d M Y · H:i', strtotime($review->created_at ?? 'now')) ?></div>
        </div>
        <div class="rc-stars">
            <?php for ($s = 1; $s <= 5; $s++): ?>
            <svg viewBox="0 0 24 24" style="fill:<?= $s <= $rating ? '#FDCB6E' : 'none' ?>;stroke:<?= $s <= $rating ? '#FDCB6E' : 'var(--border)' ?>;stroke-width:1.5;">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            <?php endfor; ?>
        </div>
    </div>
    <?php if ($review->comment): ?>
    <div class="rc-comment">"<?= Html::encode($review->comment) ?>"</div>
    <?php endif; ?>
    <div class="rc-foot">
        <div class="rc-meta">Vendor: <strong><?= Html::encode($vendor) ?></strong></div>
        <div style="display:flex;gap:6px;">
            <form class="action-form" method="post" action="<?= Url::to(['/admin/review-toggle', 'id' => $review->id]) ?>">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <button type="submit" class="hl-btn-g" style="padding:5px 10px;font-size:11px;">
                    <?= $isHidden ? 'Show' : 'Hide' ?>
                </button>
            </form>
            <form class="action-form" method="post" action="<?= Url::to(['/admin/review-delete', 'id' => $review->id]) ?>" onsubmit="return confirm('Permanently delete this review?')">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <button type="submit" class="hl-btn-g" style="padding:5px 10px;font-size:11px;color:var(--rose);border-color:var(--rose-pale);">Delete</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if ($dataProvider->totalCount === 0): ?>
<div style="text-align:center;padding:48px;color:var(--text3);font-size:12px;">No reviews found for this filter.</div>
<?php endif; ?>

<?php if ($dataProvider->pagination->pageCount > 1): ?>
<div style="display:flex;justify-content:center;margin-top:14px;gap:6px;">
    <?php for ($p = 1; $p <= $dataProvider->pagination->pageCount; $p++):
        $active = $p === $dataProvider->pagination->page + 1;
        $params = array_merge(Yii::$app->request->queryParams, ['page' => $p]);
    ?>
    <a href="?<?= http_build_query($params) ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active?'var(--acc)':'var(--border)'?>;font-size:12px;font-weight:600;color:<?= $active?'var(--acc)':'var(--text2)'?>;background:<?= $active?'var(--acc-pale)':'var(--bg2)'?>;text-decoration:none;"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
