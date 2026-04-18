<?php
/** @var yii\web\View $this */
/** @var common\models\Listing $listing */
/** @var common\models\Review[] $reviews */
/** @var array $prices */
use yii\helpers\Html;
use yii\helpers\Url;
$provider = $listing->provider;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);">
<div class="hl-section" style="padding-top:2rem;padding-bottom:2rem;">

  <!-- Breadcrumb -->
  <nav style="font-size:0.82rem;color:var(--text-muted);margin-bottom:1.5rem;">
    <a href="<?= Url::to(['/']) ?>">Home</a> /
    <a href="<?= Url::to(['/browse']) ?>">Browse</a> /
    <a href="<?= Url::to(['/browse', 'category' => $listing->category->slug ?? '']) ?>"><?= Html::encode($listing->category->name ?? '') ?></a> /
    <span><?= Html::encode($listing->name) ?></span>
  </nav>

  <div class="row g-4">
    <!-- Left: Images + Details -->
    <div class="col-lg-8">
      <!-- Gallery -->
      <div class="hl-card mb-4" style="overflow:hidden;">
        <?php $images = $listing->images; ?>
        <div class="hl-gallery-main" style="aspect-ratio:16/9;overflow:hidden;background:var(--surface-3);">
          <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" alt="<?= Html::encode($listing->name) ?>"
               style="width:100%;height:100%;object-fit:cover;transition:opacity 0.3s;" id="main-gallery-img">
        </div>
        <?php if (count($images) > 1): ?>
        <div class="d-flex gap-2 p-3 overflow-auto">
          <?php foreach ($images as $i => $img): ?>
            <div class="hl-gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                 data-src="<?= Html::encode('/uploads/' . $img->image_path) ?>"
                 style="width:80px;height:60px;border-radius:var(--radius-sm);overflow:hidden;cursor:pointer;border:2px solid <?= $i === 0 ? 'var(--hl-blue)' : 'transparent' ?>;flex-shrink:0;transition:border-color var(--transition);">
              <img src="<?= Html::encode('/uploads/' . $img->image_path) ?>"
                   style="width:100%;height:100%;object-fit:cover;" alt="Image <?= $i+1 ?>">
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Description -->
      <div class="hl-card mb-4">
        <div class="hl-card-header"><h4>About This <?= $listing->isService() ? 'Service' : 'Product' ?></h4></div>
        <div class="hl-card-body">
          <?php if ($listing->description): ?>
            <div style="line-height:1.8;color:var(--text-secondary);">
              <?= nl2br(Html::encode($listing->description)) ?>
            </div>
          <?php else: ?>
            <p style="color:var(--text-muted);">No additional details provided.</p>
          <?php endif; ?>

          <?php if ($listing->isService() && $listing->availability): ?>
          <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
            <strong><i class="bi bi-clock" style="color:var(--hl-blue);"></i> Availability:</strong>
            <span style="color:var(--text-secondary);"> <?= Html::encode($listing->availability) ?></span>
          </div>
          <?php endif; ?>

          <?php if ($listing->isProduct() && $listing->stock_quantity !== null): ?>
          <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
            <strong><i class="bi bi-box" style="color:var(--hl-blue);"></i> Stock:</strong>
            <span class="badge-status <?= $listing->stock_quantity > 0 ? 'badge-success' : 'badge-danger' ?>">
              <?= $listing->stock_quantity > 0 ? $listing->stock_quantity . ' available' : 'Out of stock' ?>
            </span>
          </div>
          <?php endif; ?>

          <?php if ($listing->location): ?>
          <div style="margin-top:1rem;">
            <strong><i class="bi bi-geo-alt" style="color:var(--hl-blue);"></i> Location:</strong>
            <span style="color:var(--text-secondary);"> <?= Html::encode($listing->location) ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Reviews -->
      <div class="hl-card" id="reviews">
        <div class="hl-card-header">
          <h4>Customer Reviews</h4>
          <?php if ($provider->total_reviews > 0): ?>
            <div class="d-flex align-items-center gap-2">
              <span class="hl-stars"><?= str_repeat('★', round($provider->rating)) ?><span class="hl-stars-empty"><?= str_repeat('★', 5 - round($provider->rating)) ?></span></span>
              <strong><?= number_format($provider->rating, 1) ?></strong>
              <span style="color:var(--text-muted);font-size:0.85rem;">(<?= $provider->total_reviews ?> reviews)</span>
            </div>
          <?php endif; ?>
        </div>
        <div class="hl-card-body">
          <?php if (empty($reviews)): ?>
            <p style="color:var(--text-muted);text-align:center;padding:1rem 0;">No reviews yet. Be the first to review!</p>
          <?php else: ?>
            <div class="d-flex flex-column gap-3">
              <?php foreach ($reviews as $review): ?>
              <div style="padding-bottom:1rem;border-bottom:1px solid var(--border);">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <div style="width:36px;height:36px;border-radius:50%;background:var(--hl-gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;flex-shrink:0;">
                    <?= Html::encode($review->user->getInitials()) ?>
                  </div>
                  <div>
                    <div style="font-weight:600;font-size:0.875rem;"><?= Html::encode($review->user->getFullName()) ?></div>
                    <div class="d-flex align-items-center gap-1">
                      <span style="color:#F59E0B;font-size:0.8rem;"><?= str_repeat('★', $review->rating) ?><span style="color:#E2E8F0;"><?= str_repeat('★', 5 - $review->rating) ?></span></span>
                      <span style="font-size:0.75rem;color:var(--text-muted);">· <?= Yii::$app->formatter->asRelativeTime($review->created_at) ?></span>
                      <?php if ($review->is_verified): ?>
                        <span class="hl-verified-badge" style="font-size:0.65rem;"><i class="bi bi-patch-check-fill"></i> Verified</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <?php if ($review->title): ?><div style="font-weight:600;font-size:0.875rem;margin-bottom:0.25rem;"><?= Html::encode($review->title) ?></div><?php endif; ?>
                <?php if ($review->comment): ?><p style="font-size:0.875rem;color:var(--text-secondary);margin:0;"><?= Html::encode($review->comment) ?></p><?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
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
            <div class="spinner-border text-primary" role="loading"></div>
            <p class="mt-2 text-muted">Loading provider details...</p>
          </div>
        </div>

