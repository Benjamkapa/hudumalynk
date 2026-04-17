<?php
/** @var yii\web\View $this */
/** @var common\models\Order $order */
/** @var common\models\Payment|null $payment */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Order #' . sprintf('%06d', $order->id);
$status = $order->status ?? 'pending';
$badgeCls = match($status) { 'completed' => 'paid', 'pending','awaiting_payment' => 'pend', default => 'new' };
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.ov-grid{display:grid;grid-template-columns:1fr 300px;gap:14px;align-items:start;}
.ov-meta-box{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px;display:flex;flex-direction:column;gap:12px;}
.ov-meta-row{display:flex;justify-content:space-between;align-items:flex-start;font-size:12px;}
.ov-meta-row .lbl{color:var(--text3);}
.ov-meta-row .val{color:var(--text1);font-weight:600;text-align:right;max-width:60%;}
.ov-section{margin-bottom:12px;}
.timeline{display:flex;flex-direction:column;gap:0;}
.tl-item{display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);}
.tl-item:last-child{border-bottom:none;}
.tl-dot{width:8px;height:8px;border-radius:50%;margin-top:4px;flex-shrink:0;}
.tl-dot.done{background:var(--teal);}
.tl-dot.curr{background:var(--acc);box-shadow:0 0 0 3px var(--acc-pale);}
.tl-dot.wait{background:var(--bg3);}
.items-tbl{width:100%;border-collapse:collapse;}
.items-tbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text3);padding:8px 12px;border-bottom:1px solid var(--border);text-align:left;}
.items-tbl td{padding:10px 12px;border-bottom:1px solid var(--border);font-size:12.5px;}
.items-tbl tr:last-child td{border-bottom:none;}
@media(max-width:768px){.ov-grid{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?= Url::to(['/admin/orders']) ?>" class="hl-btn-g" style="padding:6px 10px;font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="15 18 9 12 15 6"/></svg> Orders
        </a>
        <h1 class="hl-pg-title">Order #<?= strtoupper(sprintf('%06d', $order->id)) ?></h1>
        <span class="hl-badge <?= $badgeCls ?>"><?= ucfirst(str_replace('_', ' ', $status)) ?></span>
    </div>
    <div style="font-size:11.5px;color:var(--text3);"><?= date('l, d F Y · H:i', strtotime($order->created_at ?? 'now')) ?></div>
</div>

<div class="ov-grid">
    <!-- Left: Items + timeline -->
    <div style="display:flex;flex-direction:column;gap:12px;">
        <!-- Order items -->
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Order Items</span></div>
            <table class="items-tbl">
                <thead><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
                <tbody>
                <?php if (!empty($order->items)): ?>
                    <?php foreach ($order->items as $i => $item):
                        $bg = $colors[$i % count($colors)];
                        $name = $item->listing->title ?? 'Item #' . $item->listing_id;
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div style="width:30px;height:30px;border-radius:var(--r-sm);background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;"><?= strtoupper(substr($name, 0, 2)) ?></div>
                                <span style="font-weight:500;color:var(--text1);"><?= Html::encode($name) ?></span>
                            </div>
                        </td>
                        <td style="color:var(--text2);"><?= $item->quantity ?? 1 ?></td>
                        <td style="color:var(--text2);">KES <?= number_format($item->unit_price ?? 0) ?></td>
                        <td style="font-weight:700;color:var(--text1);">KES <?= number_format(($item->quantity ?? 1) * ($item->unit_price ?? 0)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text3);font-size:12px;">No item details available</td></tr>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background:var(--bg);">
                        <td colspan="3" style="padding:10px 12px;font-size:12px;font-weight:700;color:var(--text1);">Total</td>
                        <td style="padding:10px 12px;font-size:14px;font-weight:800;color:var(--text1);">KES <?= number_format($order->total_amount ?? 0) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes -->
        <?php if ($order->notes): ?>
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Order Notes</span></div>
            <div style="padding:14px 16px;font-size:12.5px;color:var(--text2);line-height:1.55;"><?= Html::encode($order->notes) ?></div>
        </div>
        <?php endif; ?>

        <!-- Status Timeline -->
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Order Timeline</span></div>
            <div style="padding:4px 16px;">
            <div class="timeline">
                <?php
                $stages = [
                    ['label' => 'Order Placed',       'key' => 'created',          'date' => $order->created_at],
                    ['label' => 'Payment Received',   'key' => 'awaiting_payment', 'date' => null],
                    ['label' => 'Processing',         'key' => 'processing',       'date' => null],
                    ['label' => 'Out for Delivery',   'key' => 'out_for_delivery', 'date' => null],
                    ['label' => 'Completed',          'key' => 'completed',        'date' => null],
                ];
                $stageOrder = ['created','awaiting_payment','processing','out_for_delivery','completed'];
                $currentIdx = array_search($status, $stageOrder) ?: 0;
                foreach ($stages as $sidx => $stage):
                    $dotClass = $sidx < $currentIdx ? 'done' : ($sidx === $currentIdx ? 'curr' : 'wait');
                ?>
                <div class="tl-item">
                    <div class="tl-dot <?= $dotClass ?>"></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:<?= $dotClass === 'wait' ? 'var(--text3)' : 'var(--text1)' ?>;"><?= $stage['label'] ?></div>
                        <?php if ($stage['date'] || $dotClass === 'done'): ?>
                        <div style="font-size:11px;color:var(--text3);margin-top:2px;"><?= $stage['date'] ? date('d M Y · H:i', strtotime($stage['date'])) : 'Completed' ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            </div>
        </div>
    </div>

    <!-- Right: Summary -->
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="ov-meta-box">
            <div style="font-size:12.5px;font-weight:700;color:var(--text1);padding-bottom:10px;border-bottom:1px solid var(--border);">Order Summary</div>
            <div class="ov-meta-row"><span class="lbl">Reference</span><span class="val" style="font-family:monospace;">#<?= strtoupper(sprintf('%06d', $order->id)) ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Status</span><span class="hl-badge <?= $badgeCls ?>"><?= ucfirst(str_replace('_', ' ', $status)) ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Payment type</span><span class="val"><?= ucfirst(str_replace('_', ' ', $order->payment_type ?? '—')) ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Payment status</span><span class="val"><?= ucfirst($order->payment_status ?? '—') ?></span></div>
            <div class="ov-meta-row" style="padding-top:8px;border-top:1px solid var(--border);"><span class="lbl" style="font-weight:700;color:var(--text1);">Total</span><span style="font-size:16px;font-weight:800;color:var(--text1);">KES <?= number_format($order->total_amount ?? 0) ?></span></div>
        </div>

        <!-- Customer -->
        <?php if ($order->user): ?>
        <div class="ov-meta-box">
            <div style="font-size:12.5px;font-weight:700;color:var(--text1);padding-bottom:10px;border-bottom:1px solid var(--border);">Customer</div>
            <div class="ov-meta-row"><span class="lbl">Name</span><span class="val"><?= Html::encode($order->user->getFullName()) ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Email</span><span class="val" style="font-size:11px;"><?= Html::encode($order->user->email ?? '—') ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Phone</span><span class="val"><?= Html::encode($order->user->phone ?? '—') ?></span></div>
            <a href="<?= Url::to(['/admin/user-view', 'id' => $order->user_id]) ?>" class="hl-btn-g" style="font-size:11px;padding:6px;justify-content:center;margin-top:4px;">View profile</a>
        </div>
        <?php endif; ?>

        <!-- Vendor -->
        <?php if ($order->provider): ?>
        <div class="ov-meta-box">
            <div style="font-size:12.5px;font-weight:700;color:var(--text1);padding-bottom:10px;border-bottom:1px solid var(--border);">Vendor</div>
            <div class="ov-meta-row"><span class="lbl">Business</span><span class="val"><?= Html::encode($order->provider->business_name) ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Phone</span><span class="val"><?= Html::encode($order->provider->phone ?? '—') ?></span></div>
            <div class="ov-meta-row"><span class="lbl">City</span><span class="val"><?= Html::encode($order->provider->city ?? '—') ?></span></div>
            <a href="<?= Url::to(['/admin/vendor-view', 'id' => $order->provider_id]) ?>" class="hl-btn-g" style="font-size:11px;padding:6px;justify-content:center;margin-top:4px;">View vendor</a>
        </div>
        <?php endif; ?>

        <!-- Payment -->
        <?php if ($payment): ?>
        <div class="ov-meta-box">
            <div style="font-size:12.5px;font-weight:700;color:var(--text1);padding-bottom:10px;border-bottom:1px solid var(--border);">M-Pesa Payment</div>
            <div class="ov-meta-row"><span class="lbl">Amount</span><span class="val">KES <?= number_format($payment->amount ?? 0) ?></span></div>
            <div class="ov-meta-row"><span class="lbl">Status</span><span class="hl-badge <?= $payment->status === 'completed' ? 'paid' : 'pend' ?>"><?= ucfirst($payment->status) ?></span></div>
            <?php if ($payment->mpesa_receipt): ?>
            <div class="ov-meta-row"><span class="lbl">Receipt</span><span class="val" style="font-family:monospace;font-size:11px;"><?= Html::encode($payment->mpesa_receipt) ?></span></div>
            <?php endif; ?>
            <?php if ($payment->paid_at): ?>
            <div class="ov-meta-row"><span class="lbl">Paid at</span><span class="val"><?= date('d M Y · H:i', strtotime($payment->paid_at)) ?></span></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
