<?php
/** @var common\models\Listing $listing */
/** @var int $quantity */
/** @var float $totalAmount */
/** @var string $paymentType */
/** @var float $deposit */
/** @var array $prices */
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
  <div class="hl-section" style="max-width:900px;padding-top:2.5rem;">

    <!-- Steps -->
    <div class="hl-steps mb-4">
      <div class="hl-step active done">
        <div class="hl-step-circle"><i class="bi bi-check2"></i></div><span class="hl-step-label">Review</span>
      </div>
      <div class="hl-step active">
        <div class="hl-step-circle">2</div><span class="hl-step-label">Payment</span>
      </div>
      <div class="hl-step">
        <div class="hl-step-circle">3</div><span class="hl-step-label">Confirm</span>
      </div>
    </div>

    <div class="row g-4">
      <!-- Order summary -->
      <div class="col-md-7">
        <div class="hl-card mb-3">
          <div class="hl-card-header">
            <h4><i class="bi bi-bag-check" style="color:var(--hl-blue);"></i> Order Summary</h4>
          </div>
          <div class="hl-card-body">
            <div class="d-flex gap-3 align-items-start">
              <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>"
                style="width:80px;height:60px;object-fit:cover;border-radius:var(--radius);" alt="">
              <div>
                <div style="font-weight:700;"><?= Html::encode($listing->name) ?></div>
                <div style="font-size:0.82rem;color:var(--text-muted);">
                  <?= Html::encode($listing->provider->business_name ?? '') ?>
                </div>
                <span class="hl-listing-badge badge-<?= $listing->type ?>"
                  style="position:relative;top:0;left:0;margin-top:0.4rem;display:inline-flex;"><?= ucfirst($listing->type) ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Delivery / notes -->
        <div class="hl-card">
          <div class="hl-card-header">
            <h4><i class="bi bi-geo-alt" style="color:var(--hl-green);"></i> Delivery Details</h4>
          </div>
          <div class="hl-card-body">
            <?php $form = yii\widgets\ActiveForm::begin(['id' => 'order-form']); ?>
            <div class="hl-form-group">
              <label class="hl-label">Quantity</label>
              <input type="number" name="quantity" class="hl-input" value="<?= $quantity ?>" min="1"
                <?= $listing->isService() ? 'max="1" readonly' : '' ?> style="width:100px;">
            </div>
            <?php if ($listing->isProduct()): ?>
              <div class="hl-form-group">
                <label class="hl-label">Delivery Address</label>
                <textarea name="delivery_address" class="hl-textarea" rows="2"
                  placeholder="Enter your delivery address (street, area, city)…"></textarea>
              </div>
            <?php endif; ?>
            <div class="hl-form-group">
              <label class="hl-label">Notes to Provider <span
                  style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
              <textarea name="notes" class="hl-textarea" rows="2"
                placeholder="Any specific requirements or instructions…"></textarea>
            </div>

            <!-- Payment type selection -->
            <label class="hl-label">Payment Method</label>
            <div class="d-flex flex-column gap-2 mb-3">
              <?php
              $types = [
                Order::PAYMENT_FULL => ['icon' => 'bi-credit-card-2-front', 'label' => 'Full Payment', 'desc' => 'Pay ' . $prices['KES'] . ' now to confirm immediately.'],
                Order::PAYMENT_PARTIAL => ['icon' => 'bi-cash-coin', 'label' => 'Deposit (30%)', 'desc' => 'Pay KES ' . number_format($deposit, 2) . ' deposit now, balance on delivery.'],
                Order::PAYMENT_DELIVERY => ['icon' => 'bi-truck', 'label' => 'Pay on Delivery', 'desc' => 'Pay after receiving. No immediate payment or M-Pesa prompt required.'],
              ];
              $recommended = $paymentType;
              foreach ($types as $val => $t):
                $disabled = ($val === Order::PAYMENT_DELIVERY && $totalAmount > 2000) ? 'style="opacity:0.5;pointer-events:none;"' : '';
                ?>
                <label
                  class="hl-payment-card d-flex gap-3 align-items-start <?= $val === $recommended ? 'selected' : '' ?>"
                  <?= $disabled ?>>
                  <input type="radio" name="payment_type" value="<?= $val ?>" <?= $val === $recommended ? 'checked' : '' ?>
                    style="margin-top:0.2rem;accent-color:var(--hl-blue);"
                    onchange="document.querySelectorAll('.hl-payment-card').forEach(c=>c.classList.remove('selected'));this.closest('.hl-payment-card').classList.add('selected')">
                  <div>
                    <div style="font-weight:700;"><i class="<?= $t['icon'] ?>"
                        style="color:var(--hl-blue);margin-right:0.4rem;"></i><?= $t['label'] ?></div>
                    <div style="font-size:0.82rem;color:var(--text-muted);"><?= $t['desc'] ?></div>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>

            <input type="hidden" name="confirm" value="1">
            <?= Html::submitButton('<i class="bi bi-arrow-right-circle-fill"></i> Confirm &amp; Continue', [
              'class' => 'btn-hl-primary w-100',
              'style' => 'justify-content:center;padding:0.85rem;font-size:1rem;',
              'id' => 'order-confirm',
            ]) ?>
            <?php yii\widgets\ActiveForm::end(); ?>
          </div>
        </div>
      </div>

      <!-- Price summary -->
      <div class="col-md-5">
        <div class="hl-card" style="position:sticky;top:calc(var(--navbar-h)+1rem);">
          <div class="hl-card-header">
            <h4>Price Summary</h4>
          </div>
          <div class="hl-card-body">
            <div class="d-flex justify-content-between mb-2">
              <span style="color:var(--text-secondary);">Unit Price</span>
              <span style="font-weight:600;"><?= Yii::$app->currency->format($listing->price, 'KES') ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span style="color:var(--text-secondary);">Quantity</span>
              <span style="font-weight:600;"><?= $quantity ?></span>
            </div>
            <hr style="border-color:var(--border);">
            <div class="d-flex justify-content-between mb-1">
              <span style="font-weight:700;">Total (KES)</span>
              <span
                style="font-weight:800;color:var(--hl-blue);font-size:1.15rem;"><?= number_format($totalAmount, 2) ?></span>
            </div>
            <!-- KES/USD Toggle display -->
            <div class="hl-currency-display">
              <div class="amount-kes"><span class="label">KES</span><span
                  class="value"><?= number_format($totalAmount, 2) ?></span></div>
              <div class="divider"></div>
              <div class="amount-usd"><span class="label">USD</span><span class="value usd"><?= $prices['USD'] ?></span>
              </div>
            </div>
            <div class="d-flex gap-2 mt-3" style="font-size:0.78rem;color:var(--text-muted);">
              <div><i class="bi bi-shield-check" style="color:var(--hl-green);"></i> Secure & protected</div>
              <div><i class="bi bi-phone" style="color:var(--hl-blue);"></i> M-Pesa & card</div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>