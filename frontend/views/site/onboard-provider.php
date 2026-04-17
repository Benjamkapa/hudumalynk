<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Start Selling on Nairobi\'s Top Marketplace';
$this->params['metaDescription'] = 'List your products and services, reach thousands of customers, and grow your business with HudumaLynk today.';
?>

<style>
  /* Local Marketing Page Styles */
  .hl-marketing-hero {
    position: relative;
    padding: 7rem 1.5rem;
    overflow: hidden;
    text-align: center;
    background: var(--hl-gradient-hero);
  }
  .hl-marketing-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 60%; height: 150%;
    background: radial-gradient(ellipse at center, rgba(37,99,235,0.15) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
  }
  .hl-marketing-hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
  }
  .hl-feature-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 2.5rem 2rem;
    height: 100%;
    transition: all var(--transition);
  }
  .hl-feature-box:hover {
    border-color: var(--hl-blue);
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
  }
  .hl-feature-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: var(--hl-blue-light);
    color: var(--hl-blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
  }
  
  /* Pricing Cards */
  .hl-pricing-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    transition: all var(--transition);
  }
  .hl-pricing-card.popular {
    border: 2px solid var(--hl-blue);
    box-shadow: var(--shadow-blue);
    transform: scale(1.03);
  }
  .hl-pricing-card.popular::before {
    content: 'Most Popular';
    position: absolute;
    top: 16px; right: -30px;
    background: var(--hl-blue);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 2.5rem;
    transform: rotate(45deg);
  }
  .hl-pricing-price {
    font-family: 'Play', sans-serif;
    font-size: 3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 1.5rem 0 0.5rem;
    line-height: 1;
  }
  .hl-pricing-features {
    list-style: none;
    padding: 0; margin: 2rem 0;
  }
  .hl-pricing-features li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.8rem;
    color: var(--text-secondary);
  }
  .hl-pricing-features li i {
    color: var(--hl-green);
    font-size: 1.2rem;
  }
</style>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hl-marketing-hero">
  <div class="hl-marketing-hero-content reveal">
    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-4 rounded-pill reveal" data-delay="0.1" style="background:rgba(34, 197, 94, 0.15); border:1px solid rgba(34, 197, 94, 0.3); color:var(--hl-green);">
      <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
      <span class="font-700 text-sm tracking-wide">Live in Nairobi</span>
    </div>

    <h1 class="reveal" data-delay="0.2" style="color:#fff; font-size:clamp(2.5rem, 6vw, 4.2rem);">
      Reach thousands of customers.<br>
      <span style="background:var(--hl-gradient); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Grow your business.</span>
    </h1>
    
    <p class="reveal" data-delay="0.3" style="color:var(--text-dimmer); font-size:1.15rem; max-width:600px; margin:1.5rem auto 2.5rem;">
      Whether you run a local electronics shop, offer plumbing services, or sell handmade crafts, HudumaLynk gives you the platform to thrive.
    </p>

    <div class="d-flex gap-3 justify-content-center flex-wrap reveal" data-delay="0.4">
      <a href="<?= Url::to(['/join-as-provider']) ?>" class="btn-hl-primary btn-lg px-5" style="font-size:1.1rem;">
        Start Selling Today
      </a>
      <a href="#plans" class="btn-white btn-lg">
        View Pricing
      </a>
    </div>
  </div>
</section>

<!-- ── STATS BELT ────────────────────────────────────────────── -->
<section style="background:var(--surface); border-bottom:1px solid var(--border); padding:3rem 1.5rem;">
  <div class="max-w-1280 mx-auto reveal d-flex flex-wrap justify-content-around text-center gap-4">
    <div>
      <div class="display-font font-800" style="font-size:2.5rem; color:var(--text-primary);">10k+</div>
      <div class="text-sm font-700 text-muted text-uppercase tracking-wider">Active Buyers</div>
    </div>
    <div>
      <div class="display-font font-800" style="font-size:2.5rem; color:var(--text-primary);">₦0</div>
      <div class="text-sm font-700 text-muted text-uppercase tracking-wider">Commission on basic</div>
    </div>
    <div>
      <div class="display-font font-800" style="font-size:2.5rem; color:var(--text-primary);">24/7</div>
      <div class="text-sm font-700 text-muted text-uppercase tracking-wider">Support</div>
    </div>
  </div>
