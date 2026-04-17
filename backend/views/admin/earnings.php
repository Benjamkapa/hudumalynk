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
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:22px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:#FDCB6E;}.pg-hc-val.rose{color:var(--rose);}
.chart-wrap{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px;margin-bottom:12px;}
.chart-title{font-size:13px;font-weight:700;color:var(--text1);margin-bottom:14px;}
.mini-chart{display:grid;grid-template-columns:repeat(6,1fr);gap:8px;align-items:end;height:120px;}
.bar-wrap{display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;}
.bar-inner{display:flex;flex-direction:column;justify-content:flex-end;flex:1;width:100%;gap:3px;}
.bar-seg{border-radius:3px 3px 0 0;transition:height .4s ease;}
.bar-lbl{font-size:9.5px;color:var(--text3);text-align:center;}
.bar-val{font-size:9px;color:var(--text2);text-align:center;font-weight:600;}
.comm-status{display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;}
.comm-status.paid{background:var(--teal-pale);color:var(--teal);}
.comm-status.pending{background:var(--amber-pale);color:#7a5500;}
[data-hl-t="dark"] .comm-status.pending{color:var(--amber);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Earnings & GMV</h1>
        <div class="hl-pg-sub">Platform gross merchandise value and commission income</div>
    </div>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total GMV</div><div class="pg-hc-val">KES <?= number_format($stats['total_gmv'] ?? 0) ?></div><div class="pg-hc-sub">All time</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">GMV last 30 days</div><div class="pg-hc-val teal">KES <?= number_format($stats['gmv_30d'] ?? 0) ?></div><div class="pg-hc-sub">Recent activity</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Commission earned</div><div class="pg-hc-val purple">KES <?= number_format($stats['total_commission'] ?? 0) ?></div><div class="pg-hc-sub">Platform revenue</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Pending payouts</div><div class="pg-hc-val amber">KES <?= number_format($stats['pending_payouts'] ?? 0) ?></div><div class="pg-hc-sub">Owed to vendors</div></div>
</div>

<!-- Mini Bar Chart -->
<?php
$maxGmv = max(array_column($chartData, 'gmv') ?: [1]);
?>
<div class="chart-wrap">
    <div class="chart-title">Monthly GMV & Commission — last 6 months</div>
    <div class="mini-chart">
        <?php foreach ($chartData as $cd):
            $gmvH  = $maxGmv > 0 ? max(4, round($cd['gmv'] / $maxGmv * 90)) : 4;
            $comH  = $maxGmv > 0 ? max(2, round($cd['commission'] / $maxGmv * 90)) : 2;
        ?>
        <div class="bar-wrap">
            <div class="bar-val">KES <?= number_format($cd['gmv'] / 1000, 0) ?>k</div>
            <div class="bar-inner">
                <div class="bar-seg" style="height:<?= $gmvH ?>px;background:var(--acc);"></div>
                <div class="bar-seg" style="height:<?= $comH ?>px;background:var(--teal);margin-top:-3px;"></div>
            </div>
            <div class="bar-lbl"><?= Html::encode($cd['month']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="display:flex;gap:16px;margin-top:12px;">
        <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text2);"><span style="width:12px;height:12px;border-radius:3px;background:var(--acc);flex-shrink:0;display:inline-block;"></span> GMV</div>
        <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text2);"><span style="width:12px;height:12px;border-radius:3px;background:var(--teal);flex-shrink:0;display:inline-block;"></span> Commission</div>
    </div>
</div>

<!-- Commission Ledger -->
<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Commission Ledger</span>
        <span style="font-size:11px;color:var(--text3);"><?= $dataProvider->totalCount ?> entries</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Vendor</th><th>Order</th><th>Order Amount</th><th>Rate</th><th>Commission</th><th>Status</th><th>Date</th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $comm):
            $bg     = $colors[$i % count($colors)];
            $vname  = $comm->provider->business_name ?? 'Unknown';
            $vinit  = strtoupper(substr($vname, 0, 2));
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
        <a href="?page=<?= $p ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active?'var(--acc)':'var(--border)'?>;font-size:12px;font-weight:600;color:<?= $active?'var(--acc)':'var(--text2)'?>;background:<?= $active?'var(--acc-pale)':'var(--bg2)'?>;text-decoration:none;"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
