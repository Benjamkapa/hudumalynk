<?php

/** @var yii\web\View $this */
/** @var common\models\Category[] $categories */
/** @var common\models\Listing[] $featuredListings */
/** @var common\models\Listing[] $latestListings */
/** @var array $stats */

use yii\helpers\Html;
use yii\helpers\Url;

$this->params['metaDescription'] = 'HudumaLynk — Nairobi\'s premier marketplace. Connect with verified service providers and local vendors effortlessly.';
?>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hl-hero">
  <div class="hl-hero-glow hl-hero-glow-left"></div>
  <div class="hl-hero-glow hl-hero-glow-right"></div>

  <div class="hl-hero-inner reveal">
    <div class="hl-hero-copy">
      <div class="hl-hero-tag" data-delay="0.1">
        <i class="bi bi-patch-check-fill"></i> Nairobi's #1 Verified Marketplace
      </div>

      <h1 class="reveal" data-delay="0.2">
        Everything you need,<br>
        <span>Delivered with Trust.</span>
      </h1>

      <p class="reveal" data-delay="0.3">
        Shop from local vendors or book trusted services in minutes. HudumaLynk connects you with the best of Nairobi, verified for your peace of mind.
      </p>

      <form id="hero-search-form" class="hl-hero-search reveal" data-delay="0.4" action="<?= Url::to(['/browse']) ?>" method="get">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="What are you looking for today?" autocomplete="off">
        <button type="submit">Search</button>
      </form>

      <div class="hl-hero-cta reveal" data-delay="0.5">
        <a href="<?= Url::to(['/browse']) ?>" class="btn-hl-primary btn-lg">
          <i class="bi bi-compass"></i> Explore Marketplace
        </a>
        <a href="<?= Url::to(['/join']) ?>" class="btn-white">
          <i class="bi bi-shop"></i> Join as a Vendor
        </a>
      </div>

      <div class="hl-hero-metrics reveal" data-delay="0.6">
        <div class="hl-stat-item">
          <div class="hl-stat-num"><?= number_format($stats['providers']) ?><span>+</span></div>
          <div class="hl-stat-label">Local Vendors</div>
        </div>
        <div class="hl-stat-item">
          <div class="hl-stat-num"><?= number_format($stats['listings']) ?><span>+</span></div>
          <div class="hl-stat-label">Active Listings</div>
        </div>
        <div class="hl-stat-item">
          <div class="hl-stat-num">4.8<span>★</span></div>
          <div class="hl-stat-label">Customer Rating</div>
        </div>
      </div>
    </div>

    <aside class="hl-hero-aside">
      <div class="hl-hero-panel reveal" data-delay="0.4">
        <div class="hl-panel-badge">Trusted by Nairobi</div>
        <h3>Discover trusted services faster</h3>
        <p>Browse hand-picked listings, compare top providers, and book with confidence—all in one beautiful marketplace.</p>
        <div class="hl-panel-list">
          <span><i class="bi bi-check-circle"></i> Verified providers</span>
          <span><i class="bi bi-check-circle"></i> Secure checkout</span>
          <span><i class="bi bi-check-circle"></i> Fast local delivery</span>
        </div>
      </div>

      <div class="hl-hero-card-grid reveal" data-delay="0.5">
        <div class="hl-hero-card">
          <div class="hl-card-icon"><i class="bi bi-star-fill"></i></div>
          <div>
            <div class="hl-card-title">Top Rated Services</div>
            <div class="hl-card-desc">Hand-picked providers with the highest customer satisfaction.</div>
          </div>
        </div>
        <div class="hl-hero-card">
          <div class="hl-card-icon"><i class="bi bi-clock-history"></i></div>
          <div>
            <div class="hl-card-title">Fast Booking</div>
            <div class="hl-card-desc">Search and schedule the service you need in seconds.</div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</section>

