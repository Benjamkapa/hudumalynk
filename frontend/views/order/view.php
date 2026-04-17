<?php
/** @var common\models\Order $order */
/** @var array $prices */
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;
$provider = $order->provider;

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
  <div class="hl-section" style="max-width:900px;padding-top:2.5rem;">

    <!-- Breadcrumb -->
    <nav style="font-size:0.82rem;color:var(--text-muted);margin-bottom:1.5rem;">
      <a href="<?= Url::to(['/']) ?>">Home</a> / <a href="<?= Url::to(['/orders']) ?>">My Orders</a> /
      <?= Html::encode($order->reference) ?>
    </nav>

    <!-- Status Header -->
    <div class="hl-card mb-4" style="background:var(--hl-gradient-hero);border:none;">
      <div class="hl-card-body d-flex align-items-center justify-content-between flex-wrap gap-3"
        style="padding:1.75rem;">
        <div>
          <div
            style="font-size:0.8rem;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.25rem;">
            Order Reference</div>
          <h3 style="color:#fff;margin:0;letter-spacing:-0.02em;"><?= Html::encode($order->reference) ?></h3>
          <div style="font-size:0.82rem;color:rgba(255,255,255,0.7);margin-top:0.25rem;">
            Placed on <?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d M Y \a\t g:ia') ?>
          </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <span class="badge-status badge-<?= $order->statusBadgeClass() ?>"
            style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.25);"><?= $order->statusLabel() ?></span>
          <span
            class="badge-status <?= $order->isPaid() ? 'badge-success' : 'badge-warning' ?>"><?= ucfirst($order->payment_status) ?></span>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-7">
        <!-- Items -->
        <div class="hl-card mb-3">
          <div class="hl-card-header">
            <h4>Order Items</h4>
          </div>
          <div class="hl-card-body">
            <?php foreach ($order->items as $item): ?>
              <div class="d-flex justify-content-between align-items-start"
                style="padding:0.5rem 0;border-bottom:1px solid var(--border);">
                <div>
                  <div style="font-weight:600;"><?= Html::encode($item->name) ?></div>
                  <div style="font-size:0.8rem;color:var(--text-muted);">Qty: <?= $item->quantity ?> × KES
                    <?= number_format($item->price, 2) ?>
                  </div>
                </div>
                <div style="font-weight:700;color:var(--hl-blue);">KES <?= number_format($item->total, 2) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Payment history -->
        <div class="hl-card mb-3">
          <div class="hl-card-header">
            <h4>Payment History</h4>
          </div>
          <div class="hl-card-body">
            <?php if (empty($order->payments)): ?>
              <p style="color:var(--text-muted);margin:0;">No payments recorded yet.</p>
            <?php else: ?>
              <?php foreach ($order->payments as $p): ?>
                <div class="d-flex justify-content-between align-items-center"
                  style="padding:0.4rem 0;border-bottom:1px solid var(--border);">
                  <div>
                    <span
                      class="badge-status <?= $p->isCompleted() ? 'badge-success' : 'badge-warning' ?>"><?= ucfirst($p->status) ?></span>
                    <span style="font-size:0.82rem;color:var(--text-muted);margin-left:0.5rem;"><?= $p->stageLabel() ?> ·
                      <?= $p->methodLabel() ?></span>
                    <?php if ($p->mpesa_receipt): ?>
                      <div style="font-size:0.75rem;color:var(--text-muted);">Receipt: <?= Html::encode($p->mpesa_receipt) ?>
                      </div><?php endif; ?>
                  </div>
                  <div style="font-weight:700;">KES <?= number_format($p->amount, 2) ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Delivery details -->
        <?php if ($order->delivery_address): ?>
          <div class="hl-card mb-3">
            <div class="hl-card-header">
              <h4><i class="bi bi-geo-alt" style="color:var(--hl-blue);"></i> Delivery Address</h4>
            </div>
            <div class="hl-card-body">
              <p style="margin:0;"><?= Html::encode($order->delivery_address) ?></p>
            </div>
          </div>
        <?php endif; ?>

        <!-- Review CTA -->
        <?php if ($order->canLeaveReview() && !$order->review): ?>
          <div class="hl-alert hl-alert-success">
            <i class="bi bi-star-fill"></i>
            <div>
              <strong>Order complete!</strong> Share your experience.
              <a href="<?= Url::to(['/review/create', 'order_id' => $order->id]) ?>"
                class="btn-hl-primary btn-sm ms-2">Leave a Review</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-md-5">
        <!-- Price summary -->
        <div class="hl-card mb-3">
          <div class="hl-card-header">
            <h4>Price Summary</h4>
          </div>
          <div class="hl-card-body">
            <div class="d-flex justify-content-between mb-2"><span>Total</span><strong>KES
                <?= number_format($order->total_amount, 2) ?></strong></div>
            <?php if ($order->payment_type === 'partial'): ?>
              <div class="d-flex justify-content-between mb-2" style="color:var(--text-muted);font-size:0.875rem;">
                <span>Deposit</span><span>KES <?= number_format($order->deposit_amount, 2) ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2" style="color:var(--text-muted);font-size:0.875rem;">
                <span>Balance</span><span>KES <?= number_format($order->balance_amount, 2) ?></span>
              </div>
            <?php endif; ?>
            <div class="hl-currency-display mt-2">
              <div class="amount-kes"><span class="label">KES</span><span
                  class="value"><?= number_format($order->total_amount, 2) ?></span></div>
              <div class="divider"></div>
              <div class="amount-usd"><span class="label">USD</span><span class="value usd"><?= $prices['USD'] ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="hl-card mb-3">
          <div class="hl-card-header">
            <h4>Actions</h4>
          </div>
          <div class="hl-card-body d-flex flex-column gap-2">
            <?php if (!$order->isPaid() && !$order->isCancelled()): ?>
              <!-- <h4><i class="bi bi-phone-fill btn-hl-green w-100" style="justify-content:center;padding:0.85rem;font-size:1rem;border:1px solid --hl-green;color:var(--hl-green);"></i> Complete Payment</h4> -->
              <a href="<?= Url::to(['/order/pay', 'id' => $order->id]) ?>" class="btn-hl-green btn-sm"
                style="justify-content:center;padding:0.85rem;font-size:1rem;border:1px solid #22c55e;;">
                <i class="bi bi-phone-fill"></i> Complete Payment
              </a>
            <?php endif; ?>
            <?php if ($order->canBeCancelled()): ?>
              <?= Html::a(
                '<i class="bi bi-x-circle"></i> Cancel Order',
                Url::to(['/order/cancel', 'id' => $order->id]),
                [
                  'class' => 'btn-hl-outline btn-sm text-center',
                  'style' => 'justify-content:center;border-color:var(--danger);color:var(--danger);',
                  'data-method' => 'post',
                  'data-confirm' => 'Cancel this order?'
                ]
              ) ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Provider -->
        <?php if ($provider): ?>
          <div class="hl-card mb-3">
            <div class="hl-card-header">
              <h4>Provider</h4>
            </div>
            <div class="hl-card-body text-center">
              <div class="hl-provider-avatar-placeholder" style="margin:0 auto 0.5rem;">
                <?= strtoupper(substr($provider->business_name, 0, 2)) ?>
              </div>
              <div style="font-weight:700;"><?= Html::encode($provider->business_name) ?></div>
              <div style="font-size:0.8rem;color:var(--text-muted);">📍
                <?= Html::encode($provider->city) ?>
                <?= $provider->address ? ' · ' . Html::encode($provider->address) : '' ?>
              </div>
            </div>
          </div>

          <?php if ($provider->lat !== null && $provider->lng !== null): ?>
            <div class="hl-card mb-3">
              <div class="hl-card-header">
                <h4>Business Location</h4>
              </div>
              <div class="hl-card-body">
                <div id="providerLocationMap"
                  style="height:260px;border:1px solid var(--border);border-radius:var(--r-md);overflow:hidden;"></div>
                <p style="margin:0.75rem 0 0;color:var(--text-muted);font-size:0.92rem;">Exact location for this provider.
                  <?= Html::encode($provider->address ?: $provider->city) ?>
                </p>
                <p style="margin:0.25rem 0 0;color:var(--text-muted);font-size:0.83rem;">Coordinates:
                  <?= number_format($provider->lat, 6) ?>, <?= number_format($provider->lng, 6) ?>
                </p>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php if ($provider && $provider->lat !== null && $provider->lng !== null): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof L === 'undefined') {
        console.error('Leaflet is not loaded.');
        return;
      }

      const lat = <?= json_encode($provider->lat) ?>;
      const lng = <?= json_encode($provider->lng) ?>;
      const map = L.map('providerLocationMap').setView([lat, lng], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
      }).addTo(map);
      const marker = L.marker([lat, lng]).addTo(map);
      marker.bindPopup(<?= json_encode($provider->business_name) ?>).openPopup();
    });
  </script>
<?php endif; ?>