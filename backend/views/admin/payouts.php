<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'M-Pesa Payouts';
$filter = Yii::$app->request->get('status');
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
.payout-st{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;}
.payout-st.paid{background:var(--teal-pale);color:var(--teal);}
.payout-st.pending{background:var(--amber-pale);color:#7a5500;}
[data-hl-t="dark"] .payout-st.pending{color:var(--amber);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">M-Pesa Payouts</h1>
        <div class="hl-pg-sub">Vendor commission payouts via M-Pesa</div>
    </div>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total paid out</div><div class="pg-hc-val teal">KES <?= number_format($stats['total_paid'] ?? 0) ?></div><div class="pg-hc-sub">All time disbursed</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Pending payouts</div><div class="pg-hc-val amber">KES <?= number_format($stats['pending'] ?? 0) ?></div><div class="pg-hc-sub">Awaiting disbursement</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Vendors owed</div><div class="pg-hc-val purple"><?= number_format($stats['vendors_owed'] ?? 0) ?></div><div class="pg-hc-sub">With pending balance</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Paid this month</div><div class="pg-hc-val">KES <?= number_format($stats['paid_this_month'] ?? 0) ?></div><div class="pg-hc-sub"><?= date('F Y') ?></div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/payouts']) ?>"                            class="filter-pill <?= !$filter ? 'active' : '' ?>">All</a>
    <a href="<?= Url::to(['/admin/payouts', 'status' => 'pending']) ?>"    class="filter-pill <?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="<?= Url::to(['/admin/payouts', 'status' => 'paid']) ?>"       class="filter-pill <?= $filter === 'paid'    ? 'active' : '' ?>">Paid</a>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Payout ledger</span>
        <span style="font-size:11px;color:var(--text3);"><?= number_format($dataProvider->totalCount) ?> entries</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Vendor</th><th>Order ref</th><th>Order amount</th><th>Commission rate</th><th>Payout</th><th>Status</th><th>Date</th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $comm):
            $bg    = $colors[$i % count($colors)];
            $vname = $comm->provider->business_name ?? 'Unknown';
            $vinit = strtoupper(substr($vname, 0, 2));
            $status = $comm->status ?? 'pending';
            // Net vendor payout = order_amount - commission
            $netPayout = ($comm->order_amount ?? 0) - ($comm->amount ?? 0);
        ?>
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:30px;height:30px;border-radius:var(--r-sm);background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($vinit) ?></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($vname) ?></div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($comm->provider->phone ?? '') ?></div>
                    </div>
                </div>
            </td>
            <td>
                <?php if ($comm->order): ?>
                <a href="<?= Url::to(['/admin/order-view', 'id' => $comm->order_id]) ?>" style="color:var(--acc);font-family:monospace;font-size:12px;">#<?= sprintf('%06d', $comm->order_id) ?></a>
                <?php else: ?>—<?php endif; ?>
            </td>
            <td style="font-size:12px;color:var(--text2);">KES <?= number_format($comm->order_amount ?? 0) ?></td>
            <td style="font-size:12px;color:var(--text2);"><?= $comm->rate ?? 0 ?>% (KES <?= number_format($comm->amount ?? 0) ?>)</td>
            <td style="font-weight:700;font-size:13px;color:var(--teal);">KES <?= number_format($netPayout) ?></td>
            <td><span class="payout-st <?= Html::encode($status) ?>"><?= ucfirst($status) ?></span></td>
            <td style="font-size:11.5px;color:var(--text3);">
                <?= $status === 'paid' && $comm->paid_at ? date('d M Y', strtotime($comm->paid_at)) : date('d M Y', strtotime($comm->created_at ?? 'now')) ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No payout records found</td></tr>
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
