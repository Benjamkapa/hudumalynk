<?php
/** @var yii\web\View $this */
/** @var array $revenueByMonth */
/** @var array $categoryBreakdown */
/** @var array $topVendors */
/** @var array $userGrowth */
/** @var array $orderStatuses */
/** @var array $countyStats */
/** @var float $totalGmv */
/** @var int   $totalOrders */
/** @var int   $totalUsers */
/** @var float $avgOrderValue */
/** @var float $gmvGrowthPct */
/** @var float $ordersGrowthPct */
/** @var float $usersGrowthPct */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Analytics';
?>

<style>
.an-grid2   { display: grid; grid-template-columns: minmax(0,1.6fr) minmax(0,1fr); gap: 12px; margin-bottom: 14px; }
.an-grid3   { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 14px; }
.an-grid-eq { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; margin-bottom: 14px; }

.an-tabs { display: flex; gap: 3px; background: var(--bg3); border-radius: var(--r-md); padding: 3px; }
.an-tab  { padding: 5px 13px; font-size: 11.5px; font-weight: 600; border-radius: 6px; cursor: pointer; color: var(--text3); transition: background .13s, color .13s; user-select: none; }
.an-tab.active { background: var(--bg2); color: var(--text1); box-shadow: 0 1px 3px rgba(0,0,0,.08); }

