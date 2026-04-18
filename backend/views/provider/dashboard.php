<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $stats */
/** @var array $trends */
/** @var array $recentOrders */
/** @var array $recentReviews */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
?>

<style>
.hero-cards { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.hero-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px 20px 14px; }
.hero-card .hc-label { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .07em; font-weight: 600; margin-bottom: 6px; }
.hero-card .hc-value { font-size: 32px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; margin-bottom: 5px; color: var(--text1); }
.hero-card .hc-sub { font-size: 12px; color: var(--text3); margin-bottom: 12px; }
.hero-card .hc-link { font-size: 12px; font-weight: 600; color: var(--acc); text-decoration: none; }
.hero-card .hc-link:hover { text-decoration: underline; }
.mid-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 12px; margin-bottom: 16px; }
.order-item { display: flex; align-items: center; gap: 11px; padding: 9px 0; border-bottom: 1px solid var(--border); }
.order-item:last-child { border-bottom: none; }
.order-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; flex-shrink: 0; }
.order-name { font-size: 13px; font-weight: 600; color: var(--text1); }
.order-details { font-size: 11px; color: var(--text3); }
.order-status { margin-left: auto; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 12px; }
.bot-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.review-item { padding: 12px 0; border-bottom: 1px solid var(--border); }
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.review-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: #fff; }
.review-name { font-size: 12px; font-weight: 600; color: var(--text1); }
.review-rating { font-size: 11px; color: var(--text3); margin-left: auto; }
.review-text { font-size: 12px; color: var(--text2); line-height: 1.4; }
.history-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); }
.history-item:last-child { border-bottom: none; }
.history-label { font-size: 12px; color: var(--text2); }
.history-value { font-size: 13px; font-weight: 600; color: var(--text1); }
@media (max-width: 900px) { .hero-cards { grid-template-columns: repeat(2, 1fr); } .mid-row { grid-template-columns: 1fr; } .bot-row { grid-template-columns: 1fr; } }
</style>

<div class="hl-pg-head">
    <h1 class="hl-pg-title">Welcome back, <?= Html::encode($provider->business_name) ?></h1>
    <div class="hl-pg-sub"><?= date('l, d F Y') ?></div>
</div>

<div class="hero-cards">
    <div class="hero-card">
        <div class="hc-label">Total Earnings</div>
        <div class="hc-value" id="hc-balance" data-value="<?= $stats['balance'] ?>">KSh 0</div>
        <div class="hc-sub">Paid orders</div>
        <a class="hc-link" href="<?= Url::to(['/provider/orders']) ?>">View all →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Active Listings</div>
        <div class="hc-value" id="hc-listings" data-value="<?= $stats['active_listings'] ?>">0</div>
        <div class="hc-sub">Published</div>
        <a class="hc-link" href="<?= Url::to(['/provider/listings']) ?>">Manage →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Total Orders</div>
        <div class="hc-value" id="hc-orders" data-value="<?= $stats['total_orders'] ?>">0</div>
        <div class="hc-sub">All time</div>
        <a class="hc-link" href="<?= Url::to(['/provider/orders']) ?>">View →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Average Rating</div>
        <div class="hc-value" id="hc-rating" data-value="<?= number_format($stats['avg_rating'], 1) ?>">★ 0.0</div>
        <div class="hc-sub">Feedback</div>
        <a class="hc-link" href="<?= Url::to(['/provider/reviews']) ?>">Reviews →</a>
    </div>
</div>

<div class="mid-row">
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Earnings (Last 6 Months)</span></div>
        <div class="hl-card-body"><div id="earningsChart"></div></div>
    </div>
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Recent Orders</span></div>
        <div class="hl-card-body" style="padding: 8px 16px;">
            <?php if ($recentOrders): ?>
                <?php foreach ($recentOrders as $order): ?>
                    <div class="order-item">
                        <div class="order-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><?= strtoupper(substr($order->user->getFullName(), 0, 1)) ?></div>
                        <div>
                            <div class="order-name"><?= Html::encode($order->user->getFullName()) ?></div>
                            <div class="order-details">KSh <?= number_format($order->total_amount, 0) ?> • <?= date('M d', strtotime($order->created_at)) ?></div>
                        </div>
                        <div class="order-status" style="background: rgba(34,197,94,.1); color: #22c55e;"><?= ucfirst(str_replace('_', ' ', $order->status)) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text3); font-size: 12px;">No recent orders</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="bot-row">
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Recent Reviews</span></div>
        <div class="hl-card-body" style="padding: 8px 16px;">
            <?php if ($recentReviews): ?>
                <?php foreach ($recentReviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"><?= strtoupper(substr($review->user->getFullName(), 0, 1)) ?></div>
                            <div class="review-name"><?= Html::encode($review->user->getFullName()) ?></div>
                            <div class="review-rating">★ <?= $review->rating ?>/5</div>
                        </div>
                        <div class="review-text"><?= Html::encode($review->comment) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text3); font-size: 12px;">No reviews yet</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Quick Stats</span></div>
        <div class="hl-card-body" style="padding: 8px 16px;">
            <div class="history-item">
                <span class="history-label">This Month</span>
                <span class="history-value" id="qs-month" data-value="<?= $trends['earnings'][5] ?? 0 ?>">KSh 0</span>
            </div>
            <div class="history-item">
                <span class="history-label">Pending</span>
                <span class="history-value" id="qs-pending" data-value="<?= \common\models\Order::find()->where(['provider_id' => $provider->id, 'status' => 'pending'])->count() ?>">0</span>
            </div>
            <div class="history-item">
                <span class="history-label">Completed</span>
                <span class="history-value" id="qs-completed" data-value="<?= \common\models\Order::find()->where(['provider_id' => $provider->id, 'status' => 'completed'])->count() ?>">0</span>
            </div>
            <div class="history-item">
                <span class="history-label">Subscription</span>
                <span class="history-value"><?= \common\models\Subscription::find()->where(['provider_id' => $provider->id, 'status' => 'active'])->one() ? 'Active' : 'Inactive' ?></span>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJsFile('https://cdn.jsdelivr.net/npm/apexcharts'); ?>