<!-- ── FEATURED LISTINGS ──────────────────────────────────────── -->
<?php if (!empty($featuredListings)): ?>
<section class="hl-section">
  <div class="hl-section-header d-flex align-items-end justify-content-between reveal">
    <div>
      <div class="text-uppercase font-800 text-xs mb-2" style="color:var(--hl-blue); letter-spacing:0.2em;">Premium Picks</div>
      <h2 style="margin:0;">⭐ Top-Rated Providers</h2>
      <p class="mt-2">The most trusted services and products, hand-picked for quality.</p>
    </div>
    <a href="<?= Url::to(['/browse', 'sort' => 'featured']) ?>" class="btn-hl-ghost font-700">
      View All Featured <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>

  <div class="hl-listings-grid mt-4">
    <?php foreach ($featuredListings as $i => $listing): ?>
      <?php $prices = Yii::$app->currency->both($listing->price); ?>
      <div class="reveal" data-delay="<?= 0.1 * ($i % 4) ?>">
        <a href="<?= Url::to(['/listing/' . $listing->id . '/' . $listing->slug]) ?>" class="hl-listing-card">
            <div class="hl-listing-img">
            <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" alt="<?= Html::encode($listing->name) ?>" loading="lazy">
            <span class="hl-listing-badge badge-<?= $listing->type ?>">
                <?= $listing->isService() ? 'Service' : 'Product' ?>
            </span>
            <span class="hl-listing-badge badge-featured" style="left:auto;right:10px;">⭐ Featured</span>
            </div>
            <div class="hl-listing-body">
            <div class="hl-listing-cat"><?= Html::encode($listing->category->name ?? '') ?></div>
            <div class="hl-listing-name"><?= Html::encode($listing->name) ?></div>
            <div class="hl-listing-provider">
                <i class="bi bi-shop"></i>
                <?= Html::encode($listing->provider->business_name ?? '') ?>
                <?php if ($listing->provider->is_verified): ?>
                <i class="bi bi-patch-check-fill" style="color:var(--hl-blue); font-size:0.8rem;"></i>
                <?php endif; ?>
            </div>
            <div class="hl-listing-footer">
                <div class="hl-listing-price">
                <div class="hl-price-kes"><?= $prices['KES'] ?></div>
                <div class="hl-price-usd"><?= $prices['USD'] ?></div>
                </div>
                <?php if ($listing->provider->rating > 0): ?>
                <div class="hl-listing-rating">
                <span class="stars">★</span> <?= number_format($listing->provider->rating, 1) ?>
                </div>
                <?php endif; ?>
            </div>
            </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ── LATEST LISTINGS ────────────────────────────────────────── -->
<?php if (!empty($latestListings)): ?>
<section class="hl-section pt-0">
  <div class="hl-section-header d-flex align-items-end justify-content-between reveal">
    <div>
      <div class="text-uppercase font-800 text-xs mb-2" style="color:var(--hl-blue); letter-spacing:0.2em;">New Arrivals</div>
      <h2 style="margin:0;">Fresh on HudumaLynk</h2>
    </div>
    <a href="<?= Url::to(['/browse', 'sort' => 'newest']) ?>" class="btn-hl-ghost font-700">
      See What's New <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>

  <div class="hl-listings-grid mt-4">
    <?php foreach ($latestListings as $i => $listing): ?>
      <?php $prices = Yii::$app->currency->both($listing->price); ?>
      <div class="reveal" data-delay="<?= 0.1 * ($i % 4) ?>">
        <a href="<?= Url::to(['/listing/' . $listing->id]) ?>" class="hl-listing-card">
            <div class="hl-listing-img">
            <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" alt="<?= Html::encode($listing->name) ?>" loading="lazy">
            <span class="hl-listing-badge badge-<?= $listing->type ?>">
                <?= $listing->isService() ? 'Service' : 'Product' ?>
            </span>
            </div>
            <div class="hl-listing-body">
            <div class="hl-listing-cat"><?= Html::encode($listing->category->name ?? '') ?></div>
            <div class="hl-listing-name"><?= Html::encode($listing->name) ?></div>
            <div class="hl-listing-provider">
                <i class="bi bi-shop"></i>
                <?= Html::encode($listing->provider->business_name ?? '') ?>
            </div>
            <div class="hl-listing-footer">
                <div class="hl-listing-price">
                <div class="hl-price-kes"><?= $prices['KES'] ?></div>
                <div class="hl-price-usd"><?= $prices['USD'] ?></div>
                </div>
                <div class="btn-hl-primary btn-sm px-3" style="box-shadow:none;">
                  <?= $listing->isService() ? 'Book' : 'Buy' ?>
                </div>
            </div>
            </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ── CATEGORIES ─────────────────────────────────────────────── -->
