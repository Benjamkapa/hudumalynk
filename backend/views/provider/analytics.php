<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $stats */
/** @var array $monthlyRevenue */
/** @var array $statusCounts */
/** @var common\models\Listing[] $topListings */

use yii\helpers\Html;

$this->title = 'Analytics & Insights';
$maxRevenue = max(array_map(static fn($row) => (float) $row['revenue'], $monthlyRevenue)) ?: 1;
?>

<style>
.pa-shell{display:flex;flex-direction:column;gap:16px;}
.pa-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.pa-sub{font-size:12px;color:var(--text3);margin-top:4px;line-height:1.5;}
.pa-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;}
.pa-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:15px;}
.pa-card .lbl{font-size:10px;font-weight:700;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;margin-bottom:5px;}
.pa-card .val{font-size:24px;font-weight:800;color:var(--text1);}
.pa-grid{display:grid;grid-template-columns:1.2fr .9fr;gap:16px;}
.pa-bar{display:flex;align-items:flex-end;gap:10px;height:220px;padding:18px 16px 10px;}
.pa-col{flex:1;display:flex;flex-direction:column;justify-content:flex-end;gap:8px;align-items:center;}
.pa-col .bar{width:100%;max-width:48px;border-radius:14px 14px 6px 6px;background:linear-gradient(180deg,var(--acc2),var(--acc));min-height:10px;}
.pa-col .val{font-size:10px;color:var(--text3);}
.pa-col .lbl{font-size:11px;font-weight:700;color:var(--text2);}
.pa-status{display:flex;flex-direction:column;gap:10px;padding:16px;}
.pa-status-row{display:grid;grid-template-columns:110px 1fr auto;gap:10px;align-items:center;font-size:12px;}
.pa-status-track{height:10px;border-radius:999px;background:var(--bg3);overflow:hidden;}
.pa-status-fill{height:100%;border-radius:999px;background:var(--teal);}
@media(max-width:980px){.pa-stats{grid-template-columns:1fr 1fr;}.pa-grid{grid-template-columns:1fr;}}
@media(max-width:560px){.pa-stats{grid-template-columns:1fr;}}
</style>

<div class="pa-shell">
    <div>
        <div class="pa-title">Analytics & Insights</div>
        <div class="pa-sub">Performance overview for <?= Html::encode($provider->business_name) ?> across orders, revenue, customer satisfaction, and listing activity.</div>
    </div>

    <div class="pa-stats">
        <div class="pa-card"><div class="lbl">Orders</div><div class="val"><?= number_format($stats['orders_total'] ?? 0) ?></div></div>
        <div class="pa-card"><div class="lbl">Completed</div><div class="val"><?= number_format($stats['completed_orders'] ?? 0) ?></div></div>
        <div class="pa-card"><div class="lbl">Gross Revenue</div><div class="val">KES <?= number_format((float) ($stats['gross_revenue'] ?? 0), 0) ?></div></div>
        <div class="pa-card"><div class="lbl">Average Rating</div><div class="val"><?= number_format((float) ($stats['avg_rating'] ?? 0), 1) ?>/5</div></div>
    </div>

    <div class="pa-grid">
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Revenue Trend</span></div>
            <div class="pa-bar">
                <?php foreach ($monthlyRevenue as $row): ?>
                    <?php $height = max(12, (int) round(((float) $row['revenue'] / $maxRevenue) * 170)); ?>
                    <div class="pa-col">
                        <div class="val">KES <?= number_format((float) $row['revenue'], 0) ?></div>
                        <div class="bar" style="height:<?= $height ?>px;"></div>
                        <div class="lbl"><?= Html::encode($row['label']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="hl-card">
                <div class="hl-card-head"><span class="hl-card-title">Order Pipeline</span></div>
                <div class="pa-status">
                    <?php $maxStatus = max($statusCounts) ?: 1; ?>
                    <?php foreach ($statusCounts as $status => $count): ?>
                        <div class="pa-status-row">
                            <span><?= Html::encode(ucfirst(str_replace('_', ' ', $status))) ?></span>
                            <div class="pa-status-track"><div class="pa-status-fill" style="width:<?= max(8, (int) round(($count / $maxStatus) * 100)) ?>%;"></div></div>
                            <strong><?= number_format($count) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="hl-card">
                <div class="hl-card-head"><span class="hl-card-title">Recent Listings</span></div>
                <div style="padding:12px 16px;display:flex;flex-direction:column;gap:10px;">
                    <?php if (!$topListings): ?>
                        <div style="font-size:12px;color:var(--text3);">No listings yet.</div>
                    <?php endif; ?>
                    <?php foreach ($topListings as $listing): ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                            <div>
                                <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($listing->name) ?></div>
                                <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($listing->status) ?></div>
                            </div>
                            <span class="hl-badge <?= $listing->status === 'active' ? 'paid' : 'pend' ?>"><?= Html::encode($listing->status) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
