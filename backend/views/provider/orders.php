<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $orders */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Orders';
?>

<style>
.orders-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
.orders-title { font-size: 24px; font-weight: 800; color: var(--text1); }
.order-filters { display: flex; gap: 8px; margin-bottom: 16px; }
.filter-btn { padding: 6px 12px; border: 1px solid var(--border); background: var(--bg); border-radius: var(--r-md); font-size: 12px; font-weight: 600; color: var(--text2); cursor: pointer; transition: all .2s; }
.filter-btn.active { background: var(--acc); color: #fff; border-color: var(--acc); }
.orders-table { width: 100%; border-collapse: collapse; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.orders-table thead th { background: var(--bg3); padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text3); letter-spacing: .05em; }
.orders-table tbody td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 13px; }
.orders-table tbody tr:last-child td { border-bottom: none; }
.order-id { font-weight: 600; color: var(--text1); }
.order-customer { font-weight: 600; color: var(--text1); }
.order-amount { font-weight: 600; color: var(--text1); }
.order-status { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 12px; width: fit-content; }
.status-pending { background: rgba(251,191,36,.1); color: #fbbf24; }
.status-processing { background: rgba(59,130,246,.1); color: #3b82f6; }
.status-completed { background: rgba(34,197,94,.1); color: #22c55e; }
.status-cancelled { background: rgba(239,68,68,.1); color: #ef4444; }
.order-actions { font-size: 12px; }
.action-link { color: var(--acc); text-decoration: none; margin-right: 12px; }
.action-link:hover { text-decoration: underline; }
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-text { font-size: 14px; color: var(--text3); }
</style>

<div class="orders-header">
    <h1 class="orders-title">My Orders</h1>
</div>

<?php if ($orders): ?>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="order-id">#<?= $order->id ?></td>
                    <td class="order-customer"><?= Html::encode($order->user->getFullName()) ?></td>
                    <td class="order-amount">KSh <?= number_format($order->total_amount, 0) ?></td>
                    <td>
                        <span class="order-status status-<?= $order->status ?>">
                            <?= ucfirst(str_replace('_', ' ', $order->status)) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($order->created_at)) ?></td>
                    <td class="order-actions">
                        <a href="<?= Url::to(['/provider/order-view', 'id' => $order->id]) ?>" class="action-link offcanvas-link">View</a>
                        <?php if ($order->status === 'awaiting_payment'): ?>
                            <a href="<?= Url::to(['/provider/accept-order', 'id' => $order->id]) ?>" class="action-link">Accept</a>
                        <?php endif; ?>
                        <?php if (in_array($order->status, ['processing', 'out_for_delivery'])): ?>
                            <a href="<?= Url::to(['/provider/complete-order', 'id' => $order->id]) ?>" class="action-link">Complete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <div class="empty-text">You don't have any orders yet. Keep your listings fresh to get more orders!</div>
    </div>
<?php endif; ?>