</section>

<!-- ── FEATURES ──────────────────────────────────────────────── -->
<section class="hl-section">
  <div class="hl-section-header centered text-center reveal">
    <div class="text-xs font-800 text-uppercase mb-2" style="color:var(--hl-blue); letter-spacing:0.1em;">Why HudumaLynk?</div>
    <h2>Everything you need to sell online</h2>
    <p class="mx-auto mt-3">We handle the tech so you can focus on what you do best: providing excellent service and great products.</p>
  </div>

  <div class="row g-4 mt-4">
    <!-- Feature 1 -->
    <div class="col-md-4 reveal" data-delay="0.1">
      <div class="hl-feature-box">
        <div class="hl-feature-icon"><i class="bi bi-patch-check"></i></div>
        <h4 class="display-font mb-3">Verified Trust Badge</h4>
        <p class="text-muted text-sm">Stand out from the crowd. Our verification process gives customers the confidence to choose your business over competitors.</p>
      </div>
    </div>
    <!-- Feature 2 -->
    <div class="col-md-4 reveal" data-delay="0.2">
      <div class="hl-feature-box">
        <div class="hl-feature-icon" style="background:rgba(34,197,94,0.1); color:var(--hl-green);"><i class="bi bi-wallet2"></i></div>
        <h4 class="display-font mb-3">Direct M-Pesa Payments</h4>
        <p class="text-muted text-sm">Get paid instantly and securely. We integrate with M-Pesa so you never have to worry about cash handling or delayed transfers.</p>
      </div>
    </div>
    <!-- Feature 3 -->
    <div class="col-md-4 reveal" data-delay="0.3">
      <div class="hl-feature-box">
        <div class="hl-feature-icon" style="background:rgba(6,182,212,0.1); color:var(--hl-cyan);"><i class="bi bi-graph-up-arrow"></i></div>
        <h4 class="display-font mb-3">Simple Dashboard</h4>
        <p class="text-muted text-sm">Manage orders, update listings, and track your earnings through our easy-to-use provider dashboard built for mobile and desktop.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── TESTIMONIAL ───────────────────────────────────────────── -->
<section class="hl-section-sep" style="background:var(--surface-3); padding:5rem 1.5rem;">
  <div class="hl-section p-0">
    <div class="row align-items-center">
      <div class="col-md-5 mb-4 mb-md-0 reveal">
        <div style="font-size:4rem; color:var(--hl-blue); opacity:0.2; line-height:0; margin-top:2rem;">"</div>
        <h3 class="display-font mb-4" style="line-height:1.4;">HudumaLynk completely transformed my phone repair business. I went from relying on foot traffic to receiving orders across Nairobi daily.</h3>
        <div class="d-flex align-items-center gap-3">
          <div style="width:50px; height:50px; border-radius:50%; background:var(--hl-blue); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.2rem;">K</div>
          <div>
            <div class="font-700">Kevin Mwangi</div>
            <div class="text-xs text-muted">Phone Repair Specialist, CBD</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 offset-md-1 reveal" data-delay="0.2">
        <div style="border-radius:var(--radius-xl); overflow:hidden; box-shadow:var(--shadow-xl); border:1px solid var(--border);">
            <div style="aspect-ratio:4/3; background:var(--surface-dark); display:flex; align-items:center; justify-content:center; color:#fff;">
                <i class="bi bi-play-circle" style="font-size:4rem; color:rgba(255,255,255,0.8); cursor:pointer; transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></i>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── PRICING ────────────────────────────────────────────────── -->
