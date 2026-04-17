<?php
/** @var common\models\Order $order */
/** @var float $amountToPay */
/** @var array $prices */
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;
$user = Yii::$app->user->identity;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
  <div class="hl-section" style="max-width:700px;padding-top:2.5rem;">

    <!-- Steps -->
    <div class="hl-steps mb-4">
      <div class="hl-step done">
        <div class="hl-step-circle"><i class="bi bi-check2"></i></div><span class="hl-step-label">Review</span>
      </div>
      <div class="hl-step active">
        <div class="hl-step-circle">2</div><span class="hl-step-label">Payment</span>
      </div>
      <div class="hl-step">
        <div class="hl-step-circle">3</div><span class="hl-step-label">Confirm</span>
      </div>
    </div>

    <div class="hl-card">      
      <div class="hl-card-header">
        <h4><i class="bi bi-phone-fill btn-hl-green w-100" style="justify-content:center;padding:0.85rem;font-size:1rem;border:1px solid --hl-green;color:var(--hl-green);"></i> Complete Payment</h4>
        <span class="badge-status badge-warning">Ref: <?= Html::encode($order->reference) ?></span>
      </div>
      <div class="hl-card-body">

        <!-- Amount display -->
        <div class="hl-currency-display mb-4">
          <div class="amount-kes">
            <span class="label"><?= $order->payment_type === 'partial' ? 'Deposit (30%)' : 'Amount Due' ?></span>
            <span class="value"><?= number_format($amountToPay, 2) ?></span>
          </div>
          <div class="divider"></div>
          <div class="amount-usd">
            <span class="label">USD</span>
            <span class="value usd"><?= Yii::$app->currency->format($amountToPay, 'USD') ?></span>
          </div>
        </div>
        <?php if ($order->payment_type === 'partial'): ?>
          <div class="hl-alert hl-alert-info mb-3" style="font-size:0.84rem;">
            <i class="bi bi-info-circle"></i>
            <span>Deposit payment only. Balance of <strong>KES <?= number_format($order->balance_amount, 2) ?></strong>
              will be collected on delivery/completion.</span>
          </div>
        <?php endif; ?>

        <!-- Payment method tabs -->
        <ul class="nav nav-tabs mb-3" id="payTabs">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mpesa-tab" type="button">
              <i class="bi bi-phone"></i> M-Pesa
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#card-tab" type="button">
              <i class="bi bi-credit-card"></i> Card
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <!-- M-Pesa STK Push -->
          <div class="tab-pane fade show active" id="mpesa-tab">
            <form method="post" action="<?= Url::to(['/order/pay', 'id' => $order->id]) ?>">
              <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
              <input type="hidden" name="method" value="mpesa">
              <div class="hl-form-group">
                <label class="hl-label">M-Pesa Phone Number</label>
                <div class="hl-input-icon">
                  <i class="bi bi-phone"></i>
                  <input type="tel" name="phone" class="hl-input" value="<?= Html::encode($user->phone) ?>"
                    placeholder="0712 345 678" style="padding-left:2.5rem;">
                </div>
                <small style="color:var(--text-muted);">You'll receive an M-Pesa prompt on this number. Enter your PIN
                  to confirm.</small>
              </div>
              <button type="submit" class="btn-hl-green w-100"
                style="justify-content:center;padding:0.85rem;font-size:1rem;border:1px solid #22c55e;">
                <i class="bi bi-phone-fill"></i> Send M-Pesa Prompt
              </button>
            </form>
          </div>

          <!-- Card (Flutterwave) -->
          <div class="tab-pane fade" id="card-tab">
            <div class="hl-alert hl-alert-info mb-3" style="font-size:0.84rem;">
              <i class="bi bi-shield-lock"></i>
              <span>Card payments are powered by Flutterwave — secure & encrypted.</span>
            </div>
            <form method="post" action="<?= Url::to(['/order/pay', 'id' => $order->id]) ?>">
              <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
              <input type="hidden" name="method" value="card">
              <button type="submit" class="btn-hl-primary w-100"
                style="justify-content:center;padding:0.85rem;font-size:1rem;">
                <i class="bi bi-credit-card-fill"></i> Pay with Card (KES <?= number_format($amountToPay, 2) ?>)
              </button>
            </form>
          </div>
        </div>

        <div class="text-center mt-3">
          <a href="<?= Url::to(['/orders/' . $order->id]) ?>" style="font-size:0.82rem;color:var(--text-muted);">
            <i class="bi bi-arrow-left"></i> Back to order
          </a>
        </div>
      </div>
    </div>
  </div>
</div>