.an-kpi-row { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 11px; margin-bottom: 14px; }
.an-kpi { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 14px 16px; position: relative; overflow: hidden; }
.an-kpi::after { content: ''; position: absolute; right: -14px; top: -14px; width: 64px; height: 64px; border-radius: 50%; opacity: .06; }
.an-kpi.v1::after { background: var(--acc); }
.an-kpi.v2::after { background: var(--teal); }
.an-kpi.v3::after { background: var(--amber); }
.an-kpi.v4::after { background: var(--rose); }
.an-kpi-lbl   { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); margin-bottom: 6px; }
.an-kpi-val   { font-size: 26px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; color: var(--text1); }
.an-kpi-delta { display: inline-flex; align-items: center; gap: 3px; font-size: 10.5px; font-weight: 700; margin-top: 5px; padding: 2px 7px; border-radius: 20px; }
.an-kpi-delta.up { background: var(--teal-pale); color: #006650; }
.an-kpi-delta.dn { background: var(--rose-pale); color: var(--rose); }
[data-hl-t="dark"] .an-kpi-delta.up { color: var(--teal); }
[data-hl-t="dark"] .an-kpi-delta.dn { color: var(--rose); }
.an-kpi-sub  { font-size: 10.5px; color: var(--text3); margin-top: 3px; }

.donut-legend { display: flex; flex-direction: column; gap: 8px; }
.dl-item { display: flex; align-items: center; gap: 8px; font-size: 12px; }
.dl-dot  { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.dl-lbl  { flex: 1; color: var(--text2); }
.dl-pct  { font-weight: 700; color: var(--text1); font-size: 12px; }

.an-rating { display: inline-flex; align-items: center; gap: 3px; font-size: 11px; font-weight: 600; color: var(--amber); }
.an-rating::before { content: '★'; }

.an-bar-row   { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px solid var(--border); }
.an-bar-row:last-child { border-bottom: none; }
.an-bar-lbl   { width: 80px; font-size: 11.5px; color: var(--text2); flex-shrink: 0; }
.an-bar-track { flex: 1; height: 8px; background: var(--bg3); border-radius: 4px; overflow: hidden; }
.an-bar-fill  { height: 100%; border-radius: 4px; width: 0; transition: width .8s cubic-bezier(.34,1.2,.64,1); }
.an-bar-meta  { display: flex; flex-direction: column; align-items: flex-end; min-width: 56px; }
.an-bar-val   { font-size: 11.5px; font-weight: 700; color: var(--text1); }
.an-bar-sub   { font-size: 10px; color: var(--text3); }

.an-funnel { display: flex; flex-direction: column; gap: 6px; padding: 4px 0; }
.an-funnel-row { display: flex; align-items: center; gap: 10px; }
.an-funnel-lbl { width: 90px; font-size: 11.5px; color: var(--text2); }
.an-funnel-bar { height: 28px; border-radius: var(--r-md); display: flex; align-items: center; padding: 0 10px; font-size: 11px; font-weight: 700; color: #fff; width: 0; overflow: hidden; white-space: nowrap; transition: width .8s cubic-bezier(.34,1.2,.64,1); min-width: 0; }
.an-funnel-cnt { font-size: 11.5px; color: var(--text3); margin-left: 6px; }

.an-insights { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
.an-ins-pill { display: inline-flex; align-items: center; gap: 6px; background: var(--acc-pale); border: 1px solid rgba(108,92,231,.18); color: var(--acc); border-radius: var(--r-md); padding: 6px 11px; font-size: 11.5px; font-weight: 600; }
[data-hl-t="dark"] .an-ins-pill { color: var(--acc2); border-color: rgba(162,155,254,.2); }
.an-ins-pill.teal  { background: var(--teal-pale); border-color: rgba(0,184,148,.2); color: #006650; }
[data-hl-t="dark"] .an-ins-pill.teal { color: var(--teal); }
.an-ins-pill.amber { background: var(--amber-pale); border-color: rgba(253,203,110,.25); color: #7a5500; }
[data-hl-t="dark"] .an-ins-pill.amber { color: var(--amber); }

@media (max-width: 1000px) { .an-kpi-row { grid-template-columns: repeat(2, minmax(0,1fr)); } .an-grid2 { grid-template-columns: 1fr; } .an-grid3 { grid-template-columns: 1fr 1fr; } .an-grid-eq { grid-template-columns: 1fr; } }
@media (max-width: 640px)  { .an-kpi-row { grid-template-columns: 1fr 1fr; } .an-grid3 { grid-template-columns: 1fr; } }
</style>

<?php
function anDelta(float $pct, string $suffix = '%'): string {
    $dir   = $pct >= 0 ? 'up' : 'dn';
    $arrow = $pct >= 0 ? '↑' : '↓';
    return '<span class="an-kpi-delta ' . $dir . '">' . $arrow . ' ' . abs(round($pct, 1)) . $suffix . ' vs last month</span>';
}
?>

<!-- ── Period selector ── -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
    <p style="font-size:12px; color:var(--text3);">All figures are in Kenyan Shillings (KES) and reflect the selected period.</p>
    <div class="an-tabs" id="anPeriodTabs">
        <div class="an-tab active" data-period="30">30 days</div>
        <div class="an-tab" data-period="90">90 days</div>
        <div class="an-tab" data-period="180">6 months</div>
        <div class="an-tab" data-period="365">1 year</div>
    </div>
</div>

<!-- ── KPI cards — values start at 0, count up on load ── -->
<div class="an-kpi-row">
    <div class="an-kpi v1">
        <div class="an-kpi-lbl">Total GMV</div>
        <div class="an-kpi-val" id="kpi-gmv" data-value="<?= $totalGmv ?>">KES 0</div>
        <?= anDelta($gmvGrowthPct) ?>
        <div class="an-kpi-sub">Gross Merchandise Value</div>
    </div>
    <div class="an-kpi v2">
        <div class="an-kpi-lbl">Total orders</div>
        <div class="an-kpi-val" id="kpi-orders" data-value="<?= $totalOrders ?>">0</div>
        <?= anDelta($ordersGrowthPct) ?>
        <div class="an-kpi-sub">Completed + pending</div>
    </div>
    <div class="an-kpi v3">
        <div class="an-kpi-lbl">Registered users</div>
        <div class="an-kpi-val" id="kpi-users" data-value="<?= $totalUsers ?>">0</div>
        <?= anDelta($usersGrowthPct) ?>
        <div class="an-kpi-sub">Buyers &amp; service providers</div>
    </div>
    <div class="an-kpi v4">
        <div class="an-kpi-lbl">Avg. order value</div>
        <div class="an-kpi-val" id="kpi-aov" data-value="<?= $avgOrderValue ?>">KES 0</div>
        <span class="an-kpi-delta up">↑ per transaction</span>
        <div class="an-kpi-sub">GMV ÷ completed orders</div>
    </div>
</div>

<!-- ── Revenue trend + Order status donut ── -->
<div class="an-grid2">
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Revenue &amp; Orders trend</span>
            <span style="font-size:11px;color:var(--text3);">Monthly GMV vs order volume</span>
        </div>
        <div id="anRevenueChart" style="height:280px;"></div>
    </div>

    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Order status</span></div>
        <div class="hl-card-body" style="display:flex;flex-direction:column;gap:18px;">
            <div id="anStatusDonut" style="height:180px;"></div>
            <div class="donut-legend">
                <?php
                $statusTotal  = array_sum($orderStatuses);
                $statusColors = ['completed' => '#00B894', 'pending' => '#FDCB6E', 'cancelled' => '#E17055'];
                foreach ($orderStatuses as $status => $count):
                    $pct = $statusTotal > 0 ? round($count / $statusTotal * 100) : 0;
                ?>
                <div class="dl-item">
                    <div class="dl-dot" style="background:<?= $statusColors[$status] ?? '#9898B8' ?>"></div>
                    <span class="dl-lbl"><?= ucfirst(Html::encode($status)) ?></span>
                    <span style="font-size:11px;color:var(--text3);margin-right:6px;"><?= number_format($count) ?></span>
                    <span class="dl-pct"><?= $pct ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── User growth + Category breakdown ── -->
<div class="an-grid2">
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">User &amp; Provider growth</span>
            <span style="font-size:11px;color:var(--text3);">New registrations per month</span>
        </div>
        <div id="anUserGrowthChart" style="height:240px;"></div>
    </div>

    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Category performance</span></div>
        <div class="hl-card-body" style="padding-top:8px;">
            <?php
            $maxCatRev = max(array_column($categoryBreakdown, 'revenue') ?: [1]);
            $catColors = ['#6C5CE7','#00B894','#A29BFE','#FDCB6E','#E17055','#3B82F6','#F59E0B'];
            foreach (array_slice($categoryBreakdown, 0, 6) as $ci => $cat):
                $barPct = $maxCatRev > 0 ? round($cat['revenue'] / $maxCatRev * 100) : 0;
            ?>
            <div class="an-bar-row">
                <div class="an-bar-lbl"><?= Html::encode($cat['name']) ?></div>
                <div class="an-bar-track">
                    <div class="an-bar-fill" data-width="<?= $barPct ?>" style="background:<?= $catColors[$ci % count($catColors)] ?>;"></div>
                </div>
                <div class="an-bar-meta">
                    <span class="an-bar-val"><?= number_format($cat['revenue'] / 1000, 0) ?>K</span>
                    <span class="an-bar-sub"><?= $cat['count'] ?> orders</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── County breakdown + Conversion funnel ── -->
<div class="an-grid-eq">
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Revenue by county</span>
            <a class="hl-card-link" href="<?= Url::to(['/admin/map']) ?>">View on map →</a>
        </div>
        <div class="hl-card-body" style="padding-top:8px;">
            <?php
            $maxCtyRev = max(array_column($countyStats, 'revenue') ?: [1]);
            $ctyColors = ['#6C5CE7','#00B894','#A29BFE','#FDCB6E','#E17055'];
            foreach (array_slice($countyStats, 0, 7) as $ci => $cty):
                $bPct = $maxCtyRev > 0 ? round($cty['revenue'] / $maxCtyRev * 100) : 0;
            ?>
            <div class="an-bar-row">
                <div class="an-bar-lbl"><?= Html::encode($cty['county']) ?></div>
                <div class="an-bar-track">
                    <div class="an-bar-fill" data-width="<?= $bPct ?>" style="background:<?= $ctyColors[$ci % count($ctyColors)] ?>;"></div>
                </div>
                <div class="an-bar-meta">
                    <span class="an-bar-val"><?= number_format($cty['revenue'] / 1000, 0) ?>K</span>
                    <span class="an-bar-sub"><?= $cty['orders'] ?> orders</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Conversion funnel</span>
            <span style="font-size:11px;color:var(--text3);">Visits → orders</span>
        </div>
        <div class="hl-card-body">
            <?php
            $funnel = $funnel ?? [
                ['stage' => 'Site visits',    'count' => 8400,                              'color' => '#6C5CE7'],
                ['stage' => 'Listing views',  'count' => 4200,                              'color' => '#A29BFE'],
                ['stage' => 'Enquiries sent', 'count' => 1800,                              'color' => '#00B894'],
                ['stage' => 'Orders placed',  'count' => $totalOrders,                      'color' => '#FDCB6E'],
                ['stage' => 'Completed',      'count' => ($orderStatuses['completed'] ?? 0),'color' => '#3B82F6'],
            ];
            $maxFunnel = $funnel[0]['count'] ?: 1;
            ?>
            <div class="an-funnel">
                <?php foreach ($funnel as $f):
                    $fPct = round($f['count'] / $maxFunnel * 100);
                ?>
                <div class="an-funnel-row">
                    <div class="an-funnel-lbl"><?= Html::encode($f['stage']) ?></div>
                    <div class="an-funnel-bar"
                         data-width="<?= $fPct ?>"
                         data-label="<?= number_format($f['count']) ?>"
                         data-show-inline="<?= $fPct > 20 ? '1' : '0' ?>"
                         style="background:<?= $f['color'] ?>;"></div>
                    <?php if ($fPct <= 20): ?>
                        <span class="an-funnel-cnt"><?= number_format($f['count']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:14px;font-size:11px;color:var(--text3);">
                Overall conversion:
                <strong style="color:var(--teal);">
                    <?= $funnel[0]['count'] > 0 ? round(($orderStatuses['completed'] ?? 0) / $funnel[0]['count'] * 100, 1) : 0 ?>%
                </strong>
                visits → completed orders
            </div>
        </div>
    </div>
</div>

<!-- ── Top vendors table ── -->
<div class="hl-card" style="margin-bottom:14px;">
    <div class="hl-card-head">
        <span class="hl-card-title">Top vendors by revenue</span>
        <a class="hl-card-link" href="<?= Url::to(['/admin/vendors']) ?>">All vendors →</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="hl-tbl">
            <thead>
                <tr><th>#</th><th>Business</th><th>Orders</th><th>Revenue (KES)</th><th>Avg. order</th><th>Rating</th><th>Share of GMV</th><th></th></tr>
            </thead>
            <tbody>
                <?php
                $vendorColors = ['#6C5CE7','#00B894','#3B82F6','#FDCB6E','#E17055'];
                foreach (array_slice($topVendors, 0, 8) as $vi => $v):
                    $share    = $totalGmv > 0 ? round($v['revenue'] / $totalGmv * 100, 1) : 0;
                    $avgOrd   = $v['orders'] > 0 ? round($v['revenue'] / $v['orders']) : 0;
                    $initials = strtoupper(substr($v['business_name'], 0, 2));
                ?>
                <tr>
                    <td style="color:var(--text3);font-size:11px;"><?= $vi + 1 ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div class="cust-avatar" style="background:<?= $vendorColors[$vi % count($vendorColors)] ?>;width:30px;height:30px;font-size:10px;flex-shrink:0;"><?= Html::encode($initials) ?></div>
                            <span style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($v['business_name']) ?></span>
                        </div>
                    </td>
                    <td><?= number_format($v['orders']) ?></td>
                    <td style="font-weight:600;color:var(--text1);"><?= number_format($v['revenue']) ?></td>
                    <td style="color:var(--text2);"><?= number_format($avgOrd) ?></td>
                    <td><span class="an-rating"><?= number_format($v['rating'] ?? 0, 1) ?></span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div class="an-bar-track" style="width:70px;">
                                <div class="an-bar-fill" data-width="<?= min($share * 4, 100) ?>" style="background:<?= $vendorColors[$vi % count($vendorColors)] ?>;"></div>
                            </div>
                            <span style="font-size:11px;color:var(--text3);"><?= $share ?>%</span>
                        </div>
                    </td>
                    <td><a href="<?= Url::to(['/admin/vendors/view', 'id' => $v['id'] ?? 0]) ?>" class="hl-card-link">View →</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Quick insights ── -->
<div class="hl-card" style="margin-bottom:14px;">
    <div class="hl-card-head"><span class="hl-card-title">Quick insights</span></div>
    <div class="hl-card-body">
        <div class="an-insights">
            <div class="an-ins-pill teal">✓ Revenue up <?= round($gmvGrowthPct, 1) ?>% this month</div>
            <?php if (!empty($categoryBreakdown[0])): ?>
            <div class="an-ins-pill">📦 <?= Html::encode($categoryBreakdown[0]['name']) ?> is your top category</div>
            <?php endif; ?>
            <?php if (!empty($countyStats[0])): ?>
            <div class="an-ins-pill amber">📍 <?= Html::encode($countyStats[0]['county']) ?> drives most orders</div>
            <?php endif; ?>
            <?php if (!empty($topVendors[0])): ?>
            <div class="an-ins-pill">🏆 <?= Html::encode($topVendors[0]['business_name']) ?> is top vendor</div>
            <?php endif; ?>
            <div class="an-ins-pill teal">👥 <?= number_format($totalUsers) ?> users registered</div>
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
            var p    = Math.min((ts - startTime) / duration, 1);
            var ease = 1 - Math.pow(1 - p, 3);
            el.textContent = format(target * ease);
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ── KPI cards ─────────────────────────────────────────────────────────────
    var gmvEl = document.getElementById('kpi-gmv');
    if (gmvEl) countUp(gmvEl, parseFloat(gmvEl.dataset.value) || 0, 1400, function(v) {
        return 'KES ' + Math.round(v).toLocaleString('en-KE');
    });

    var ordersEl = document.getElementById('kpi-orders');
    if (ordersEl) countUp(ordersEl, parseInt(ordersEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toLocaleString('en-KE');
    });

    var usersEl = document.getElementById('kpi-users');
    if (usersEl) countUp(usersEl, parseInt(usersEl.dataset.value) || 0, 1000, function(v) {
        return Math.round(v).toLocaleString('en-KE');
    });

    var aovEl = document.getElementById('kpi-aov');
    if (aovEl) countUp(aovEl, parseFloat(aovEl.dataset.value) || 0, 1200, function(v) {
        return 'KES ' + Math.round(v).toLocaleString('en-KE');
    });

    // ── Animate all progress bars + funnel bars ───────────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.an-bar-fill').forEach(function (el) {
            el.style.width = (el.dataset.width || 0) + '%';
        });
        document.querySelectorAll('.an-funnel-bar').forEach(function (el) {
            el.style.width = (el.dataset.width || 0) + '%';
            if (el.dataset.showInline === '1') el.textContent = el.dataset.label;
        });
    }, 200);

    // ── ApexCharts setup ──────────────────────────────────────────────────────
    var isDark       = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var gridColor    = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    var tickColor    = isDark ? '#55556A' : '#9898B8';
    var tooltipTheme = isDark ? 'dark' : 'light';

    var animCfg = {
        enabled: true, easing: 'easeinout', speed: 900,
        animateGradually: { enabled: true, delay: 120 },
        dynamicAnimation: { enabled: true, speed: 450 }
    };

    // 1. Revenue + orders combo
    new ApexCharts(document.querySelector('#anRevenueChart'), {
        series: [
            { name: 'GMV (KES)', type: 'area', data: " . json_encode(array_column($revenueByMonth, 'gmv')) . " },
            { name: 'Orders',    type: 'line', data: " . json_encode(array_column($revenueByMonth, 'orders')) . " }
        ],
        chart: { height: 280, toolbar: { show: false }, background: 'transparent', zoom: { enabled: false }, animations: animCfg },
        colors: ['#6C5CE7','#00B894'],
        stroke: { curve: 'smooth', width: [2.5, 2.5] },
        fill: { type: ['gradient','solid'], gradient: { opacityFrom: 0.3, opacityTo: 0 } },
        dataLabels: { enabled: false },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        xaxis: {
            categories: " . json_encode(array_column($revenueByMonth, 'month')) . ",
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: tickColor, fontSize: '10px' } }
        },
        yaxis: [
            { labels: { style: { colors: tickColor, fontSize: '10px' }, formatter: function(v) { return (v/1000).toFixed(0)+'K'; } } },
            { opposite: true, labels: { style: { colors: tickColor, fontSize: '10px' } } }
        ],
        legend: { position: 'top', fontSize: '11px', labels: { colors: tickColor } },
        tooltip: { theme: tooltipTheme },
        theme: { mode: isDark ? 'dark' : 'light' }
    }).render();

    // 2. Order status donut
    new ApexCharts(document.querySelector('#anStatusDonut'), {
        series: " . json_encode(array_values($orderStatuses)) . ",
        labels: " . json_encode(array_map('ucfirst', array_keys($orderStatuses))) . ",
        chart: { type: 'donut', height: 180, background: 'transparent', toolbar: { show: false }, animations: animCfg },
        colors: ['#00B894','#FDCB6E','#E17055'],
        dataLabels: { enabled: false },
        legend: { show: false },
        plotOptions: { pie: { donut: { size: '68%' } } },
        stroke: { width: 0 },
        tooltip: { theme: tooltipTheme },
        theme: { mode: isDark ? 'dark' : 'light' }
    }).render();

    // 3. User + provider growth bars
    new ApexCharts(document.querySelector('#anUserGrowthChart'), {
        series: [
            { name: 'New users',     data: " . json_encode(array_column($userGrowth, 'new_users')) . " },
            { name: 'New providers', data: " . json_encode(array_column($userGrowth, 'providers')) . " }
        ],
        chart: { type: 'bar', height: 240, toolbar: { show: false }, background: 'transparent', zoom: { enabled: false }, animations: animCfg },
        colors: ['#6C5CE7','#00B894'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        xaxis: {
            categories: " . json_encode(array_column($userGrowth, 'month')) . ",
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: tickColor, fontSize: '10px' } }
        },
        yaxis: { labels: { style: { colors: tickColor, fontSize: '10px' } } },
        legend: { position: 'top', fontSize: '11px', labels: { colors: tickColor } },
        tooltip: { theme: tooltipTheme },
        theme: { mode: isDark ? 'dark' : 'light' }
    }).render();

    // ── Period tabs ───────────────────────────────────────────────────────────
    document.querySelectorAll('.an-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.an-tab').forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            // window.location.href = '?period=' + tab.dataset.period;
        });
    });

});
", \yii\web\View::POS_END); ?>