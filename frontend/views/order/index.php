<?php
/** @var common\models\Order[] $orders */
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
<div class="hl-section" style="padding-top:2.5rem;max-width:1000px;">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h2 style="margin-bottom:0.25rem;">My Orders</h2>
      <p style="color:var(--text-muted);margin:0;"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?> placed</p>
    </div>
    <a href="<?= Url::to(['/browse']) ?>" class="btn-hl-primary btn-sm">
      <i class="bi bi-plus-lg"></i> New Order
    </a>
  </div>

  <?php if (empty($orders)): ?>
    <div style="text-align:center;padding:5rem 2rem;background:var(--surface);border-radius:var(--radius-lg);border:1px solid var(--border);">
      <i class="bi bi-bag-x" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:1rem;"></i>
      <h4>No orders yet</h4>
      <p style="color:var(--text-muted);margin-bottom:1.5rem;">Explore services and products to place your first order.</p>
      <a href="<?= Url::to(['/browse']) ?>" class="btn-hl-primary">Browse Listings</a>
    </div>
  <?php else: ?>
    <div class="d-flex flex-column gap-3">
      <?php foreach ($orders as $order): ?>
      <a href="<?= Url::to(['/orders/' . $order->id]) ?>" class="hl-card" style="text-decoration:none;color:inherit;transition:all var(--transition);"
         onmouseover="this.style.boxShadow='var(--shadow-md)';this.style.borderColor='var(--hl-blue)'"
         onmouseout="this.style.boxShadow='';this.style.borderColor=''">
        <div class="hl-card-body d-flex align-items-center gap-4 flex-wrap">
          <!-- Ref + date -->
          <div style="min-width:160px;">
            <div style="font-weight:700;font-size:0.95rem;color:var(--hl-blue);"><?= Html::encode($order->reference) ?></div>
            <div style="font-size:0.78rem;color:var(--text-muted);"><?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d M Y, g:ia') ?></div>
          </div>
          <!-- Items -->
          <div style="flex:1;min-width:200px;">
            <?php foreach ($order->items as $item): ?>
              <div style="font-size:0.875rem;font-weight:500;"><?= Html::encode($item->name) ?></div>
            <?php endforeach; ?>
            <div style="font-size:0.78rem;color:var(--text-muted);">
              <?= Html::encode($order->provider->business_name ?? '') ?>
            </div>
          </div>
          <!-- Amount -->
          <div style="text-align:right;min-width:130px;">
            <div style="font-size:1.05rem;font-weight:800;color:var(--hl-blue);">KES <?= number_format($order->total_amount, 2) ?></div>
            <div style="font-size:0.75rem;color:var(--text-muted);"><?= ucfirst($order->payment_type) ?></div>
          </div>
          <!-- Statuses -->
          <div class="d-flex flex-column gap-1 align-items-end" style="min-width:140px;">
            <span class="badge-status badge-<?= $order->statusBadgeClass() ?>"><?= $order->statusLabel() ?></span>
            <span class="badge-status <?= $order->isPaid() ? 'badge-success' : 'badge-warning' ?>"><?= ucfirst($order->payment_status) ?></span>
          </div>
          <i class="bi bi-chevron-right" style="color:var(--text-muted);"></i>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
</div>
