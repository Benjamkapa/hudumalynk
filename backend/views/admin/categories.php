<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats, $parents */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Categories';
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:#FDCB6E;}
.type-badge{display:inline-block;padding:2px 7px;border-radius:20px;font-size:9.5px;font-weight:700;}
.type-badge.service{background:var(--acc-pale);color:var(--acc);}
.type-badge.product{background:var(--teal-pale);color:var(--teal);}
.type-badge.both{background:var(--bg3);color:var(--text2);}
.action-form{display:inline;}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Categories</h1>
        <div class="hl-pg-sub">Manage listing categories and subcategories</div>
    </div>
    <a href="<?= Url::to(['/admin/category-create']) ?>" class="hl-btn-p offcanvas-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Category
    </a>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">All categories</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Active</div><div class="pg-hc-val teal"><?= number_format($stats['active'] ?? 0) ?></div><div class="pg-hc-sub">Visible to vendors</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Services</div><div class="pg-hc-val purple"><?= number_format($stats['services'] ?? 0) ?></div><div class="pg-hc-sub">Service categories</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Products</div><div class="pg-hc-val amber"><?= number_format($stats['products'] ?? 0) ?></div><div class="pg-hc-sub">Product categories</div></div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">All categories</span>
        <span style="font-size:11px;color:var(--text3);"><?= $dataProvider->totalCount ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Category</th><th>Parent</th><th>Type</th><th>Listings</th><th>Sort</th><th>Status</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $cat):
            $bg      = $colors[$i % count($colors)];
            $init    = strtoupper(substr($cat->name, 0, 2));
            $listCnt = $cat->getListings()->count();
        ?>
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:32px;height:32px;border-radius:var(--r-md);background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($init) ?></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($cat->name) ?></div>
                        <?php if ($cat->description): ?>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode(mb_strimwidth($cat->description, 0, 40, '…')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td style="font-size:12px;color:var(--text2);"><?= Html::encode($cat->parent->name ?? '—') ?></td>
            <td><span class="type-badge <?= Html::encode($cat->type ?? 'both') ?>"><?= ucfirst($cat->type ?? 'both') ?></span></td>
            <td style="font-size:12px;font-weight:600;color:var(--text1);"><?= $listCnt ?></td>
            <td style="font-size:12px;color:var(--text2);"><?= $cat->sort_order ?? 0 ?></td>
            <td>
                <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;background:<?= $cat->status === 'active' ? 'var(--teal-pale)' : 'var(--bg3)' ?>;color:<?= $cat->status === 'active' ? 'var(--teal)' : 'var(--text3)' ?>;">
                    <?= ucfirst($cat->status) ?>
                </span>
            </td>
            <td>
                <div style="display:flex;gap:5px;">
                    <a href="<?= Url::to(['/admin/category-edit', 'id' => $cat->id]) ?>" class="hl-btn-g offcanvas-link" style="padding:5px 9px;font-size:11px;">Edit</a>
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/category-delete', 'id' => $cat->id]) ?>" onsubmit="return confirm('Delete category &quot;<?= Html::encode($cat->name) ?>&quot;? This cannot be undone.')">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-g" style="padding:5px 9px;font-size:11px;color:var(--rose);border-color:var(--rose-pale);">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No categories yet — <a href="<?= Url::to(['/admin/category-create']) ?>" class="offcanvas-link" style="color:var(--acc);">create the first one</a></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php if ($dataProvider->pagination->pageCount > 1): ?>
    <div style="display:flex;justify-content:center;padding:14px;gap:6px;">
        <?php for ($p = 1; $p <= $dataProvider->pagination->pageCount; $p++):
            $active = $p === $dataProvider->pagination->page + 1;
        ?>
        <a href="?page=<?= $p ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active?'var(--acc)':'var(--border)'?>;font-size:12px;font-weight:600;color:<?= $active?'var(--acc)':'var(--text2)'?>;background:<?= $active?'var(--acc-pale)':'var(--bg2)'?>;text-decoration:none;"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>