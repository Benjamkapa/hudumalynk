<?php
/** @var yii\web\View $this */
/** @var array $stats, $chartData */
/** @var yii\data\ActiveDataProvider $dataProvider */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Earnings & GMV';
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>

<style>
.pg-hero { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 11px; margin-bottom: 16px; }
.pg-hcard { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 14px 15px; }
.pg-hc-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); margin-bottom: 5px; }
.pg-hc-val { font-size: 22px; font-weight: 800; letter-spacing: -.04em; color: var(--text1); line-height: 1; }
.pg-hc-sub { font-size: 11px; color: var(--text3); margin-top: 4px; }
.pg-hc-val.teal   { color: var(--teal); }
.pg-hc-val.purple { color: var(--acc2); }
.pg-hc-val.amber  { color: #FDCB6E; }
.pg-hc-val.rose   { color: var(--rose); }
.comm-status { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9.5px; font-weight: 700; }
.comm-status.paid    { background: var(--teal-pale); color: var(--teal); }
.comm-status.pending { background: var(--amber-pale); color: #7a5500; }
[data-hl-t="dark"] .comm-status.pending { color: var(--amber); }
@media (max-width: 900px) { .pg-hero { grid-template-columns: 1fr 1fr; } }
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Earnings & GMV</h1>
        <div class="hl-pg-sub">Platform gross merchandise value and commission income</div>
    </div>
</div>

<!-- ── Hero cards ── -->
<div class="pg-hero">
    <div class="pg-hcard">
        <div class="pg-hc-lbl">Total GMV</div>
        <div class="pg-hc-val" id="eg-gmv" data-value="<?= $stats['total_gmv'] ?? 0 ?>">KES 0</div>
        <div class="pg-hc-sub">All time</div>
    </div>
    <div class="pg-hcard">
        <div class="pg-hc-lbl">GMV last 30 days</div>
        <div class="pg-hc-val teal" id="eg-gmv30" data-value="<?= $stats['gmv_30d'] ?? 0 ?>">KES 0</div>
        <div class="pg-hc-sub">Recent activity</div>
    </div>
    <div class="pg-hcard">
        <div class="pg-hc-lbl">Commission earned</div>
        <div class="pg-hc-val purple" id="eg-commission" data-value="<?= $stats['total_commission'] ?? 0 ?>">KES 0</div>
        <div class="pg-hc-sub">Platform revenue</div>
    </div>
    <div class="pg-hcard">
        <div class="pg-hc-lbl">Pending payouts</div>
        <div class="pg-hc-val amber" id="eg-payouts" data-value="<?= $stats['pending_payouts'] ?? 0 ?>">KES 0</div>
        <div class="pg-hc-sub">Owed to vendors</div>
    </div>
</div>

<!-- ── ApexCharts replacing the mini bar ── -->
<div class="hl-card" style="margin-bottom:12px;">
    <div class="hl-card-head"><span class="hl-card-title">Monthly GMV &amp; Commission — last 6 months</span></div>
    <div id="earningsGmvChart" style="height:220px;"></div>
</div>

<!-- ── Commission ledger ── -->
<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Commission Ledger</span>
        <span style="font-size:11px;color:var(--text3);"><?= $dataProvider->totalCount ?> entries</span>
    </div>
    <table class="hl-tbl">
        <thead>
            <tr><th>Vendor</th><th>Order</th><th>Order Amount</th><th>Rate</th><th>Commission</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
            <?php foreach ($dataProvider->getModels() as $i => $comm):
                $bg    = $colors[$i % count($colors)];
                $vname = $comm->provider->business_name ?? 'Unknown';
                $vinit = strtoupper(substr($vname, 0, 2));
            ?>
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:9px;">
                        <div style="width:28px;height:28px;border-radius:var(--r-sm);background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:9.5px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($vinit) ?></div>
                        <span style="font-size:12px;color:var(--text1);font-weight:500;"><?= Html::encode($vname) ?></span>
                    </div>
                </td>
                <td style="font-family:monospace;font-size:12px;color:var(--text2);">
                    <?php if ($comm->order): ?>
                        <a href="<?= Url::to(['/admin/order-view', 'id' => $comm->order_id]) ?>" style="color:var(--acc);">#<?= sprintf('%06d', $comm->order_id) ?></a>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td style="font-size:12px;color:var(--text2);">KES <?= number_format($comm->order_amount ?? 0) ?></td>
                <td style="font-size:12px;color:var(--text2);"><?= $comm->rate ?? 0 ?>%</td>
                <td style="font-weight:700;color:var(--text1);font-size:12.5px;">KES <?= number_format($comm->amount ?? 0) ?></td>
                <td><span class="comm-status <?= Html::encode($comm->status ?? 'pending') ?>"><?= ucfirst($comm->status ?? 'pending') ?></span></td>
                <td style="font-size:11.5px;color:var(--text3);"><?= date('d M Y', strtotime($comm->created_at ?? 'now')) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if ($dataProvider->totalCount === 0): ?>
            <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No commissions recorded yet</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if ($dataProvider->pagination->pageCount > 1): ?>
    <div style="display:flex;justify-content:center;padding:14px;gap:6px;">
        <?php for ($p = 1; $p <= $dataProvider->pagination->pageCount; $p++):
            $active = $p === $dataProvider->pagination->page + 1;
        ?>
        <a href="?page=<?= $p ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active ? 'var(--acc)' : 'var(--border)' ?>;font-size:12px;font-weight:600;color:<?= $active ? 'var(--acc)' : 'var(--text2)' ?>;background:<?= $active ? 'var(--acc-pale)' : 'var(--bg2)' ?>;text-decoration:none;"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
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

    // ── Hero cards ────────────────────────────────────────────────────────────
    [
        { id: 'eg-gmv',        dur: 1400 },
        { id: 'eg-gmv30',      dur: 1200 },
        { id: 'eg-commission', dur: 1300 },
        { id: 'eg-payouts',    dur: 1100 }
    ].forEach(function(cfg) {
        var el = document.getElementById(cfg.id);
        if (!el) return;
        countUp(el, parseFloat(el.dataset.value) || 0, cfg.dur, function(v) {
            return 'KES ' + Math.round(v).toLocaleString('en-KE');
        });
    });

    // ── GMV + Commission chart ────────────────────────────────────────────────
    var isDark    = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    var tickColor = isDark ? '#55556A' : '#9898B8';

    var chartMonths     = " . json_encode(array_column($chartData, 'month')) . ";
    var chartGmv        = " . json_encode(array_column($chartData, 'gmv')) . ";
    var chartCommission = " . json_encode(array_column($chartData, 'commission')) . ";

    new ApexCharts(document.querySelector('#earningsGmvChart'), {
        series: [
            { name: 'GMV',        type: 'area', data: chartGmv },
            { name: 'Commission', type: 'bar',  data: chartCommission }
        ],
        chart: {
            height: 220, toolbar: { show: false }, background: 'transparent', zoom: { enabled: false },
            animations: {
                enabled: true, easing: 'easeinout', speed: 900,
                animateGradually: { enabled: true, delay: 120 },
                dynamicAnimation: { enabled: true, speed: 450 }
            }
        },
        colors: ['#6C5CE7', '#00B894'],
        stroke: { curve: 'smooth', width: [2.5, 0] },
        fill: { type: ['gradient', 'solid'], gradient: { opacityFrom: 0.28, opacityTo: 0 } },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        xaxis: {
            categories: chartMonths,
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: tickColor, fontSize: '10px' } }
        },
        yaxis: {
            labels: {
                style: { colors: tickColor, fontSize: '10px' },
                formatter: function(v) { return 'KES ' + (v / 1000).toFixed(0) + 'K'; }
            }
        },
        legend: { position: 'top', fontSize: '11px', labels: { colors: tickColor } },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: function(v) { return 'KES ' + v.toLocaleString(); } }
        },
        theme: { mode: isDark ? 'dark' : 'light' }
    }).render();

});
", \yii\web\View::POS_END); ?>