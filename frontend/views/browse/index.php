<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\Category[] $categories */
/** @var string|null $currentType */
/** @var string|null $currentQ */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">

  <!-- Filter Bar -->
  <div style="background:var(--surface);border-bottom:1px solid var(--border);padding:1rem 0;position:sticky;top:var(--navbar-h);z-index:100;box-shadow:var(--shadow-xs);">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">
      <form method="get" action="<?= Url::to(['/browse']) ?>" class="d-flex align-items-center gap-3 flex-wrap">
        <!-- Search -->
        <div class="hl-input-icon" style="flex:1;min-width:240px;">
          <i class="bi bi-search"></i>
          <input type="text" name="q" class="hl-input" value="<?= Html::encode($currentQ) ?>" placeholder="Search services or products…" style="padding-left:2.5rem;">
        </div>
        <!-- Type -->
        <select name="type" class="hl-select" style="width:140px;" onchange="this.form.submit()">
          <option value="">All Types</option>
          <option value="service" <?= $currentType === 'service' ? 'selected' : '' ?>>Services</option>
          <option value="product" <?= $currentType === 'product' ? 'selected' : '' ?>>Products</option>
        </select>
        <!-- Sort -->
        <select name="sort" class="hl-select" style="width:160px;" onchange="this.form.submit()">
          <option value="">Sort: Featured</option>
          <option value="newest">Newest First</option>
          <option value="price_asc">Price: Low→High</option>
          <option value="price_desc">Price: High→Low</option>
        </select>
        <!-- <button type="submit" class="btn-hl-ghost"><i class="bi bi-search"></i> Search</button> -->
        <?php if ($currentQ || $currentType): ?>
          <a href="<?= Url::to(['/browse']) ?>" class="btn-hl-ghost" style="color:gold" ><i class="bi bi-x"></i></a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div style="max-width:1280px;margin:0 auto;padding:2rem 1.5rem;">
    <div class="row g-4">
      <!-- Sidebar categories -->
      <div class="col-lg-2 d-none d-lg-block">
        <div class="hl-filter-bar" style="position:sticky;top:140px;">
          <h5>Categories</h5>
          <div class="d-flex flex-column gap-1">
            <a href="<?= Url::to(['/browse']) ?>" class="hl-nav-links" style="padding:0.5rem 0.75rem;border-radius:var(--radius);font-size:0.85rem;display:block;<?= !Yii::$app->request->get('category') ? 'background:var(--hl-blue-light);color:var(--hl-blue);font-weight:600;' : '' ?>">All Categories</a>
            <?php foreach ($categories as $cat): ?>
              <a href="<?= Url::to(['/browse/' . $cat->slug]) ?>" style="padding:0.5rem 0.75rem;border-radius:var(--radius);font-size:0.85rem;display:flex;align-items:center;gap:0.5rem;color:var(--text-secondary);transition:all var(--transition);"
                 onmouseover="this.style.background='var(--hl-blue-light)';this.style.color='var(--hl-blue)'"
                 onmouseout="this.style.background='';this.style.color='var(--text-secondary)'">
                <i class="<?= Html::encode($cat->icon ?: 'bi-grid') ?>" style="font-size:0.9rem;"></i>
                <?= Html::encode($cat->name) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Main grid -->
      <div class="col-lg-10">
        <?php
        $total = $dataProvider->getTotalCount();
        $models = $dataProvider->getModels();
        ?>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h1 style="font-size:clamp(1.6rem, 3vw, 2.2rem);margin:0 0 0.35rem;">Marketplace</h1>
            <p style="color:var(--text-muted);font-size:0.875rem;margin:0;">
            <?= number_format($total) ?> result<?= $total !== 1 ? 's' : '' ?> found
            <?= $currentQ ? ' for "<strong>' . Html::encode($currentQ) . '</strong>"' : '' ?>
            </p>
          </div>
        </div>

        <?php if (empty($models)): ?>
          <div style="text-align:center;padding:5rem 2rem;background:var(--surface);border-radius:var(--radius-lg);border:1px solid var(--border);">
            <i class="bi bi-search" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:1rem;"></i>
            <h4>No listings found</h4>
            <p style="color:var(--text-muted);">Try adjusting your search or <a href="<?= Url::to(['/browse']) ?>">browse all listings</a>.</p>
          </div>
        <?php else: ?>
          <div class="hl-listings-grid">
            <?php foreach ($models as $listing): ?>
              <?php $prices = Yii::$app->currency->both($listing->price); ?>
              <a href="<?= Url::to(['/listing/' . $listing->id . '/' . $listing->slug]) ?>" class="hl-listing-card" style="text-decoration:none;color:inherit;">
                <div class="hl-listing-img">
                  <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" alt="<?= Html::encode($listing->name) ?>" loading="lazy">
                  <span class="hl-listing-badge badge-<?= $listing->type ?>"><?= $listing->isService() ? 'Service' : 'Product' ?></span>
                  <?php if ($listing->is_featured): ?>
                    <span class="hl-listing-badge badge-featured" style="left:auto;right:10px;">⭐</span>
                  <?php endif; ?>
                </div>
                <div class="hl-listing-body">
                  <div class="hl-listing-cat"><?= Html::encode($listing->category->name ?? '') ?></div>
                  <div class="hl-listing-name"><?= Html::encode($listing->name) ?></div>
                  <div class="hl-listing-provider">
                    <i class="bi bi-shop"></i> <?= Html::encode($listing->provider->business_name ?? '') ?>
                    <?php if ($listing->provider->is_verified): ?>
                      <i class="bi bi-patch-check-fill" style="color:var(--hl-blue);font-size:0.8rem;"></i>
                    <?php endif; ?>
                  </div>
                  <div class="hl-listing-footer">
                    <div>
                      <div class="hl-listing-price"><?= Yii::$app->currency->format($listing->price) ?></div>
                    </div>
                    <?php if ($listing->provider->rating > 0): ?>
                      <div class="hl-listing-rating"><span class="stars">★</span> <?= number_format($listing->provider->rating, 1) ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>

          <!-- Pagination -->
          <div class="mt-4 d-flex justify-content-center">
            <?= LinkPager::widget([
              'pagination'          => $dataProvider->pagination,
              'options'             => ['class' => 'hl-pagination'],
              'linkOptions'         => ['class' => 'hl-pag-btn'],
              'disabledPageCssClass'=> 'hl-pag-btn',
              'activePageCssClass'  => 'hl-pag-btn active',
              'prevPageLabel'       => '‹',
              'nextPageLabel'       => '›',
            ]) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
