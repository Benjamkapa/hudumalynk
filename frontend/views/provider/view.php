<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var common\models\Listing[] $listings */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
  <div class="hl-section" style="max-width:1200px;">

    <!-- Hero -->
    <div class="hl-card mb-4">
      <div class="hl-card-body" style="text-align:center;padding:2.5rem;">
        <?php if ($provider->logo): ?>
          <img src="<?= Html::encode('/uploads/' . $provider->logo) ?>" class="hl-provider-avatar" style="width:80px;height:80px;margin-bottom:1rem;" alt="<?= Html::encode($provider->business_name) ?>">
        <?php else: ?>
          <div class="hl-provider-avatar-placeholder" style="width:80px;height:80px;margin:0 auto 1rem;"><?= strtoupper(substr($provider->business_name, 0, 2)) ?></div>
        <?php endif; ?>
        <h1 style="font-size:1.6rem;margin-bottom:0.25rem;"><?= Html::encode($provider->business_name) ?></h1>
        <?php if ($provider->is_verified): ?>
          <div class="hl-verified-badge" style="font-size:0.8rem;display:inline-flex;margin-bottom:0.5rem;"><i class="bi bi-patch-check-fill"></i> Verified Provider</div>
        <?php endif; ?>
        <?php if ($provider->total_reviews > 0): ?>
          <div class="d-flex justify-content-center align-items-center gap-1 mb-2">
            <span class="hl-stars"><?= str_repeat('★', round($provider->rating)) ?></span>
            <span style="font-weight:700;font-size:1.1rem;"><?= number_format($provider->rating, 1) ?></span>
            <span style="color:var(--text-muted);">(<?= $provider->total_reviews ?> reviews)</span>
          </div>
        <?php endif; ?>
        <div style="font-size:0.875rem;color:var(--text-secondary);">
          <i class="bi bi-geo-alt"></i> <?= Html::encode($provider->city ?? $provider->address) ?>
          <?php if ($provider->phone): ?><i class="bi bi-telephone ms-3"></i> <?= Html::encode($provider->phone) ?><?php endif; ?>
        </div>
        <?php if ($provider->description): ?>
          <p style="max-width:600px;margin:1.25rem auto 0;font-size:0.9rem;color:var(--text-secondary);line-height:1.7;"><?= nl2br(Html::encode($provider->description)) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Stats row -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-md-4">
        <div class="hl-stat-card">
          <div class="hl-stat-icon blue"><i class="bi bi-shop"></i></div>
          <div>
            <div class="hl-stat-value"><?= count($listings) ?></div>
            <div class="hl-stat-label">Listings</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="hl-stat-card">
          <div class="hl-stat-icon green"><i class="bi bi-bag-check"></i></div>
          <div>
            <div class="hl-stat-value"><?= $provider->total_orders ?></div>
            <div class="hl-stat-label">Orders</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="hl-stat-card">
          <div class="hl-stat-icon warn"><i class="bi bi-star"></i></div>
          <div>
            <div class="hl-stat-value"><?= number_format($provider->rating, 1) ?></div>
            <div class="hl-stat-label">Rating</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Listings grid -->
    <div class="hl-card">
      <div class="hl-card-header">
        <h4><?= Html::encode($provider->business_name) ?>'s Listings</h4>
      </div>
      <div class="hl-card-body p-0">
        <?php if (empty($listings)): ?>
          <div class="text-center py-5">
            <i class="bi bi-shop-window" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:1rem;"></i>
            <h5>No listings yet</h5>
            <p style="color:var(--text-muted);">This provider hasn't added any services or products.</p>
          </div>
        <?php else: ?>
          <div class="hl-listings-grid">
            <?php foreach ($listings as $listing): ?>
              <a href="<?= Url::to(['/listing/' . $listing->id . '/' . $listing->slug]) ?>" class="hl-listing-card">
                <div class="hl-listing-img">
                  <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" alt="<?= Html::encode($listing->name) ?>">
                  <span class="hl-listing-badge badge-<?= $listing->type ?>"><?= ucfirst($listing->type) ?></span>
                  <?php if ($listing->is_featured): ?><span class="hl-listing-badge badge-featured" style="right:10px;">⭐</span><?php endif; ?>
                </div>
                <div class="hl-listing-body">
                  <div class="hl-listing-cat"><?= Html::encode($listing->category->name ?? '') ?></div>
                  <div class="hl-listing-name"><?= Html::encode($listing->name) ?></div>
                  <div class="hl-listing-footer">
                    <div class="hl-listing-price"><?= Yii::$app::currency->format($listing->price) ?></div>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