<?php if (!empty($categories)): ?>
<section class="hl-section-sep" style="background:var(--surface-3); border-top:1px solid var(--border);">
  <div class="hl-section">
    <div class="hl-section-header centered reveal">
      <div class="text-uppercase font-800 text-xs mb-2" style="color:var(--hl-blue); letter-spacing:0.2em;">Explore More</div>
      <h2>Browse by Category</h2>
      <p>Find exactly what you need across <?= count($categories) ?> hand-curated niches.</p>
    </div>
    
    <div class="hl-categories-grid reveal" data-delay="0.2">
      <?php foreach ($categories as $cat): ?>
        <a href="<?= Url::to(['/browse/' . $cat->slug]) ?>" class="hl-cat-card">
          <div class="hl-cat-icon" style="background:var(--surface-2); border:1px solid var(--border); color:var(--hl-blue);">
            <i class="<?= Html::encode($cat->icon ?: 'bi-grid') ?>"></i>
          </div>
          <span><?= Html::encode($cat->name) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── HOW IT WORKS ───────────────────────────────────────────── -->
<section class="hl-section">
  <div class="hl-section-header centered reveal">
    <div class="text-uppercase font-800 text-xs mb-2" style="color:var(--hl-blue); letter-spacing:0.2em;">Our Process</div>
    <h2>How HudumaLynk Works</h2>
    <p>Get what you need in three simple, secure steps.</p>
  </div>

  <div class="hl-how-grid mt-5">
    <?php 
    $steps = [
        ['icon' => 'bi-search', 'title' => 'Find', 'desc' => 'Search or browse verified services and products across Nairobi.'],
        ['icon' => 'bi-cart-check', 'title' => 'Order', 'desc' => 'Place your order and pick your preferred payment method.'],
        ['icon' => 'bi-shield-lock', 'title' => 'Secure Pay', 'desc' => 'Pay via M-Pesa or card. Your funds are protected until delivery.'],
        ['icon' => 'bi-star', 'title' => 'Review', 'desc' => 'Share your experience to help our community grow stronger.']
    ];
    foreach ($steps as $i => $step): ?>
      <div class="hl-how-card reveal" data-delay="<?= $i * 0.1 ?>" style="border:none; background:transparent; padding:0;">
        <div class="hl-how-num" style="background:var(--hl-blue-light); border-color:var(--hl-blue); color:var(--hl-blue);"><?= $i + 1 ?></div>
        <div class="mt-4 mb-2"><i class="<?= $step['icon'] ?>" style="font-size:2rem; color:var(--hl-blue);"></i></div>
        <h4 class="display-font"><?= $step['title'] ?></h4>
        <p class="text-muted text-sm"><?= $step['desc'] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ── CTA BANNER ─────────────────────────────────────────────── -->
<section class="reveal" style="margin:2rem 1.5rem 5rem; border-radius:var(--radius-xl); background:var(--hl-gradient-hero); overflow:hidden; position:relative;">
  <div style="position:absolute; inset:0; background:radial-gradient(circle at 70% 30%, var(--hl-blue) 0%, transparent 60%); opacity:0.3; filter:blur(40px);"></div>
  <div class="hl-section" style="padding:4.5rem 2rem; text-align:center; position:relative; z-index:1;">
    <h2 style="color:#fff; font-size:2.4rem; letter-spacing:-0.03em;">Become a Verified Provider</h2>
    <p style="color:rgba(255,255,255,0.8); max-width:540px; margin:0 auto 2.5rem; font-size:1.1rem;">
      Own a shop or offer a service? Join Nairobi's fastest-growing marketplace. Reach thousands of local customers and grow your brand with HudumaLynk.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?= Url::to(['/join']) ?>" class="btn-hl-primary btn-lg px-5">
        Join the Network
      </a>
      <a href="<?= Url::to(['/join', '#' => 'plans']) ?>" class="btn-white btn-lg">
        View Pricing
      </a>
    </div>
  </div>
</section>
