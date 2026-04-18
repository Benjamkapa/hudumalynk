<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $stats */
/** @var array $monthlyRevenue */
/** @var array $statusCounts */
/** @var common\models\Listing[] $topListings */

use yii\helpers\Html;

$this->title = 'Analytics & Insights';
?>

<style>
.pa-shell { display: flex; flex-direction: column; gap: 16px; }
.pa-title { font-size: 24px; font-weight: 800; color: var(--text1); letter-spacing: -.03em; }
.pa-sub { font-size: 12px; color: var(--text3); margin-top: 4px; line-height: 1.5; }
.pa-stats { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
.pa-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 15px; }
.pa-card .lbl { font-size: 10px; font-weight: 700; color: var(--text3); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 5px; }
.pa-card .val { font-size: 24px; font-weight: 800; color: var(--text1); }
.pa-grid { display: grid; grid-template-columns: 1.2fr .9fr; gap: 16px; }
.pa-status { display: flex; flex-direction: column; gap: 10px; padding: 16px; }
.pa-status-row { display: grid; grid-template-columns: 110px 1fr auto; gap: 10px; align-items: center; font-size: 12px; }
.pa-status-track { height: 10px; border-radius: 999px; background: var(--bg3); overflow: hidden; }
.pa-status-fill { height: 100%; border-radius: 999px; background: var(--teal); width: 0; transition: width .8s cubic-bezier(.34,1.2,.64,1); }
@media (max-width: 980px) { .pa-stats { grid-template-columns: 1fr 1fr; } .pa-grid { grid-template-columns: 1fr; } }
@media (max-width: 560px) { .pa-stats { grid-template-columns: 1fr; } }
</style>

<div class="pa-shell">
    <div>
        <div class="pa-title">Analytics & Insights</div>
        <div class="pa-sub">Performance overview for <?= Html::encode($provider->business_name) ?> across orders, revenue, customer satisfaction, and listing activity.</div>
    </div>

    <div class="pa-stats">
        <div class="pa-card">
            <div class="lbl">Orders</div>
            <div class="val" id="pa-orders" data-value="<?= $stats['orders_total'] ?? 0 ?>">0</div>
        </div>
        <div class="pa-card">
            <div class="lbl">Completed</div>
            <div class="val" id="pa-completed" data-value="<?= $stats['completed_orders'] ?? 0 ?>">0</div>
        </div>
        <div class="pa-card">
            <div class="lbl">Gross Revenue</div>
            <div class="val" id="pa-revenue" data-value="<?= (float)($stats['gross_revenue'] ?? 0) ?>">KES 0</div>
        </div>
        <div class="pa-card">
            <div class="lbl">Average Rating</div>
            <div class="val" id="pa-rating" data-value="<?= number_format((float)($stats['avg_rating'] ?? 0), 1) ?>">0.0/5</div>
        </div>
    </div>

    <div class="pa-grid">
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Revenue Trend</span></div>
            <div id="paRevenueChart" style="height:240px;"></div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="hl-card">
                <div class="hl-card-head"><span class="hl-card-title">Order Pipeline</span></div>
                <div class="pa-status">
                    <?php $maxStatus = max($statusCounts) ?: 1; ?>
                    <?php foreach ($statusCounts as $status => $count):
                        $barPct = max(8, (int) round(($count / $maxStatus) * 100));
                    ?>
                    <div class="pa-status-row">
                        <span><?= Html::encode(ucfirst(str_replace('_', ' ', $status))) ?></span>
                        <div class="pa-status-track">
                            <div class="pa-status-fill" data-width="<?= $barPct ?>"></div>
                        </div>
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

<?php $this->registerJsFile('https://cdn.jsdelivr.net/npm/apexcharts'); ?>
<?php $this->registerJs("
document.addEventListener('DOMContentLoaded', function () {

    // ── Count-up utility ─────────────────────────────────────────────────────
    function countUp(el, target, duration, format) {
        var startTime = null;
        function step(ts) {
            if (!startTime) startTime = ts;
            var p = Math.min((ts - startTime) / duration, 1);
            var ease = 1 - Math.pow(1 - p, 3);
            el.textContent = format(target * ease);
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ── Stat cards ───────────────────────────────────────────────────────────
    var ordersEl = document.getElementById('pa-orders');
    if (ordersEl) countUp(ordersEl, parseInt(ordersEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toLocaleString('en-KE');
    });

    var completedEl = document.getElementById('pa-completed');
    if (completedEl) countUp(completedEl, parseInt(completedEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toLocaleString('en-KE');
    });

    var revenueEl = document.getElementById('pa-revenue');
    if (revenueEl) countUp(revenueEl, parseFloat(revenueEl.dataset.value) || 0, 1400, function(v) {
        return 'KES ' + Math.round(v).toLocaleString('en-KE');
    });

    var ratingEl = document.getElementById('pa-rating');
    if (ratingEl) countUp(ratingEl, parseFloat(ratingEl.dataset.value) || 0, 1200, function(v) {
        return v.toFixed(1) + '/5';
    });

    // ── Animate pipeline progress bars ───────────────────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.pa-status-fill').forEach(function (el) {
            el.style.width = (el.dataset.width || 0) + '%';
        });
    }, 200);

    // ── Revenue trend — ApexCharts bar ───────────────────────────────────────
    var isDark    = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    var tickColor = isDark ? '#55556A' : '#9898B8';

    var months   = " . json_encode(array_column($monthlyRevenue, 'label')) . ";
    var revenues = " . json_encode(array_column($monthlyRevenue, 'revenue')) . ";

    new ApexCharts(document.querySelector('#paRevenueChart'), {
        series: [{ name: 'Revenue (KES)', data: revenues }],
        chart: {
            type: 'bar', height: 240,
            toolbar: { show: false }, background: 'transparent', zoom: { enabled: false },
            animations: {
                enabled: true, easing: 'easeinout', speed: 800,
                animateGradually: { enabled: true, delay: 100 }
            }
        },
        colors: ['#6C5CE7'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        xaxis: {
            categories: months,
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: tickColor, fontSize: '10px' } }
        },
        yaxis: {
            labels: {
                style: { colors: tickColor, fontSize: '10px' },
                formatter: function(v) { return 'KES ' + (v/1000).toFixed(0) + 'K'; }
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: function(v) { return 'KES ' + v.toLocaleString(); } }
        },
        theme: { mode: isDark ? 'dark' : 'light' }
    }).render();

});
", \yii\web\View::POS_END); ?>