<script>
function loadProviderModal(id) {
  const modalTitle = document.getElementById('modalTitle');
  const modalBody = document.getElementById('modalBody');
  const loading = document.getElementById('modalLoading');
  
  modalTitle.textContent = 'Loading...';
  loading.style.display = 'block';
  
  fetch('/api/provider/' + id)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        modalTitle.textContent = data.provider.business_name;
        loading.style.display = 'none';
        modalBody.innerHTML = `
          <div style="text-align:center;">
            ${data.provider.logo ? 
              `<img src="/uploads/${data.provider.logo}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 1rem;display:block;border:3px solid var(--hl-blue-light);">` : 
              `<div style="width:80px;height:80px;margin:0 auto 1rem;background:var(--hl-gradient);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">${data.provider.business_name.substring(0,2).toUpperCase()}</div>`
            }
            <h5 style="margin:0 0 0.5rem;">${data.provider.business_name}</h5>
            ${data.provider.is_verified ? '<span class="hl-verified-badge mb-2"><i class="bi bi-patch-check-fill"></i> Verified</span>' : ''}
            ${data.provider.rating > 0 ? `<div style="margin:1rem 0;"><span class="hl-stars">${'★'.repeat(Math.round(data.provider.rating))}</span> <strong>${data.provider.rating.toFixed(1)}</strong> (${data.provider.total_reviews} reviews)</div>` : ''}
            <div style="font-size:0.875rem;color:var(--text-secondary);line-height:1.6;">
              <div><i class="bi bi-geo-alt" style="color:var(--hl-blue);"></i> ${data.provider.city || data.provider.address}</div>
              ${data.provider.phone ? `<div style="margin-top:0.5rem;"><i class="bi bi-telephone" style="color:var(--hl-green);"></i> ${data.provider.phone}</div>` : ''}
            </div>
            ${data.provider.description ? `<p style="margin:1.25rem 0 1rem;color:var(--text-secondary);line-height:1.7;">${data.provider.description.replace(/\\n/g, '<br>')}</p>` : ''}
            <div class="d-flex gap-2">
              <a href="/provider/${data.provider.id}/${data.provider.slug}" class="btn-hl-primary btn-sm flex-fill">Full Profile</a>
              <button class="btn-hl-ghost btn-sm flex-fill" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        `;
      } else {
        modalTitle.textContent = 'Error';
        loading.style.display = 'none';
        modalBody.innerHTML = '<p class="text-danger text-center">Could not load provider details. <button class="btn btn-link p-0" onclick="loadProviderModal(' + id + ')">Retry</button></p>';
      }
    })
    .catch(() => {
      modalTitle.textContent = 'Error';
      loading.style.display = 'none';
      modalBody.innerHTML = '<p class="text-danger text-center">Network error. Please check your connection.</p>';
    });
}
</script>
          </div>
        </div>
      </div>

    </div>

    <!-- Right: Booking / Order Sidebar -->
    <div class="col-lg-4">
      <div style="position:sticky;top:calc(var(--navbar-h) + 1rem);">

        <!-- Price card -->
        <div class="hl-card mb-3">
          <div class="hl-card-body">
            <span class="hl-listing-badge badge-<?= $listing->type ?>" style="position:relative;top:0;left:0;margin-bottom:0.75rem;display:inline-flex;"><?= $listing->isService() ? 'Service' : 'Product' ?></span>
            <h3 style="margin-bottom:0.25rem;"><?= Html::encode($listing->name) ?></h3>

            <!-- Dual-currency price display with toggle -->
            <div class="hl-currency-display mt-3">
              <div class="amount-kes">
                <span class="label">KES</span>
                <span class="value"><?= number_format($listing->price, 2) ?></span>
              </div>
              <div class="divider"></div>
              <div class="amount-usd">
                <span class="label">USD</span>
                <span class="value usd"><?= $prices['USD'] ?></span>
              </div>
            </div>
            <p style="font-size:0.72rem;color:var(--text-muted);margin-top:0.25rem;">Rate: 1 USD ≈ <?= number_format(1 / $prices['rate'], 0) ?> KES</p>

            <?php if (!Yii::$app->user->isGuest): ?>
              <a href="<?= Url::to(['/order/create', 'listing_id' => $listing->id]) ?>"
                 class="btn-hl-primary w-100 mt-3" style="justify-content:center;padding:0.85rem;font-size:1rem;">
                <i class="bi bi-<?= $listing->isService() ? 'calendar-check' : 'cart-plus' ?>"></i>
                <?= $listing->isService() ? 'Book This Service' : 'Order Now' ?>
              </a>
            <?php else: ?>
              <a href="<?= Url::to(['/auth/login', 'returnUrl' => Yii::$app->request->url]) ?>"
                 class="btn-hl-primary w-100 mt-3" style="justify-content:center;padding:0.85rem;font-size:1rem;">
                <i class="bi bi-box-arrow-in-right"></i> Sign In to Order
              </a>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-3 justify-content-center" style="font-size:0.78rem;color:var(--text-muted);">
              <span><i class="bi bi-shield-check" style="color:var(--hl-green);"></i> Secure payment</span>
              <span><i class="bi bi-arrow-counterclockwise" style="color:var(--hl-blue);"></i> Review after delivery</span>
            </div>
          </div>
        </div>

        <!-- Provider card -->
        <div class="hl-card">
          <div class="hl-card-header"><h4>About the Provider</h4></div>
          <div class="hl-card-body" style="text-align:center;">
            <?php if ($provider->logo): ?>
              <img src="<?= Html::encode('/uploads/' . $provider->logo) ?>"
                   class="hl-provider-avatar" alt="<?= Html::encode($provider->business_name) ?>">
            <?php else: ?>
              <div class="hl-provider-avatar-placeholder"><?= strtoupper(substr($provider->business_name, 0, 2)) ?></div>
            <?php endif; ?>

            <h5 style="margin:0.5rem 0 0.25rem;"><?= Html::encode($provider->business_name) ?></h5>
            <?php if ($provider->is_verified): ?>
              <span class="hl-verified-badge"><i class="bi bi-patch-check-fill"></i> Verified Provider</span>
            <?php endif; ?>

            <?php if ($provider->total_reviews > 0): ?>
            <div class="d-flex justify-content-center align-items-center gap-1 mt-2" style="font-size:0.85rem;">
              <span style="color:#F59E0B;">★</span>
              <strong><?= number_format($provider->rating, 1) ?></strong>
              <span style="color:var(--text-muted);">(<?= $provider->total_reviews ?> reviews)</span>
            </div>
            <?php endif; ?>

            <p style="font-size:0.82rem;color:var(--text-secondary);margin:0.75rem 0;">
              <i class="bi bi-geo-alt" style="color:var(--hl-blue);"></i> <?= Html::encode($provider->city) ?>
            </p>

            <!-- <button class="btn-hl-outline w-100 btn-sm offcanvas-link" onclick="openOc('/provider/view/<?= $provider->id ?>', '<?= Html::encode($provider->business_name) ?>')" type="button">
              <i class="bi bi-eye"></i> View Full Profile
            </button> -->
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</div>
