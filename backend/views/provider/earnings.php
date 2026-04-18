<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $commissions */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Earnings & Commissions';
?>

<style>
.earnings-header { margin-bottom: 24px; }
.earnings-title { font-size: 24px; font-weight: 800; color: var(--text1); margin-bottom: 8px; }
.earnings-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-bottom: 24px; }
.earnings-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px 20px; }
.earnings-label { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .1em; font-weight: 600; margin-bottom: 8px; }
.earnings-value { font-size: 28px; font-weight: 800; letter-spacing: -.02em; color: var(--text1); }
.earnings-sub { font-size: 12px; color: var(--text3); margin-top: 4px; }
.commissions-table { width: 100%; border-collapse: collapse; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.commissions-table thead th { background: var(--bg3); padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text3); letter-spacing: .05em; }
.commissions-table tbody td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 13px; }
.commissions-table tbody tr:last-child td { border-bottom: none; }
.comm-id { font-weight: 600; color: var(--text1); }
.comm-order { color: var(--acc); text-decoration: none; }
.comm-amount { font-weight: 600; color: var(--text1); }
.comm-status { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 12px; }
.status-pending { background: rgba(251,191,36,.1); color: #fbbf24; }
.status-completed { background: rgba(34,197,94,.1); color: #22c55e; }
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-text { font-size: 14px; color: var(--text3); }
</style>

<div class="earnings-header">
    <h1 class="earnings-title">Earnings & Commissions</h1>
</div>

<!-- Summary Cards -->
<div class="earnings-cards">
    <div class="earnings-card">
        <div class="earnings-label">Total Earned</div>
        <div class="earnings-value">KSh <?= number_format(
            \common\models\Commission::find()->where(['provider_id' => $provider->id])->sum('amount') ?? 0,
            0
        ) ?></div>
        <div class="earnings-sub">All time</div>
    </div>
    <div class="earnings-card">
        <div class="earnings-label">This Month</div>
        <div class="earnings-value">KSh <?= number_format(
            \common\models\Commission::find()
                ->where(['provider_id' => $provider->id])
                ->andWhere(['like', 'created_at', date('Y-m')])
                ->sum('amount') ?? 0,
            0
        ) ?></div>
        <div class="earnings-sub"><?= date('F Y') ?></div>
    </div>
    <div class="earnings-card">
        <div class="earnings-label">Pending</div>
        <div class="earnings-value">KSh <?= number_format(
            \common\models\Commission::find()->where(['provider_id' => $provider->id, 'status' => 'pending'])->sum('amount') ?? 0,
            0
        ) ?></div>
        <div class="earnings-sub">Waiting for payout</div>
    </div>
</div>

<!-- Commissions Table -->
<?php if ($commissions): ?>
    <table class="commissions-table">
        <thead>
            <tr>
                <th>Commission ID</th>
                <th>Order ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commissions as $commission): ?>
                <tr>
                    <td class="comm-id">#<?= $commission->id ?></td>
                    <td>
                        <a href="<?= Url::to(['/provider/order-view', 'id' => $commission->order_id]) ?>" class="comm-order offcanvas-link">
                            #<?= $commission->order_id ?>
                        </a>
                    </td>
                    <td class="comm-amount">KSh <?= number_format($commission->amount, 0) ?></td>
                    <td>
                        <span class="comm-status status-<?= $commission->status ?>">
                            <?= ucfirst($commission->status) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($commission->created_at)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">💰</div>
        <div class="empty-text">No earnings yet. Complete orders to earn commissions!</div>
    </div>
<?php endif; ?>