<section id="plans" class="hl-section" style="padding:6rem 1.5rem;">
  <div class="hl-section-header centered text-center reveal">
    <div class="text-xs font-800 text-uppercase mb-2" style="color:var(--hl-blue); letter-spacing:0.1em;">Fair Pricing</div>
    <h2>Choose Your Plan</h2>
    <p class="mx-auto mt-3">Simple, transparent pricing. Upgrade or downgrade at any time.</p>
  </div>

  <div class="row g-4 mt-4 justify-content-center">
    <!-- Basic -->
    <div class="col-md-4 reveal" data-delay="0.1">
      <div class="hl-pricing-card">
        <h4 class="display-font">Basic</h4>
        <p class="text-muted text-sm">Includes a 14-day free trial.</p>
        <div class="hl-pricing-price">KES 1,000</div>
        <div class="text-muted text-xs">Per Month</div>
        
        <a href="<?= Url::to(['/join-as-provider', 'plan' => 'basic']) ?>" class="btn-hl-outline w-100 mt-4 text-center justify-content-center">Start Free Trial</a>
        
        <ul class="hl-pricing-features">
          <li><i class="bi bi-check-circle-fill"></i> 5 Products + 3 Services</li>
          <li><i class="bi bi-check-circle-fill"></i> Standard Search Visibility</li>
          <li><i class="bi bi-check-circle-fill"></i> M-Pesa Payments</li>
          <li style="opacity:0.4;"><i class="bi bi-x-circle" style="color:inherit;"></i> Verified Badge</li>
        </ul>
      </div>
    </div>

    <!-- Professional -->
    <div class="col-md-4 reveal" data-delay="0.2">
      <div class="hl-pricing-card popular" style="background:var(--surface-dark); color:#fff; border-color:var(--hl-blue);">
        <h4 class="display-font" style="color:#fff;">Professional</h4>
        <p class="text-sm" style="color:var(--text-dimmer);">For growing small businesses.</p>
        <div class="hl-pricing-price" style="color:#fff;">KES 2,500</div>
        <div class="text-xs" style="color:var(--text-dimmer);">Per Month</div>
        
        <a href="<?= Url::to(['/join-as-provider', 'plan' => 'pro']) ?>" class="btn-hl-primary w-100 mt-4 text-center justify-content-center">Go Professional</a>
        
        <ul class="hl-pricing-features">
          <li style="color:#fff;"><i class="bi bi-check-circle-fill"></i> 20 Products + 10 Services</li>
          <li style="color:#fff;"><i class="bi bi-check-circle-fill"></i> 1 Featured Slot</li>
          <li style="color:#fff;"><i class="bi bi-check-circle-fill"></i> Verified Trust Badge</li>
          <li style="color:#fff;"><i class="bi bi-check-circle-fill"></i> Priority Listing</li>
        </ul>
      </div>
    </div>

    <!-- Premium -->
    <div class="col-md-4 reveal" data-delay="0.3">
      <div class="hl-pricing-card">
        <h4 class="display-font">Premium</h4>
        <p class="text-muted text-sm">For established shops & agencies.</p>
        <div class="hl-pricing-price">KES 5,000</div>
        <div class="text-muted text-xs">Per Month</div>
        
        <a href="<?= Url::to(['/join-as-provider', 'plan' => 'premium']) ?>" class="btn-hl-outline w-100 mt-4 text-center justify-content-center">Go Premium</a>
        
        <ul class="hl-pricing-features">
          <li><i class="bi bi-check-circle-fill"></i> Unlimited Listings</li>
          <li><i class="bi bi-check-circle-fill"></i> 3 Featured Homepage Slots</li>
          <li><i class="bi bi-check-circle-fill"></i> Verified Trust Badge</li>
          <li><i class="bi bi-check-circle-fill"></i> Priority Support</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ── FINAL CTA ──────────────────────────────────────────────── -->
<section class="reveal" style="text-align:center; padding:5rem 1.5rem;">
  <h2 class="display-font mb-4">Ready to boost your sales?</h2>
  <a href="<?= Url::to(['/join-as-provider']) ?>" class="btn-hl-primary btn-lg px-5 shadow-lg" style="transform:scale(1.05);">
    Create Your Free Account
  </a>
</section>
