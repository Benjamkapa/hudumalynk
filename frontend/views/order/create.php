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
                <div style="font-size:0.82rem;color:var(--text-muted);display:flex;align-items:center;gap:0.5rem;">
                  <span>by <?= Html::encode($listing->provider->business_name ?? '') ?></span>
                  <!-- <button class="btn btn-link btn-sm p-0 text-decoration-none" onclick="loadProviderModal(<?= $listing->provider->id ?>)" style="font-size:0.75rem;color:var(--hl-blue);">
                    <i class="bi bi-eye"></i> View Full Profile
                  </button> -->
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

<!-- Provider Modal -->
<div class="modal fade" id="providerModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content hl-card" style="border:none;box-shadow:var(--shadow-lg);border-radius:var(--radius-xl);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" id="modalTitle">Loading...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="modalBody">
        <div class="text-center py-4" id="modalLoading">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Loading provider details...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function loadProviderModal(id) {
  const modal = new bootstrap.Modal(document.getElementById('providerModal'));
  const modalTitle = document.getElementById('modalTitle');
  const modalBody = document.getElementById('modalBody');
  const loading = document.getElementById('modalLoading');
  
  modalTitle.textContent = 'Loading...';
  loading.style.display = 'block';
  modalBody.innerHTML = '<div class="text-center py-4" id="modalLoading"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading provider details...</p></div>';
  
  fetch('/api/provider/' + id)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        modalTitle.textContent = data.provider.business_name;
        modalBody.innerHTML = `
          <div style="text-align:center;">
            ${data.provider.logo ? 
              `<img src="/uploads/${data.provider.logo}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 1rem;display:block;border:3px solid var(--hl-blue-light);">` : 
              `<div style="width:80px;height:80px;margin:0 auto 1rem;background:var(--hl-gradient);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">${data.provider.business_name.substring(0,2).toUpperCase()}</div>`
            }
            <h5 style="margin:0 0 0.5rem;">${data.provider.business_name}</h5>
            ${data.provider.is_verified ? '<span class="hl-verified-badge mb-2"><i class="bi bi-patch-check-fill"></i> Verified Provider</span>' : ''}
            ${data.provider.rating > 0 ? `<div style="margin:1rem 0;"><span class="hl-stars">${'★'.repeat(Math.round(data.provider.rating))}</span> <strong>${data.provider.rating.toFixed(1)}</strong> (${data.provider.total_reviews} reviews)</div>` : ''}
            <div style="font-size:0.875rem;color:var(--text-secondary);line-height:1.6;">
              <div><i class="bi bi-geo-alt" style="color:var(--hl-blue);"></i> ${data.provider.city || data.provider.address || 'Location not specified'}</div>
              ${data.provider.phone ? `<div style="margin-top:0.5rem;"><i class="bi bi-telephone" style="color:var(--hl-green);"></i> ${data.provider.phone}</div>` : ''}
            </div>
            ${data.provider.description ? `<p style="margin:1.25rem 0 1rem;color:var(--text-secondary);line-height:1.7;">${data.provider.description.replace(/\\n/g, '<br>')}</p>` : ''}
            <div class="d-flex gap-2 mt-3">
              <a href="/provider/${data.provider.id}/${data.provider.slug}" class="btn-hl-primary btn-sm flex-fill" target="_blank">View Full Profile</a>
              <button class="btn-hl-ghost btn-sm flex-fill" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        `;
      } else {
        modalTitle.textContent = 'Error';
        modalBody.innerHTML = '<p class="text-danger text-center">Could not load provider details. <button class="btn btn-link p-0" onclick="loadProviderModal(' + id + ')">Retry</button></p>';
      }
    })
    .catch(() => {
      modalTitle.textContent = 'Error';
      modalBody.innerHTML = '<p class="text-danger text-center">Network error. Please check your connection.</p>';
    });
  
  modal.show();
}
</script>