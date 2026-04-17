<?php
/** @var common\models\Order $order */
/** @var string $checkoutRequestId */
/** @var float $amountToPay */
/** @var array $prices */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;display:flex;align-items:center;">
<div class="hl-section" style="max-width:560px;padding:3rem 1.5rem;text-align:center;">

  <div class="hl-card" style="padding:3rem 2rem;">
    <!-- Spinner -->
    <div style="width:80px;height:80px;border-radius:50%;background:var(--hl-green-light);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;animation:pulse 1.5s infinite;">
      <i class="bi bi-phone-vibrate-fill" style="font-size:2.2rem;color:var(--hl-green-dark);"></i>
    </div>

    <h3 style="margin-bottom:0.5rem;">Check Your Phone</h3>
    <p style="color:var(--text-secondary);margin-bottom:1.5rem;">
      An M-Pesa payment prompt of <strong>KES <?= number_format($amountToPay, 2) ?></strong> has been sent to your phone.<br>
      Enter your M-Pesa PIN to complete the payment.
    </p>

    <div class="hl-alert hl-alert-info mb-3" style="text-align:left;">
      <i class="bi bi-clock"></i>
      <span>This prompt expires in <strong>60 seconds</strong>. Do not close this page.</span>
    </div>

    <div id="mpesa-status-msg" style="font-size:0.9rem;color:var(--text-muted);margin-bottom:1.5rem;min-height:1.5rem;">
      Waiting for M-Pesa confirmation…
    </div>

    <!-- Progress bar -->
    <div style="height:4px;background:var(--border);border-radius:999px;overflow:hidden;margin-bottom:2rem;">
      <div style="height:100%;background:var(--hl-gradient);border-radius:999px;width:0%;transition:width 60s linear;" id="mpesa-progress"></div>
    </div>

    <div style="font-size:0.78rem;color:var(--text-muted);">
      Order Reference: <strong><?= Html::encode($order->reference) ?></strong>
    </div>
  </div>

  <p style="margin-top:1rem;font-size:0.82rem;color:var(--text-muted);">
    Didn't receive the prompt?
    <a href="<?= Url::to(['/order/pay', 'id' => $order->id]) ?>">Try again</a>
    &nbsp;·&nbsp;
    <a href="<?= Url::to(['/orders/' . $order->id]) ?>">Check order status</a>
  </p>
</div>
</div>

<style>
@keyframes pulse {
  0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(62,201,79,0.4); }
  50% { transform: scale(1.05); box-shadow: 0 0 0 12px rgba(62,201,79,0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Start progress bar animation
  setTimeout(() => { document.getElementById('mpesa-progress').style.width = '100%'; }, 100);

  // Poll for payment status
  window.HL.initMpesaPolling(
    '<?= Html::encode($checkoutRequestId) ?>',
    '<?= Url::to(['/order/payment-status']) ?>',
    '<?= Url::to(['/orders/' . $order->id]) ?>'
  );
});
</script>
