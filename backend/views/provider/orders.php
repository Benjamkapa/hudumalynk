<?php
/** @var common\models\Provider $provider */
/** @var common\models\Order[] $orders */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Orders';
?>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">My Orders</h1>
        <div class="hl-pg-sub">Track and fulfill customer orders</div>
    </div>
    <div style="display:flex;gap:10px;">
        <div class="hl-search-wrap" style="height:36px;margin:0;width:240px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="hl-search-input" type="text" placeholder="Search Reference...">
        </div>
    </div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Recent Transactions</span>
        <span style="font-size:11px;color:var(--text3);"><?= count($orders) ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead>
            <tr>
                <th>Reference & Date</th>
                <th>Customer</th>
                <th>Item(s)</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text3);">No orders received yet. Start by optimizing your listings!</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $o):
                    $statusClass = in_array($o->status, ['completed','pending','cancelled']) ? $o->status : 'pending';
                    $paidBadge = $o->isPaid() ? 'paid' : 'pend';
                ?>
                <tr>
                    <td>
                        <div style="font-size:12.5px;font-weight:700;color:var(--text1);"><?= Html::encode($o->reference) ?></div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Yii::$app->formatter->asDatetime($o->created_at, 'php:d M Y, g:ia') ?></div>
                    </td>
                    <td>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($o->user->getFullName() ?? '—') ?></div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($o->user->phone) ?></div>
                    </td>
                    <td style="font-size:12px;color:var(--text2);max-width:200px;">
                        <?php foreach ($o->items as $idx => $i): ?>
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= Html::encode($i->name) ?>">
                                <span style="font-weight:700;color:var(--text1);"><?= $i->quantity ?>x</span> <?= Html::encode($i->name) ?>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <div style="font-weight:700;color:var(--text1);font-size:12.5px;">KES <?= number_format($o->total_amount, 2) ?></div>
                        <div style="font-size:10px;text-transform:uppercase;color:var(--text3);letter-spacing:.05em;"><?= ucfirst($o->payment_type) ?></div>
                    </td>
                    <td>
                        <!-- Utilizing the badge styles from the new provider dashboard css (or unified hl-badge) -->
                        <span class="hl-badge <?= $statusClass === 'completed' ? 'paid' : ($statusClass==='cancelled'?'danger':$statusClass) ?>">
                            <?= $o->statusLabel() ?>
                        </span>
                    </td>
                    <td>
                        <span class="hl-badge <?= $paidBadge ?>">
                            <?= strtoupper($o->payment_status) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <?php if ($o->status === \common\models\Order::STATUS_AWAITING_PAYMENT): ?>
                                <?= Html::a('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>', 
                                    Url::to(['/provider/accept-order', 'id' => $o->id]),
                                    ['class' => 'hl-btn-g', 'data-method' => 'post', 'style' => 'width:28px;height:28px;padding:5px;color:var(--teal);', 'title' => 'Accept Order']) ?>
                            <?php elseif (in_array($o->status, [\common\models\Order::STATUS_PROCESSING, \common\models\Order::STATUS_DEPOSIT_PAID])): ?>
                                <?= Html::a('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12l5.25 5 2.625-3M8 12l5.25 5L22 7"/></svg>', 
                                    Url::to(['/provider/complete-order', 'id' => $o->id]),
                                    ['class' => 'hl-btn-g', 'data-method' => 'post', 'style' => 'width:28px;height:28px;padding:5px;color:var(--acc);', 'title' => 'Mark as Complete', 'data-confirm' => 'Mark order as fulfilled?']) ?>
                            <?php endif; ?>
                            <a href="<?= Url::to(['/provider/orders', 'id' => $o->id]) ?>" class="hl-btn-g" style="width:28px;height:28px;padding:5px;" title="View Details">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
