<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Invoices';
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
.inv-st{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;}
.inv-st.completed,.inv-st.paid{background:var(--teal-pale);color:var(--teal);}
.inv-st.pending{background:var(--amber-pale);color:#7a5500;}
.inv-st.failed{background:var(--rose-pale);color:var(--rose);}
[data-hl-t="dark"] .inv-st.pending{color:var(--amber);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Invoices</h1>
        <div class="hl-pg-sub">All M-Pesa payment records and invoices</div>
    </div>
    <button class="hl-btn-g" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print Report
    </button>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total payments</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">All time</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Completed</div><div class="pg-hc-val teal"><?= number_format($stats['completed'] ?? 0) ?></div><div class="pg-hc-sub">Successfully paid</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Pending</div><div class="pg-hc-val amber"><?= number_format($stats['pending'] ?? 0) ?></div><div class="pg-hc-sub">Awaiting payment</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Total value</div><div class="pg-hc-val purple">KES <?= number_format($stats['total_value'] ?? 0) ?></div><div class="pg-hc-sub">Revenue collected</div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/invoices']) ?>"                              class="filter-pill <?= !$filter ? 'active' : '' ?>">All</a>
    <a href="<?= Url::to(['/admin/invoices', 'status' => 'completed']) ?>"    class="filter-pill <?= $filter === 'completed' ? 'active' : '' ?>">Completed</a>
    <a href="<?= Url::to(['/admin/invoices', 'status' => 'pending']) ?>"      class="filter-pill <?= $filter === 'pending'   ? 'active' : '' ?>">Pending</a>
    <a href="<?= Url::to(['/admin/invoices', 'status' => 'failed']) ?>"       class="filter-pill <?= $filter === 'failed'    ? 'active' : '' ?>">Failed</a>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Payment invoices</span>
        <span style="font-size:11px;color:var(--text3);"><?= number_format($dataProvider->totalCount) ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Invoice #</th><th>Customer</th><th>Vendor</th><th>Order</th><th>Amount</th><th>M-Pesa receipt</th><th>Status</th><th>Date</th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $payment):
            $bg     = $colors[$i % count($colors)];
            $cname  = $payment->order->user->getFullName() ?? 'Unknown';
            $cinit  = strtoupper(substr($cname, 0, 2));
            $vname  = $payment->order->provider->business_name ?? '—';
            $status = $payment->status ?? 'pending';
        ?>
        <tr>
            <td style="font-family:monospace;font-size:12px;font-weight:700;color:var(--text1);">INV-<?= strtoupper(sprintf('%06d', $payment->id)) ?></td>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:26px;height:26px;border-radius:50%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:9.5px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($cinit) ?></div>
                    <span style="font-size:12px;color:var(--text1);"><?= Html::encode($cname) ?></span>
                </div>
            </td>
            <td style="font-size:12px;color:var(--text2);"><?= Html::encode($vname) ?></td>
            <td>
                <?php if ($payment->order): ?>
                <a href="<?= Url::to(['/admin/order-view', 'id' => $payment->order_id]) ?>" style="color:var(--acc);font-family:monospace;font-size:12px;">#<?= sprintf('%06d', $payment->order_id) ?></a>
                <?php else: ?>—<?php endif; ?>
            </td>
            <td style="font-weight:700;font-size:13px;color:var(--text1);">KES <?= number_format($payment->amount ?? 0) ?></td>
            <td style="font-family:monospace;font-size:11.5px;color:var(--text2);"><?= Html::encode($payment->mpesa_receipt ?? '—') ?></td>
            <td><span class="inv-st <?= Html::encode($status) ?>"><?= ucfirst($status) ?></span></td>
            <td style="font-size:11px;color:var(--text3);"><?= date('d M Y · H:i', strtotime($payment->created_at ?? 'now')) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No invoices found</td></tr>
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
