<?php
/** @var common\models\Provider $provider */
/** @var common\models\Commission[] $commissions */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Earnings & Commissions';
?>
<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Earnings & Commissions</h1>
        <div class="hl-pg-sub">Track your completed sales and platform fees</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px; margin-bottom:20px;">
  <div class="hl-card" style="padding:24px; display:flex; align-items:center; gap:16px;">
    <div style="width:48px;height:48px;background:var(--teal-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--teal);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:24px;height:24px;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </div>
    <div>
      <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Net Lifetime Earnings</div>
      <div style="font-size:22px;font-weight:800;color:var(--text1);line-height:1;">KES <?= number_format($provider->getTotalEarnings(), 2) ?></div>
    </div>
  </div>
</div>

<div class="hl-card">
  <div class="hl-card-head">
    <span class="hl-card-title">Transaction History</span>
  </div>
  <table class="hl-tbl">
    <thead>
      <tr>
        <th>Order Ref</th>
        <th>Date</th>
        <th>Order Total</th>
        <th>Platform Fee</th>
        <th>Net Earnings</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($commissions)): ?>
        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text3);">No earnings recorded yet. Fulfill orders to see them here.</td></tr>
      <?php else: ?>
        <?php foreach ($commissions as $c): ?>
        <tr>
          <td><a href="<?= Url::to(['/provider/orders', '#' => $c->order->reference]) ?>" style="font-weight:700;color:var(--acc);text-decoration:none;"><?= Html::encode($c->order->reference) ?></a></td>
          <td style="font-size:11px;color:var(--text3);"><?= Yii::$app->formatter->asDatetime($c->created_at, 'php:d M Y g:ia') ?></td>
          <td style="font-weight:600;font-size:12.5px;">KES <?= number_format($c->order_amount, 2) ?></td>
          <td style="color:var(--rose);font-size:11.5px;">- KES <?= number_format($c->amount, 2) ?> (<?= $c->rate ?>%)</td>
          <td style="font-weight:700;color:var(--teal);font-size:12.5px;">KES <?= number_format($c->order_amount - $c->amount, 2) ?></td>
          <td><span class="hl-badge paid">SETTLED</span></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
