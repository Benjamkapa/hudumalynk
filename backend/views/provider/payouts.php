<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'My Payouts & Earnings';
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.amber{color:#FDCB6E;}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.comm-badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;}
.comm-badge.pending{background:var(--amber-pale);color:#7a5500;}
.comm-badge.paid{background:var(--teal-pale);color:var(--teal);}
[data-hl-t="dark"] .comm-badge.pending{color:var(--amber);}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Payouts & Earnings</h1>
        <div class="hl-pg-sub">Track your commissions and payouts</div>
    </div>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total earned</div><div class="pg-hc-val">KES <?= number_format($stats['total_earned'] ?? 0) ?></div><div class="pg-hc-sub">All time commissions</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Pending payout</div><div class="pg-hc-val amber">KES <?= number_format($stats['pending_payouts'] ?? 0) ?></div><div class="pg-hc-sub">Ready for withdrawal</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Paid out</div><div class="pg-hc-val teal">KES <?= number_format($stats['paid_out'] ?? 0) ?></div><div class="pg-hc-sub">Successfully sent to M-Pesa</div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/provider/payouts']) ?>" class="filter-pill active">All earnings</a>
    <a href="<?= Url::to(['/provider/payouts', 'status' => 'pending']) ?>" class="filter-pill">Pending (<?= number_format($stats['pending_payouts'] ?? 0) ?>)</a>
    <a href="<?= Url::to(['/provider/payouts', 'status' => 'paid']) ?>" class="filter-pill">Paid out</a>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Commission history</span>
        <span style="font-size:11px;color:var(--text3);">Recent <?= $dataProvider->totalCount ?> transactions</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>Order</th><th>Amount</th><th>Status</th><th>Date</th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->models as $comm): ?>
        <tr>
            <?php if ($comm->order): ?>
            <td>
                <a href="<?= Url::to(['/provider/orders', 'id' => $comm->order_id]) ?>" style="color:var(--acc);font-family:monospace;font-size:12px;">
                    #<?= sprintf('%06d', $comm->order_id) ?>
                </a>
            </td>
            <?php else: ?>
            <td>—</td>
            <?php endif; ?>
            <td style="font-weight:700;color:var(--text1);">KES <?= number_format($comm->amount ?? 0) ?></td>
            <td><span class="comm-badge <?= $comm->status ?? 'pending' ?>"><?= ucfirst($comm->status ?? 'pending') ?></span></td>
            <td style="font-size:11px;color:var(--text3);"><?= date('d M Y · H:i', strtotime($comm->created_at ?? 'now')) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($dataProvider->models)): ?>
        <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text3);">
            No earnings yet. Complete some orders to earn commissions!
        </td></tr>
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
