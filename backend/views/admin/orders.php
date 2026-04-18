<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Orders';
$currentStatus = Yii::$app->request->get('status');
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
$statusColors = [
    'completed'        => 'paid',
    'pending'          => 'pend',
    'processing'       => 'new',
    'awaiting_payment' => 'pend',
    'cancelled'        => 'new',
    'out_for_delivery' => 'new',
];
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:#FDCB6E;}.pg-hc-val.rose{color:var(--rose);}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.ord-st{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;}
.ord-st.completed{background:var(--teal-pale);color:var(--teal);}
.ord-st.pending,.ord-st.awaiting_payment{background:var(--amber-pale);color:#7a5500;}
.ord-st.processing,.ord-st.out_for_delivery{background:var(--acc-pale);color:var(--acc);}
.ord-st.cancelled{background:var(--rose-pale);color:var(--rose);}
[data-hl-t="dark"] .ord-st.pending,[data-hl-t="dark"] .ord-st.awaiting_payment{color:var(--amber);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Orders</h1>
        <div class="hl-pg-sub">Track and manage all marketplace transactions</div>
    </div>
    <button class="hl-btn-g" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
        Export
    </button>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total orders</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">All time</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Completed</div><div class="pg-hc-val teal"><?= number_format($stats['completed'] ?? 0) ?></div><div class="pg-hc-sub">Successfully fulfilled</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Pending</div><div class="pg-hc-val amber"><?= number_format($stats['pending'] ?? 0) ?></div><div class="pg-hc-sub">Awaiting action</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">GMV today</div><div class="pg-hc-val purple">KES <?= number_format($stats['gmv_today'] ?? 0) ?></div><div class="pg-hc-sub">Gross merchandise value</div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/orders']) ?>"                              class="filter-pill <?= !$currentStatus ? 'active' : '' ?>">All orders</a>
    <a href="<?= Url::to(['/admin/orders', 'status' => 'pending']) ?>"       class="filter-pill <?= $currentStatus === 'pending'    ? 'active' : '' ?>">Pending</a>
    <a href="<?= Url::to(['/admin/orders', 'status' => 'processing']) ?>"    class="filter-pill <?= $currentStatus === 'processing'  ? 'active' : '' ?>">Processing</a>
    <a href="<?= Url::to(['/admin/orders', 'status' => 'completed']) ?>"     class="filter-pill <?= $currentStatus === 'completed'   ? 'active' : '' ?>">Completed</a>
    <a href="<?= Url::to(['/admin/orders', 'status' => 'cancelled']) ?>"     class="filter-pill <?= $currentStatus === 'cancelled'   ? 'active' : '' ?>">Cancelled</a>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Order transactions</span>
        <span style="font-size:11px;color:var(--text3);"><?= $dataProvider->totalCount ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Order ref</th><th>Customer</th><th>Vendor</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $order):
            $bg       = $colors[$i % count($colors)];
            $custName = $order->user ? $order->user->getFullName() : 'Unknown';
            $custInit = strtoupper(substr($custName, 0, 2));
            $status   = $order->status ?? 'pending';
            $stClass  = str_replace([' ', '-'], '_', $status);
            $payBadge = $order->payment_status === 'paid' ? 'paid' : 'pend';
        ?>
        <tr>
            <td style="font-weight:700;color:var(--text1);font-size:12px;font-family:monospace;">
                #<?= strtoupper(sprintf('%06d', $order->id)) ?>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:50%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:9.5px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($custInit) ?></div>
                    <div>
                        <div style="font-size:12px;color:var(--text1);font-weight:500;"><?= Html::encode($custName) ?></div>
                        <?php if ($order->user): ?>
                        <div style="font-size:10px;color:var(--text3);"><?= Html::encode($order->user->phone ?? '') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td style="font-size:12px;color:var(--text2);"><?= Html::encode($order->provider->business_name ?? '—') ?></td>
            <td style="font-weight:700;font-size:12px;color:var(--text1);">KES <?= number_format($order->total_amount ?? 0) ?></td>
            <td><span class="hl-badge <?= $payBadge ?>"><?= ucfirst($order->payment_status ?? 'unpaid') ?></span></td>
            <td><span class="ord-st <?= Html::encode($stClass) ?>"><?= ucfirst(str_replace('_', ' ', $status)) ?></span></td>
            <td style="font-size:11px;color:var(--text3);"><?= date('d M Y · H:i', strtotime($order->created_at ?? 'now')) ?></td>
            <td><a href="<?= Url::to(['/admin/order-view', 'id' => $order->id]) ?>" class="hl-btn-g offcanvas-link" style="padding:5px 9px;font-size:11px;">View</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No orders found</td></tr>
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
