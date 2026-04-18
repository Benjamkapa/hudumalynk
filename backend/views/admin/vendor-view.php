<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $vendor */
/** @var array $stats, $recentOrders, $recentReviews */
/** @var common\models\Subscription|null $subscription */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Vendor: ' . $vendor->business_name;
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.vv-grid{display:grid;grid-template-columns:300px 1fr;gap:14px;align-items:start;}
.vv-sidebar{display:flex;flex-direction:column;gap:12px;}
.vv-profile{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:20px;text-align:center;}
.vv-avatar{width:72px;height:72px;border-radius:var(--r-lg);background:var(--acc);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#fff;margin:0 auto 12px;background-size:cover;background-position:center;}
.vv-name{font-size:16px;font-weight:800;color:var(--text1);letter-spacing:-.02em;}
.vv-city{font-size:11.5px;color:var(--text3);margin-top:3px;}
.vv-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;margin-top:8px;}
.vv-badge.active{background:var(--teal-pale);color:var(--teal);}
.vv-badge.pending{background:var(--amber-pale);color:#7a5500;}
.vv-badge.suspended{background:var(--rose-pale);color:var(--rose);}
.vv-badge.rejected{background:var(--bg3);color:var(--text3);}
[data-hl-t="dark"] .vv-badge.pending{color:var(--amber);}
.vv-info{display:flex;flex-direction:column;gap:6px;margin-top:14px;text-align:left;}
.vv-info-row{display:flex;justify-content:space-between;align-items:center;font-size:12px;}
.vv-info-row .lbl{color:var(--text3);}
.vv-info-row .val{color:var(--text1);font-weight:600;}
.vv-actions{display:flex;flex-direction:column;gap:6px;margin-top:14px;}
.kpi-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.kpi-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-md);padding:12px 14px;}
.kpi-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:4px;}
.kpi-val{font-size:20px;font-weight:800;letter-spacing:-.03em;color:var(--text1);}
.kpi-val.teal{color:var(--teal);}
.kpi-val.purple{color:var(--acc2);}
.kpi-val.amber{color:#FDCB6E;}
.star{color:#FDCB6E;}
@media(max-width:768px){.vv-grid{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?= Url::to(['/admin/vendors']) ?>" class="hl-btn-g" style="padding:6px 10px;font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="15 18 9 12 15 6"/></svg> Vendors
        </a>
        <h1 class="hl-pg-title"><?= Html::encode($vendor->business_name) ?></h1>
        <?php if ($vendor->is_verified): ?>
            <span style="display:inline-flex;align-items:center;gap:3px;background:var(--acc-pale);color:var(--acc);padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">✓ Verified</span>
        <?php endif; ?>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="<?= Url::to(['/admin/vendor-edit', 'id' => $vendor->id]) ?>" class="hl-btn-g offcanvas-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Profile
        </a>
        <form method="post" action="<?= Url::to(['/admin/vendor-suspend', 'id' => $vendor->id]) ?>" style="display:inline;" onsubmit="return confirm('<?= $vendor->status === 'suspended' ? 'Re-activate' : 'Suspend' ?> this vendor?')">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <button type="submit" class="hl-btn-g" style="color:<?= $vendor->status === 'suspended' ? 'var(--teal)' : 'var(--rose)' ?>;border-color:<?= $vendor->status === 'suspended' ? 'var(--teal-pale)' : 'var(--rose-pale)' ?>;">
                <?= $vendor->status === 'suspended' ? 'Re-activate' : 'Suspend' ?>
            </button>
        </form>
    </div>
</div>

<div class="vv-grid">
    <!-- Sidebar -->
    <div class="vv-sidebar">
        <div class="vv-profile">
            <?php $logoSrc = $vendor->logo ? rtrim(Yii::$app->params['frontendUrl'], '/') . "/uploads/logos/{$vendor->logo}" : null; ?>
            <div class="vv-avatar" style="background:<?= $logoSrc ? "url('{$logoSrc}')" : '#6C5CE7' ?>;<?= $logoSrc ? 'background-size:cover;' : '' ?>">
                <?= $logoSrc ? '' : Html::encode(strtoupper(substr($vendor->business_name, 0, 2))) ?>
            </div>
            <div class="vv-name"><?= Html::encode($vendor->business_name) ?></div>
            <div class="vv-city"><?= Html::encode($vendor->city ?: 'No city set') ?></div>
            <span class="vv-badge <?= Html::encode($vendor->status) ?>"><?= ucfirst($vendor->status) ?></span>

            <div class="vv-info">
                <div class="vv-info-row"><span class="lbl">Owner</span><span class="val"><?= Html::encode($vendor->user->getFullName() ?? '—') ?></span></div>
                <div class="vv-info-row"><span class="lbl">Email</span><span class="val" style="font-size:11px;"><?= Html::encode($vendor->user->email ?? '—') ?></span></div>
                <div class="vv-info-row"><span class="lbl">Phone</span><span class="val"><?= Html::encode($vendor->phone ?: ($vendor->user->phone ?? '—')) ?></span></div>
                <div class="vv-info-row"><span class="lbl">Joined</span><span class="val"><?= date('d M Y', strtotime($vendor->created_at)) ?></span></div>
                <?php if ($vendor->website): ?>
                <div class="vv-info-row"><span class="lbl">Website</span><a href="<?= Html::encode($vendor->website) ?>" target="_blank" style="color:var(--acc);font-size:11px;font-weight:600;">Visit →</a></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Subscription -->
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Subscription</span></div>
            <div style="padding:14px 15px;">
            <?php if ($subscription && $subscription->plan): ?>
                <div style="font-size:13px;font-weight:700;color:var(--acc);margin-bottom:6px;"><?= Html::encode($subscription->plan->name) ?></div>
                <div style="font-size:11.5px;color:var(--text3);margin-bottom:3px;">Expires <?= date('d M Y', strtotime($subscription->end_date)) ?></div>
                <div style="font-size:11.5px;color:var(--text3);">KES <?= number_format($subscription->plan->price_kes) ?>/period</div>
                <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;background:var(--teal-pale);color:var(--teal);margin-top:8px;">Active</span>
            <?php else: ?>
                <div style="font-size:12px;color:var(--text3);">No active subscription</div>
                <div style="font-size:11px;color:var(--rose);margin-top:4px;">Cannot publish listings</div>
            <?php endif; ?>
            </div>
        </div>

        <?php if ($vendor->description): ?>
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">About</span></div>
            <div style="padding:14px 15px;font-size:12.5px;color:var(--text2);line-height:1.55;"><?= Html::encode($vendor->description) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main content -->
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="kpi-grid">
            <div class="kpi-card"><div class="kpi-lbl">Listings</div><div class="kpi-val purple"><?= number_format($stats['listings']) ?></div></div>
            <div class="kpi-card"><div class="kpi-lbl">Total Orders</div><div class="kpi-val"><?= number_format($stats['orders']) ?></div></div>
            <div class="kpi-card"><div class="kpi-lbl">Revenue</div><div class="kpi-val teal">KES <?= number_format($stats['revenue']) ?></div></div>
            <div class="kpi-card"><div class="kpi-lbl">Avg Rating</div><div class="kpi-val amber"><?= number_format($stats['avg_rating'], 1) ?> <span class="star">★</span></div></div>
        </div>

        <!-- Recent Orders -->
        <div class="hl-card">
            <div class="hl-card-head">
                <span class="hl-card-title">Recent Orders</span>
                <a href="<?= Url::to(['/admin/orders']) ?>" class="hl-card-link">View all →</a>
            </div>
            <?php if (empty($recentOrders)): ?>
            <div style="padding:24px;text-align:center;color:var(--text3);font-size:12px;">No orders yet</div>
            <?php else: ?>
            <table class="hl-tbl">
                <thead><tr><th>Order</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($recentOrders as $i => $o):
                    $bg = $colors[$i % count($colors)];
                    $cname = $o->user ? $o->user->getFullName() : 'Unknown';
                    $cinit = strtoupper(substr($cname, 0, 2));
                    $stCls = match($o->status) { 'completed' => 'paid', 'pending','awaiting_payment' => 'pend', default => 'new' };
                ?>
                <tr>
                    <td style="font-weight:700;font-size:12px;color:var(--text1);">ORD-<?= strtoupper(sprintf('%06d', $o->id)) ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:9.5px;font-weight:800;color:#fff;"><?= Html::encode($cinit) ?></div>
                            <span style="font-size:12px;color:var(--text1);"><?= Html::encode($cname) ?></span>
                        </div>
                    </td>
                    <td style="font-weight:700;font-size:12px;">KES <?= number_format($o->total_amount) ?></td>
                    <td><span class="hl-badge <?= $stCls ?>"><?= ucfirst(str_replace('_', ' ', $o->status)) ?></span></td>
                    <td style="font-size:11px;color:var(--text3);"><?= date('d M Y', strtotime($o->created_at)) ?></td>
                    <td><a href="<?= Url::to(['/admin/order-view', 'id' => $o->id]) ?>" class="hl-btn-g offcanvas-link" style="padding:4px 9px;font-size:11px;">View</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Recent Reviews -->
        <div class="hl-card">
            <div class="hl-card-head">
                <span class="hl-card-title">Recent Reviews</span>
                <a href="<?= Url::to(['/admin/reviews']) ?>" class="hl-card-link">View all →</a>
            </div>
            <?php if (empty($recentReviews)): ?>
            <div style="padding:24px;text-align:center;color:var(--text3);font-size:12px;">No reviews yet</div>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:0;">
            <?php foreach ($recentReviews as $i => $r):
                $bg    = $colors[$i % count($colors)];
                $uname = $r->user ? $r->user->getFullName() : 'Anonymous';
                $uinit = strtoupper(substr($uname, 0, 2));
                $rating= (int)$r->rating;
            ?>
            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-bottom:1px solid var(--border);">
                <div style="width:32px;height:32px;border-radius:50%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($uinit) ?></div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($uname) ?></span>
                        <span style="font-size:11px;color:#FDCB6E;"><?= str_repeat('★', $rating) ?><span style="color:var(--text3);"><?= str_repeat('★', 5 - $rating) ?></span></span>
                        <span style="font-size:10.5px;color:var(--text3);margin-left:auto;"><?= date('d M Y', strtotime($r->created_at)) ?></span>
                    </div>
                    <?php if ($r->comment): ?><div style="font-size:12px;color:var(--text2);line-height:1.4;font-style:italic;">"<?= Html::encode(mb_strimwidth($r->comment, 0, 100, '…')) ?>"</div><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
