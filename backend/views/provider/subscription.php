<?php
/** @var common\models\Provider $provider */
/** @var common\models\SubscriptionPlan[] $plans */
/** @var common\models\Subscription|null $current */
/** @var common\models\Payment[] $payments */
/** @var int $usedProducts */
/** @var int $usedServices */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<style>
/* ── Subscription page — inherits dashboard design tokens ── */

/* Layout rows reuse .hero-cards / grid patterns from dashboard */
.sub-stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap: 12px;
    margin-bottom: 16px;
}

/* Hero plan card — extends .hero-card style */
.sub-active-card {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 20px 22px 18px;
    margin-bottom: 16px;
    position: relative;
}
.sub-active-card.expiring {
    border-color: #f59e0b;
    background: linear-gradient(135deg, rgba(245,158,11,.05) 0%, var(--bg2) 60%);
}
.sub-active-card.active-plan {
    border-color: #22c55e;
    background: linear-gradient(135deg, rgba(34,197,94,.05) 0%, var(--bg2) 60%);
}

/* Badge — matches dashboard's order-status pattern */
.sub-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    padding: 3px 10px;
    border-radius: 12px;
    margin-bottom: 10px;
}
.sub-badge.active  { background: rgba(34,197,94,.12);  color: #22c55e; }
.sub-badge.warning { background: rgba(245,158,11,.12); color: #f59e0b; }
.sub-badge.inactive{ background: rgba(239,68,68,.12);  color: #ef4444; }

/* Plan name + price — mirrors .hc-value / .hc-sub */
.sub-plan-name-lg {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -.04em;
    line-height: 1.1;
    color: var(--text1);
    margin-bottom: 4px;
}
.sub-plan-price-sm {
    font-size: 12px;
    color: var(--text3);
    margin-bottom: 16px;
}

/* Countdown — same density as dashboard stat items */
.sub-countdown {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}
.sub-countdown-box {
    background: var(--bg1, var(--bg));
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 8px 14px;
    text-align: center;
    min-width: 52px;
}
.sub-countdown-val {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.04em;
    color: var(--text1);
    line-height: 1;
}
.sub-countdown-lbl {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text3);
    margin-top: 2px;
}
.sub-countdown-sep {
    font-size: 18px;
    font-weight: 800;
    color: var(--border);
}

/* Usage bars */
.sub-usage-section {
    border-top: 1px solid var(--border);
    padding-top: 14px;
    margin-top: 4px;
}
.sub-usage-section-lbl {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text3);
    margin-bottom: 10px;
}
.sub-usage-row { margin-bottom: 10px; }
.sub-usage-label-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 600;
    color: var(--text2);
    margin-bottom: 5px;
}
.sub-usage-label-row span:last-child { color: var(--text3); font-weight: 400; }
.sub-track {
    height: 6px;
    background: var(--bg1, rgba(0,0,0,.06));
    border-radius: 99px;
    overflow: hidden;
    border: 1px solid var(--border);
}
.sub-fill {
    height: 100%;
    border-radius: 99px;
    background: var(--acc, #6C5CE7);
    transition: width .6s ease;
}
.sub-fill.green  { background: #22c55e; }
.sub-fill.yellow { background: #f59e0b; }
.sub-fill.red    { background: #ef4444; }

/* ── Stat cards — extend .hero-card ── */
.hero-card .hc-label { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .07em; font-weight: 600; margin-bottom: 6px; }
.hero-card .hc-value { font-size: 32px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; margin-bottom: 5px; color: var(--text1); }
.hero-card .hc-sub   { font-size: 12px; color: var(--text3); }

/* ── Plan cards ── */
.sub-plan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.sub-plan-card {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    transition: box-shadow .2s;
}
.sub-plan-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); }
.sub-plan-card.plan-popular { border-color: var(--acc, #6C5CE7); }
.sub-plan-card.plan-current { border-color: #22c55e; }

.sub-plan-ribbon {
    text-align: center;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    padding: 4px 0;
}
.sub-plan-ribbon.ribbon-popular { background: var(--acc, #6C5CE7); color: #fff; }
.sub-plan-ribbon.ribbon-current { background: #22c55e; color: #fff; }

.sub-plan-body {
    padding: 18px 18px 14px;
    flex: 1;
}
.sub-plan-nm {
    font-size: 13px;
    font-weight: 700;
    color: var(--text1);
    margin-bottom: 6px;
}
.sub-plan-price-big {
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -.04em;
    color: var(--acc, #6C5CE7);
    line-height: 1;
    margin-bottom: 4px;
}
.sub-plan-price-big small {
    font-size: 13px;
    font-weight: 500;
    color: var(--text3);
}
.sub-plan-desc-sm {
    font-size: 11px;
    color: var(--text3);
    margin-bottom: 14px;
    line-height: 1.45;
}
.sub-plan-features {
    list-style: none;
    padding: 0; margin: 0;
    display: flex;
    flex-direction: column;
    gap: 7px;
}
.sub-plan-features li {
    font-size: 12px;
    color: var(--text2);
    display: flex;
    align-items: center;
    gap: 7px;
}
.sub-plan-features li i { color: #22c55e; flex-shrink: 0; }

.sub-plan-foot { padding: 12px 18px 16px; }

/* ── Section headers — same as .hl-card-head ── */
.sub-section-head {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text3);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sub-section-head::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* ── Comparison table ── */
.sub-compare-wrap {
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    overflow: hidden;
    margin-bottom: 16px;
}
.sub-compare-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.sub-compare-table th {
    padding: 10px 14px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    background: var(--bg2);
    border-bottom: 1px solid var(--border);
    color: var(--text2);
}
.sub-compare-table th:first-child { text-align: left; }
.sub-compare-table td {
    padding: 9px 14px;
    text-align: center;
    border-bottom: 1px solid var(--border);
    color: var(--text2);
}
.sub-compare-table td:first-child { text-align: left; color: var(--text3); }
.sub-compare-table tr:last-child td { border-bottom: none; }
.sub-compare-table .chk { color: #22c55e; }
.sub-compare-table .crs { color: var(--border); }
.sub-compare-table .hi  { background: rgba(108,92,231,.04); }

/* ── Payment history — mirrors .order-item ── */
.sub-pay-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 9px 0;
    border-bottom: 1px solid var(--border);
}
.sub-pay-item:last-child { border-bottom: none; }
.sub-pay-icon {
    width: 34px; height: 34px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.sub-pay-icon.paid    { background: rgba(34,197,94,.12); color: #22c55e; }
.sub-pay-icon.pending { background: rgba(245,158,11,.12); color: #f59e0b; }
.sub-pay-icon.failed  { background: rgba(239,68,68,.12);  color: #ef4444; }
.sub-pay-ref  { font-size: 13px; font-weight: 600; color: var(--text1); }
.sub-pay-meta { font-size: 11px; color: var(--text3); }
.sub-pay-amount { margin-left: auto; font-size: 13px; font-weight: 700; color: var(--text1); }
.sub-pay-status {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 12px;
    margin-left: 8px;
}
.sub-pay-status.paid    { background: rgba(34,197,94,.1);  color: #22c55e; }
.sub-pay-status.pending { background: rgba(245,158,11,.1); color: #f59e0b; }
.sub-pay-status.failed  { background: rgba(239,68,68,.1);  color: #ef4444; }

/* ── Danger zone ── */
.sub-danger-box {
    background: var(--bg2);
    border: 1px solid rgba(239,68,68,.3);
    border-radius: var(--r-lg);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.sub-danger-box h6 { margin: 0 0 3px; font-size: 13px; font-weight: 700; color: var(--text1); }
.sub-danger-box p  { margin: 0; font-size: 12px; color: var(--text3); }

/* ── No-subscription alert ── */
.sub-no-plan-alert {
    background: rgba(239,68,68,.07);
    border: 1px solid rgba(239,68,68,.25);
    border-radius: var(--r-lg);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text2);
    margin-bottom: 16px;
}
.sub-no-plan-alert i { color: #ef4444; font-size: 16px; flex-shrink: 0; }

@media (max-width: 900px) {
    .sub-stats-row { grid-template-columns: repeat(2, 1fr); }
    .sub-plan-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .sub-stats-row { grid-template-columns: 1fr 1fr; }
    .sub-countdown  { flex-wrap: wrap; }
}
</style>

<!-- ── Page header (mirrors dashboard .hl-pg-head) ── -->
<div class="hl-pg-head">
    <h1 class="hl-pg-title">My Subscription</h1>
    <div class="hl-pg-sub">Manage your billing, usage, and listing limits</div>
</div>

<?php if ($current):
    $unlimited   = 999;
    $isWarning   = $current->isExpiringSoon();
    $endTs       = strtotime($current->end_date);
    $hoursLeft   = max(0, (int)(($endTs - time()) / 3600));
    $daysDisplay = floor($hoursLeft / 24);
    $hrsDisplay  = $hoursLeft % 24;
    $daysLeft    = $current->daysRemaining();

    $maxP = $current->plan->max_products;
    $maxS = $current->plan->max_services;
    $usedProducts = \common\models\Listing::find()->where(['provider_id' => $provider->id])->count();
    $usedServices = 0;

    $prodPct = ($maxP >= $unlimited) ? ($usedProducts > 0 ? 10 : 0) : ($maxP > 0 ? min(100, round($usedProducts / $maxP * 100)) : 0);
    $svcPct  = ($maxS >= $unlimited) ? 0 : ($maxS > 0 ? min(100, round($usedServices / $maxS * 100)) : 0);

    $prodFill = $prodPct >= 90 ? 'red' : ($prodPct >= 70 ? 'yellow' : 'green');
    $svcFill  = $svcPct  >= 90 ? 'red' : ($svcPct  >= 70 ? 'yellow' : 'green');
?>

    <!-- ── Active plan card ── -->
    <div class="sub-active-card <?= $isWarning ? 'expiring' : 'active-plan' ?>">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div>
                <div class="sub-badge <?= $isWarning ? 'warning' : 'active' ?>">
                    <i class="bi bi-lightning-fill"></i>
                    <?= $isWarning ? 'Expiring Soon' : 'Active Plan' ?>
                </div>
                <div class="sub-plan-name-lg"><?= Html::encode($current->plan->name) ?></div>
                <div class="sub-plan-price-sm">
                    KES <?= number_format($current->plan->price_kes, 0) ?>/month ·
                    Renews <?= Yii::$app->formatter->asDate($current->end_date, 'php:d M Y') ?>
                </div>
                <div class="sub-countdown">
                    <div class="sub-countdown-box">
                        <div class="sub-countdown-val"><?= $daysDisplay ?></div>
                        <div class="sub-countdown-lbl">Days</div>
                    </div>
                    <div class="sub-countdown-sep">:</div>
                    <div class="sub-countdown-box">
                        <div class="sub-countdown-val"><?= str_pad($hrsDisplay, 2, '0', STR_PAD_LEFT) ?></div>
                        <div class="sub-countdown-lbl">Hrs</div>
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <a class="hc-link" href="<?= Url::to(['/provider/orders']) ?>">View Orders →</a>
            </div>
        </div>

        <div class="sub-usage-section">
            <div class="sub-usage-section-lbl">Usage</div>
            <!-- Products -->
            <div class="sub-usage-row">
                <div class="sub-usage-label-row">
                    <span><i class="bi bi-box-seam me-1"></i> Products</span>
                    <span>
                        <?= $usedProducts ?> /
                        <?= ($maxP >= $unlimited) ? '∞' : $maxP ?>
                        <?= ($maxP < $unlimited) ? "({$prodPct}%)" : '' ?>
                    </span>
                </div>
                <div class="sub-track">
                    <div class="sub-fill <?= $prodFill ?>"
                         style="width:<?= $maxP >= $unlimited ? min(10, $usedProducts) : $prodPct ?>%"></div>
                </div>
            </div>
            <!-- Services -->
            <div class="sub-usage-row">
                <div class="sub-usage-label-row">
                    <span><i class="bi bi-tools me-1"></i> Services</span>
                    <span>
                        <?= $usedServices ?> /
                        <?= ($maxS >= $unlimited) ? '∞' : $maxS ?>
                        <?= ($maxS < $unlimited) ? "({$svcPct}%)" : '' ?>
                    </span>
                </div>
                <div class="sub-track">
                    <div class="sub-fill <?= $svcFill ?>"
                         style="width:<?= $maxS >= $unlimited ? min(10, $usedServices) : $svcPct ?>%"></div>
                </div>
            </div>
            <!-- Featured slots -->
            <div class="sub-usage-row">
                <div class="sub-usage-label-row">
                    <span><i class="bi bi-star me-1"></i> Featured Slots</span>
                    <span><?= $current->plan->featured_slots ?> available</span>
                </div>
                <div class="sub-track">
                    <div class="sub-fill" style="width:<?= $current->plan->featured_slots > 0 ? 100 : 0 ?>%;background:var(--acc,#6C5CE7);"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Stats strip — same .hero-card pattern as dashboard ── -->
    <div class="sub-stats-row">
        <div class="hero-card">
            <div class="hc-label">Plan Cost</div>
            <div class="hc-value" style="font-size:24px;">KES <?= number_format($current->plan->price_kes, 0) ?></div>
            <div class="hc-sub">per month</div>
        </div>
        <div class="hero-card">
            <div class="hc-label">Days Remaining</div>
            <div class="hc-value" style="color:<?= $isWarning ? '#f59e0b' : '#22c55e' ?>;"><?= $daysLeft ?></div>
            <div class="hc-sub">until <?= Yii::$app->formatter->asDate($current->end_date, 'php:d M Y') ?></div>
        </div>
        <div class="hero-card">
            <div class="hc-label">Featured Slots</div>
            <div class="hc-value" style="color:var(--acc,#6C5CE7);"><?= $current->plan->featured_slots ?></div>
            <div class="hc-sub">boost your listings</div>
        </div>
    </div>

<?php else: ?>

    <div class="sub-no-plan-alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>You don't have an active subscription. Customers cannot see your listings. Subscribe to a plan below to start selling.</span>
    </div>

<?php endif; ?>

<!-- ── Choose a plan ── -->
<div class="sub-section-head"><i class="bi bi-grid-1x2"></i> Choose a Plan</div>

<div class="sub-plan-grid">
    <?php foreach ($plans as $plan):
        $isCurrent = $current && $current->plan_id === $plan->id;
    ?>
    <div class="sub-plan-card <?= $plan->is_popular ? 'plan-popular' : '' ?> <?= $isCurrent ? 'plan-current' : '' ?>">

        <?php if ($isCurrent): ?>
            <div class="sub-plan-ribbon ribbon-current"><i class="bi bi-check2"></i> Current Plan</div>
        <?php elseif ($plan->is_popular): ?>
            <div class="sub-plan-ribbon ribbon-popular">⚡ Most Popular</div>
        <?php endif; ?>

        <div class="sub-plan-body">
            <div class="sub-plan-nm"><?= Html::encode($plan->name) ?></div>
            <div class="sub-plan-price-big">
                KES <?= number_format($plan->price_kes, 0) ?>
                <small>/mo</small>
            </div>
            <p class="sub-plan-desc-sm"><?= Html::encode($plan->description) ?></p>
            <ul class="sub-plan-features">
                <li><i class="bi bi-check-circle-fill"></i>
                    <?= $plan->hasUnlimitedProducts() ? '<strong>Unlimited</strong>' : "<strong>{$plan->max_products}</strong>" ?> Products
                </li>
                <li><i class="bi bi-check-circle-fill"></i>
                    <?= $plan->hasUnlimitedServices() ? '<strong>Unlimited</strong>' : "<strong>{$plan->max_services}</strong>" ?> Services
                </li>
                <li><i class="bi bi-check-circle-fill"></i>
                    <strong><?= $plan->featured_slots ?></strong> Featured Slot<?= $plan->featured_slots !== 1 ? 's' : '' ?>
                </li>
                <li><i class="bi bi-check-circle-fill"></i> Verified Provider Badge</li>
                <li><i class="bi bi-check-circle-fill"></i> M-Pesa Payments</li>
            </ul>
        </div>

        <div class="sub-plan-foot">
            <form action="<?= Url::to(['/provider/subscribe-plan', 'planId' => $plan->id]) ?>" method="post">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <?php if ($isCurrent): ?>
                    <button type="submit" class="btn-hl-green w-100" style="justify-content:center;">
                        <i class="bi bi-arrow-clockwise"></i> Renew Plan
                    </button>
                <?php elseif ($plan->is_popular): ?>
                    <button type="submit" class="btn-hl-primary w-100" style="justify-content:center;">
                        <i class="bi bi-phone-fill"></i> Subscribe via M-Pesa
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn-hl-outline w-100" style="justify-content:center;">
                        <i class="bi bi-phone-fill"></i> Subscribe via M-Pesa
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Plan comparison table ── -->
<div class="sub-section-head"><i class="bi bi-table"></i> Plan Comparison</div>

<div class="sub-compare-wrap" style="margin-bottom:16px;">
    <table class="sub-compare-table">
        <thead>
            <tr>
                <th>Feature</th>
                <?php foreach ($plans as $plan): ?>
                <th <?= $plan->is_popular ? 'class="hi"' : '' ?>>
                    <?= Html::encode($plan->name) ?>
                    <?php if ($plan->is_popular): ?><br><small style="font-weight:400;text-transform:none;color:var(--text3);">Popular</small><?php endif; ?>
                </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Price</td>
                <?php foreach ($plans as $plan): ?><td <?= $plan->is_popular ? 'class="hi"' : '' ?>><strong>KES <?= number_format($plan->price_kes, 0) ?></strong></td><?php endforeach; ?>
            </tr>
            <tr>
                <td>Products</td>
                <?php foreach ($plans as $plan): ?><td <?= $plan->is_popular ? 'class="hi"' : '' ?>><?= $plan->hasUnlimitedProducts() ? '<span style="font-size:1.1rem;">∞</span>' : $plan->max_products ?></td><?php endforeach; ?>
            </tr>
            <tr>
                <td>Services</td>
                <?php foreach ($plans as $plan): ?><td <?= $plan->is_popular ? 'class="hi"' : '' ?>><?= $plan->hasUnlimitedServices() ? '<span style="font-size:1.1rem;">∞</span>' : $plan->max_services ?></td><?php endforeach; ?>
            </tr>
            <tr>
                <td>Featured Slots</td>
                <?php foreach ($plans as $plan): ?><td <?= $plan->is_popular ? 'class="hi"' : '' ?>><?= $plan->featured_slots ?></td><?php endforeach; ?>
            </tr>
            <tr>
                <td>Verified Badge</td>
                <?php foreach ($plans as $plan): ?><td <?= $plan->is_popular ? 'class="hi"' : '' ?>><i class="bi bi-check-lg chk"></i></td><?php endforeach; ?>
            </tr>
            <tr>
                <td>M-Pesa Payments</td>
                <?php foreach ($plans as $plan): ?><td <?= $plan->is_popular ? 'class="hi"' : '' ?>><i class="bi bi-check-lg chk"></i></td><?php endforeach; ?>
            </tr>
            <tr>
                <td>Priority Support</td>
                <?php foreach ($plans as $pi => $plan): ?>
                <td <?= $plan->is_popular ? 'class="hi"' : '' ?>>
                    <?= $pi >= 1 ? '<i class="bi bi-check-lg chk"></i>' : '<i class="bi bi-dash crs"></i>' ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td>Analytics Dashboard</td>
                <?php foreach ($plans as $pi => $plan): ?>
                <td <?= $plan->is_popular ? 'class="hi"' : '' ?>>
                    <?= $pi >= 2 ? '<i class="bi bi-check-lg chk"></i>' : '<i class="bi bi-dash crs"></i>' ?>
                </td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>

<!-- ── Payment history ── -->
<div class="sub-section-head"><i class="bi bi-receipt"></i> Payment History</div>

<div class="hl-card" style="margin-bottom:16px;">
    <div class="hl-card-body" style="padding: 8px 16px;">
        <?php if (!empty($payments)): ?>
            <?php foreach ($payments as $p):
                $statusClass = $p->status === 'completed' ? 'paid' : ($p->status === 'pending' ? 'pending' : 'failed');
                $icon = $p->status === 'completed' ? 'check-lg' : ($p->status === 'pending' ? 'clock' : 'x-lg');
            ?>
            <div class="sub-pay-item">
                <div class="sub-pay-icon <?= $statusClass ?>">
                    <i class="bi bi-<?= $icon ?>"></i>
                </div>
                <div>
                    <div class="sub-pay-ref">
                        <?= $p->mpesa_receipt ? Html::encode($p->mpesa_receipt) : 'Payment #' . $p->id ?>
                    </div>
                    <div class="sub-pay-meta">
                        <?= Yii::$app->formatter->asDatetime($p->created_at, 'php:d M Y \a\t g:ia') ?>
                        · <?= ucfirst($p->stageLabel()) ?> · <?= ucfirst($p->methodLabel()) ?>
                    </div>
                </div>
                <div class="sub-pay-amount">KES <?= number_format($p->amount, 2) ?></div>
                <span class="sub-pay-status <?= $statusClass ?>"><?= ucfirst($p->status) ?></span>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:var(--text3); font-size:12px; padding: 8px 0;">No payment records found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- ── Danger zone ── -->
<?php if ($current): ?>
<div class="sub-section-head" style="color:#ef4444;"><i class="bi bi-exclamation-triangle"></i> Danger Zone</div>
<div class="sub-danger-box">
    <div>
        <h6>Cancel Subscription</h6>
        <p>Your plan stays active until <?= Yii::$app->formatter->asDate($current->end_date, 'php:d M Y') ?>. After that your listings will be hidden and orders will stop.</p>
    </div>
    <form action="<?= Url::to(['/provider/cancel-subscription']) ?>" method="post">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <button type="submit"
                class="btn-hl-outline"
                style="border-color:#ef4444; color:#ef4444; white-space:nowrap;"
                onclick="return confirm('Cancel subscription? Your listings will be hidden at the end of the billing period.')">
            <i class="bi bi-x-circle"></i> Cancel Subscription
        </button>
    </form>
</div>
<?php endif; ?>