<?php $this->registerJs("
document.addEventListener('DOMContentLoaded', function () {

    // ── Utility: ease-out cubic count-up ──────────────────────────────────────
    function countUp(el, target, duration, format) {
        var startTime = null;
        function step(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var ease     = 1 - Math.pow(1 - progress, 3);
            el.textContent = format(target * ease);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ── Hero cards ────────────────────────────────────────────────────────────
    var balanceEl = document.getElementById('hc-balance');
    if (balanceEl) countUp(balanceEl, parseFloat(balanceEl.dataset.value) || 0, 1400, function(v) {
        return 'KSh ' + Math.round(v).toLocaleString('en-KE');
    });

    var listingsEl = document.getElementById('hc-listings');
    if (listingsEl) countUp(listingsEl, parseInt(listingsEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toString();
    });

    var ordersEl = document.getElementById('hc-orders');
    if (ordersEl) countUp(ordersEl, parseInt(ordersEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toString();
    });

    var ratingEl = document.getElementById('hc-rating');
    if (ratingEl) countUp(ratingEl, parseFloat(ratingEl.dataset.value) || 0, 1200, function(v) {
        return '\u2605 ' + v.toFixed(1);
    });

    // ── Quick stats ───────────────────────────────────────────────────────────
    var monthEl = document.getElementById('qs-month');
    if (monthEl) countUp(monthEl, parseFloat(monthEl.dataset.value) || 0, 1300, function(v) {
        return 'KSh ' + Math.round(v).toLocaleString('en-KE');
    });

    var pendingEl = document.getElementById('qs-pending');
    if (pendingEl) countUp(pendingEl, parseInt(pendingEl.dataset.value) || 0, 900, function(v) {
        return Math.round(v).toString();
    });

    var completedEl = document.getElementById('qs-completed');
    if (completedEl) countUp(completedEl, parseInt(completedEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toString();
    });

    // ── Earnings chart — ApexCharts (mirrors analytics page setup) ────────────
    var isDark    = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    var tickColor = isDark ? '#55556A' : '#9898B8';

    new ApexCharts(document.querySelector('#earningsChart'), {
        series: [{
            name: 'Earnings (KSh)',
            data: " . json_encode(array_values($trends['earnings'])) . "
        }],
        chart: {
            type:       'area',
            height:     220,
            toolbar:    { show: false },
            background: 'transparent',
            zoom:       { enabled: false },
            animations: {
                enabled:          true,
                easing:           'easeinout',
                speed:            900,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 450 }
            }
        },
        colors:     ['#6C5CE7'],
        stroke:     { curve: 'smooth', width: 2.5 },
        fill: {
            type:     'gradient',
            gradient: { opacityFrom: 0.3, opacityTo: 0.0 }
        },
        dataLabels: { enabled: false },
        grid: {
            borderColor:     gridColor,
            strokeDashArray: 4
        },
        xaxis: {
            categories: " . json_encode(array_values($trends['months'])) . ",
            axisBorder: { show: false },
            axisTicks:  { show: false },
            labels:     { style: { colors: tickColor, fontSize: '10px' } }
        },
        yaxis: {
            labels: {
                style:     { colors: tickColor, fontSize: '10px' },
                formatter: function(v) { return 'KSh ' + v.toLocaleString(); }
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: function(v) { return 'KSh ' + v.toLocaleString(); } }
        },
        markers: {
            size:        4,
            colors:      ['#6C5CE7'],
            strokeWidth: 0
        },
        legend: { show: false },
        theme:  { mode: isDark ? 'dark' : 'light' }
    }).render();

});
", \yii\web\View::POS_END); ?>