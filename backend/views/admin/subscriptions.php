<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
/** @var common\models\SubscriptionPlan[] $plans */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Subscriptions';
$currentStatus = Yii::$app->request->get('status');
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:22px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.amber{color:#FDCB6E;}.pg-hc-val.purple{color:var(--acc2);}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.sub-st{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;}
.sub-st.active{background:var(--teal-pale);color:var(--teal);}
.sub-st.expired{background:var(--rose-pale);color:var(--rose);}
.sub-st.cancelled{background:var(--bg3);color:var(--text3);}
.action-form{display:inline;}
.plans-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-bottom:16px;}
.plan-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px;}
.plan-name{font-size:13px;font-weight:700;color:var(--text1);margin-bottom:4px;}
.plan-price{font-size:18px;font-weight:800;color:var(--acc);letter-spacing:-.03em;}
.plan-period{font-size:10.5px;color:var(--text3);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Subscriptions</h1>
        <div class="hl-pg-sub">Manage vendor subscription plans and billing</div>
    </div>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Active subs</div><div class="pg-hc-val teal"><?= number_format($stats['active'] ?? 0) ?></div><div class="pg-hc-sub">Currently subscribed</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Total revenue</div><div class="pg-hc-val">KES <?= number_format($stats['total_revenue'] ?? 0) ?></div><div class="pg-hc-sub">All subscription payments</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Expiring soon</div><div class="pg-hc-val amber"><?= number_format($stats['expiring_soon'] ?? 0) ?></div><div class="pg-hc-sub">Within next 7 days</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Expired</div><div class="pg-hc-val purple"><?= number_format($stats['expired'] ?? 0) ?></div><div class="pg-hc-sub">Lapsed subscriptions</div></div>
</div>

<!-- Plans overview -->
<?php if (!empty($plans)): ?>
<div style="margin-bottom:16px;">
    <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Available Plans</div>
    <div class="plans-grid">
        <?php foreach ($plans as $plan): ?>
        <div class="plan-card">
            <div class="plan-name"><?= Html::encode($plan->name) ?></div>
            <div class="plan-price">KES <?= number_format($plan->price_kes) ?></div>
            <div class="plan-period"><?= $plan->duration_days ?? 30 ?> days · up to <?= $plan->max_products ?? '∞' ?> listings</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/subscriptions']) ?>"                             class="filter-pill <?= !$currentStatus ? 'active' : '' ?>">All</a>
    <a href="<?= Url::to(['/admin/subscriptions', 'status' => 'active']) ?>"       class="filter-pill <?= $currentStatus === 'active'    ? 'active' : '' ?>">Active</a>
    <a href="<?= Url::to(['/admin/subscriptions', 'status' => 'expired']) ?>"      class="filter-pill <?= $currentStatus === 'expired'   ? 'active' : '' ?>">Expired</a>
    <a href="<?= Url::to(['/admin/subscriptions', 'status' => 'cancelled']) ?>"    class="filter-pill <?= $currentStatus === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">All subscriptions</span>
        <span style="font-size:11px;color:var(--text3);"><?= number_format($dataProvider->totalCount) ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Provider</th><th>Plan</th><th>Status</th><th>Start</th><th>End</th><th>Amount</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $sub):
            $bg = $colors[$i % count($colors)];
            $vname  = $sub->provider->business_name ?? 'Unknown';
            $vinit  = strtoupper(substr($vname, 0, 2));
            $status = $sub->status ?? 'active';
            $isExpiring = $status === 'active' && isset($sub->end_date) && strtotime($sub->end_date) - time() < 7 * 86400 && strtotime($sub->end_date) > time();
        ?>
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:30px;height:30px;border-radius:var(--r-sm);background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($vinit) ?></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($vname) ?></div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($sub->provider->user->email ?? '') ?></div>
                    </div>
                </div>
            </td>
            <td style="font-size:12.5px;font-weight:600;color:var(--acc);"><?= Html::encode($sub->plan->name ?? '—') ?></td>
            <td>
                <span class="sub-st <?= Html::encode($status) ?>"><?= ucfirst($status) ?></span>
                <?php if ($isExpiring): ?>
                <span style="display:inline-block;padding:2px 6px;border-radius:20px;font-size:9px;font-weight:700;background:var(--amber-pale);color:#7a5500;margin-left:4px;">Expiring</span>
                <?php endif; ?>
            </td>
            <td style="font-size:11.5px;color:var(--text3);"><?= $sub->start_date ? date('d M Y', strtotime($sub->start_date)) : '—' ?></td>
            <td style="font-size:11.5px;color:<?= $isExpiring ? 'var(--amber)' : 'var(--text3)' ?>;font-weight:<?= $isExpiring ? '600' : '400' ?>;">
                <?= $sub->end_date ? date('d M Y', strtotime($sub->end_date)) : '—' ?>
            </td>
            <td style="font-size:13px;font-weight:700;color:var(--text1);">KES <?= number_format($sub->amount ?? $sub->plan->price_kes ?? 0) ?></td>
            <td>
                <div style="display:flex;gap:5px;">
                    <?php if ($status === 'active' || $status === 'expired'): ?>
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/subscription-renew', 'id' => $sub->id]) ?>" onsubmit="return confirm('Renew this subscription?')">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-g" style="padding:5px 10px;font-size:11px;">Renew</button>
                    </form>
                    <?php endif; ?>
                    <?php if ($status === 'active'): ?>
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/subscription-cancel', 'id' => $sub->id]) ?>" onsubmit="return confirm('Cancel this subscription?')">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-g" style="padding:5px 10px;font-size:11px;color:var(--rose);border-color:var(--rose-pale);">Cancel</button>
                    </form>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No subscriptions found</td></tr>
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
