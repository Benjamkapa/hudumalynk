<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Payouts & Earnings';
?>

<style>
.pp-shell{display:flex;flex-direction:column;gap:16px;}
.pp-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.pp-sub{font-size:12px;color:var(--text3);margin-top:4px;}
.pp-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
.pp-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:15px;}
.pp-card .lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text3);margin-bottom:5px;}
.pp-card .val{font-size:24px;font-weight:800;color:var(--text1);}
.pp-card .val.teal{color:var(--teal);}.pp-card .val.amber{color:var(--amber);}
@media(max-width:800px){.pp-stats{grid-template-columns:1fr;}}
</style>

<div class="pp-shell">
    <div>
        <div class="pp-title">Payouts & Earnings</div>
        <div class="pp-sub">Monitor how much <?= Html::encode($provider->business_name) ?> has earned, what is still pending, and which orders generated commission.</div>
    </div>

    <div class="pp-stats">
        <div class="pp-card"><div class="lbl">Total Earned</div><div class="val">KES <?= number_format((float) ($stats['total_earned'] ?? 0), 0) ?></div></div>
        <div class="pp-card"><div class="lbl">Pending Payouts</div><div class="val amber">KES <?= number_format((float) ($stats['pending_payouts'] ?? 0), 0) ?></div></div>
        <div class="pp-card"><div class="lbl">Paid Out</div><div class="val teal">KES <?= number_format((float) ($stats['paid_out'] ?? 0), 0) ?></div></div>
    </div>

    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Commission Ledger</span>
            <a href="<?= Url::to(['/provider/earnings']) ?>" class="hl-card-link">Open earnings view</a>
        </div>
        <table class="hl-tbl">
            <thead>
                <tr><th>Commission</th><th>Order</th><th>Status</th><th>Amount</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php if ($dataProvider->getCount() === 0): ?>
                    <tr><td colspan="5" style="text-align:center;padding:28px;color:var(--text3);">No payout records yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($dataProvider->getModels() as $commission): ?>
                    <tr>
                        <td>#<?= (int) $commission->id ?></td>
                        <td><a href="<?= Url::to(['/provider/order-view', 'id' => $commission->order_id]) ?>" class="offcanvas-link">Order #<?= (int) $commission->order_id ?></a></td>
                        <td><span class="hl-badge <?= $commission->status === 'paid' ? 'paid' : 'pend' ?>"><?= Html::encode(ucfirst($commission->status)) ?></span></td>
                        <td>KES <?= number_format((float) $commission->amount, 0) ?></td>
                        <td><?= date('d M Y · H:i', strtotime($commission->created_at ?: 'now')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
