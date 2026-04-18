<?php
/** @var yii\web\View $this */
/** @var common\models\Order $order */
/** @var common\models\Payment $payment */
/** @var common\models\Provider $provider */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Order #' . $order->id;
?>

<style>
.order-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
.order-title { font-size: 22px; font-weight: 800; color: var(--text1); }
.back-link { color: var(--acc); text-decoration: none; font-size: 14px; }
.back-link:hover { text-decoration: underline; }
.order-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; }
.order-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 20px; }
.card-title { font-size: 14px; font-weight: 700; color: var(--text1); margin-bottom: 16px; }
.detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border); }
.detail-row:last-child { border-bottom: none; }
.detail-label { font-size: 12px; color: var(--text3); font-weight: 600; }
.detail-value { font-size: 14px; color: var(--text1); font-weight: 600; }
.item-list { margin-bottom: 20px; }
.item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border); }
.item:last-child { border-bottom: none; }
.item-name { font-size: 13px; font-weight: 600; color: var(--text1); }
.item-qty { font-size: 12px; color: var(--text3); }
.item-price { font-size: 13px; font-weight: 600; color: var(--text1); }
.summary { background: var(--bg3); border-radius: var(--r-md); padding: 16px; margin-top: 16px; }
.summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; }
.summary-row.total { border-top: 1px solid var(--border); padding-top: 12px; margin-top: 8px; font-weight: 700; font-size: 14px; color: var(--text1); }
.customer-info { display: flex; gap: 12px; margin-bottom: 16px; }
.customer-avatar { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: #fff; flex-shrink: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.customer-details { flex: 1; }
.customer-name { font-size: 14px; font-weight: 700; color: var(--text1); }
.customer-email { font-size: 12px; color: var(--text3); }
.customer-phone { font-size: 12px; color: var(--text3); }
.status-badge { display: inline-block; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 20px; margin-top: 12px; }
.status-pending { background: rgba(251,191,36,.1); color: #fbbf24; }
.status-processing { background: rgba(59,130,246,.1); color: #3b82f6; }
.status-completed { background: rgba(34,197,94,.1); color: #22c55e; }
.status-cancelled { background: rgba(239,68,68,.1); color: #ef4444; }
.btn-action { display: block; width: 100%; padding: 12px; border: none; border-radius: var(--r-md); font-size: 14px; font-weight: 600; cursor: pointer; margin-bottom: 8px; text-decoration: none; text-align: center; transition: background .2s; }
.btn-action.primary { background: var(--acc); color: #fff; }
.btn-action.primary:hover { background: var(--acc2); }
.btn-action.secondary { background: var(--bg3); color: var(--text1); border: 1px solid var(--border); }
.btn-action.secondary:hover { background: var(--border); }
</style>

<a href="<?= Url::to(['/provider/orders']) ?>" class="back-link">← Back to Orders</a>

<div class="order-header">
    <h1 class="order-title">Order #<?= $order->id ?></h1>
</div>

<div class="order-grid">
    <div>
        <!-- Customer Info -->
        <div class="order-card">
            <div class="card-title">Customer Information</div>
            <div class="customer-info">
                <div class="customer-avatar">
                    <?= strtoupper(substr($order->user->getFullName(), 0, 1)) ?>
                </div>
                <div class="customer-details">
                    <div class="customer-name"><?= Html::encode($order->user->getFullName()) ?></div>
                    <div class="customer-email"><?= Html::encode($order->user->email) ?></div>
                    <div class="customer-phone"><?= Html::encode($order->user->phone) ?></div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="order-card" style="margin-top: 16px;">
            <div class="card-title">Items Ordered</div>
            <div class="item-list">
                <?php foreach ($order->items as $item): ?>
                    <div class="item">
                        <div>
                            <div class="item-name"><?= Html::encode($item->listing?->name ?? 'Unknown Service') ?></div>
                            <div class="item-qty">Qty: <?= $item->quantity ?></div>
                        </div>
                        <div class="item-price">KSh <?= number_format($item->price, 0) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>KSh <?= number_format($order->total_amount, 0) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>KSh <?= number_format($order->total_amount, 0) ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <?php if ($payment): ?>
            <div class="order-card" style="margin-top: 16px;">
                <div class="card-title">Payment Details</div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value"><?= Html::encode($payment->methodLabel()) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value"><?= ucfirst($payment->status) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value"><?= date('M d, Y H:i', strtotime($payment->created_at)) ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="order-card">
            <div class="card-title">Order Status</div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="status-badge status-<?= $order->status ?>">
                    <?= ucfirst(str_replace('_', ' ', $order->status)) ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status</span>
                <span class="detail-value" style="color: <?= $order->payment_status === 'paid' ? '#22c55e' : '#fbbf24' ?>;">
                    <?= ucfirst($order->payment_status) ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Created</span>
                <span class="detail-value"><?= date('M d, Y', strtotime($order->created_at)) ?></span>
            </div>

            <!-- Order Actions -->
            <?php if ($order->status === 'awaiting_payment'): ?>
                <a href="<?= Url::to(['/provider/accept-order', 'id' => $order->id]) ?>" class="btn-action primary">Accept Order</a>
            <?php elseif (in_array($order->status, ['processing', 'out_for_delivery'])): ?>
                <a href="<?= Url::to(['/provider/complete-order', 'id' => $order->id]) ?>" class="btn-action primary">Mark Complete</a>
            <?php endif; ?>
            <?php if ($order->payment_status !== 'paid'): ?>
                <a href="<?= Url::to(['/provider/record-cash-payment', 'id' => $order->id]) ?>" class="btn-action secondary">Record Cash Payment</a>
            <?php endif; ?>
        </div>
    </div>
</div>