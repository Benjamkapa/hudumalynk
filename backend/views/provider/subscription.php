<?php
/** @var common\models\Provider $provider */
/** @var common\models\SubscriptionPlan[] $plans */
/** @var common\models\Subscription|null $current */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h3 style="margin:0;">My Subscription</h3>
    <p style="color:var(--text-muted);font-size:0.875rem;margin:0;">Manage your billing and listing limits</p>
  </div>
</div>

<?php if ($current): ?>
<div class="hl-card mb-5" style="border:1px solid <?= $current->isExpiringSoon() ? 'var(--warning)' : 'var(--hl-green)' ?>;border-left:4px solid <?= $current->isExpiringSoon() ? 'var(--warning)' : 'var(--hl-green)' ?>;">
  <div class="hl-card-body d-flex justify-content-between align-items-center flex-wrap gap-4" style="padding:1.5rem 2rem;">
    <div>
      <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:0.25rem;">Current Plan</div>
      <h4 style="margin:0;font-weight:800;color:var(--hl-blue);"><?= Html::encode($current->plan->name) ?></h4>
      <div style="font-size:0.85rem;color:var(--text-secondary);margin-top:0.25rem;">
        Valid until <?= Yii::$app->formatter->asDate($current->end_date, 'php:d M Y') ?>
        <?php if ($current->isExpiringSoon()): ?>
          <strong style="color:var(--warning);">(Expires in <?= $current->daysRemaining() ?> days)</strong>
        <?php else: ?>
          <span style="color:var(--text-muted);">(<?= $current->daysRemaining() ?> days remaining)</span>
        <?php endif; ?>
      </div>
    </div>
    <div>
      <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Usage Limits</div>
      <div class="d-flex gap-4">
        <div><strong><?= $current->plan->hasUnlimitedProducts() ? '∞' : $current->plan->max_products ?></strong> Products</div>
        <div><strong><?= $current->plan->hasUnlimitedServices() ? '∞' : $current->plan->max_services ?></strong> Services</div>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
<div class="hl-alert hl-alert-danger mb-4">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <span>You do not have an active subscription. Customers cannot see your listings. Please subscribe to a plan to start selling.</span>
</div>
<?php endif; ?>

<h4 style="margin-bottom:1.5rem;">Choose a Plan</h4>
<div class="row g-4">
  <?php foreach ($plans as $plan): ?>
  <div class="col-lg-4 col-md-6">
    <div class="hl-card h-100 d-flex flex-column" style="border:2px solid <?= $plan->is_popular ? 'var(--hl-blue)' : 'var(--border)' ?>;position:relative;">
      <?php if ($plan->is_popular): ?>
        <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--hl-blue);color:#fff;padding:0.2rem 1rem;border-radius:var(--radius-pill);font-size:0.7rem;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;">Most Popular</div>
      <?php endif; ?>

      <div style="padding:2rem 1.5rem;text-align:center;border-bottom:1px solid var(--border);flex:1;">
        <h4 style="margin-bottom:0.5rem;"><?= Html::encode($plan->name) ?></h4>
        <div style="font-size:1.75rem;font-weight:800;color:var(--hl-blue);margin-bottom:1rem;">
          KES <?= number_format($plan->price_kes, 0) ?>
          <span style="font-size:0.85rem;font-weight:500;color:var(--text-muted);">/mo</span>
        </div>
        <p style="font-size:0.85rem;color:var(--text-secondary);min-height:40px;"><?= Html::encode($plan->description) ?></p>

        <ul style="list-style:none;text-align:left;padding:0;margin:1.5rem 0 0;font-size:0.875rem;display:flex;flex-direction:column;gap:0.75rem;">
          <li><i class="bi bi-check-circle-fill" style="color:var(--hl-green);margin-right:0.5rem;"></i> <?= $plan->hasUnlimitedProducts() ? 'Unlimited' : $plan->max_products ?> Products</li>
          <li><i class="bi bi-check-circle-fill" style="color:var(--hl-green);margin-right:0.5rem;"></i> <?= $plan->hasUnlimitedServices() ? 'Unlimited' : $plan->max_services ?> Services</li>
          <li><i class="bi bi-check-circle-fill" style="color:var(--hl-green);margin-right:0.5rem;"></i> <?= $plan->featured_slots ?> Featured Slots</li>
          <li><i class="bi bi-check-circle-fill" style="color:var(--hl-green);margin-right:0.5rem;"></i> Verified Provider Badge</li>
        </ul>
      </div>

      <div style="padding:1.5rem;">
        <form action="<?= Url::to(['/provider/subscribe-plan', 'planId' => $plan->id]) ?>" method="post">
          <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
          <?php if ($current && $current->plan_id === $plan->id): ?>
            <button type="submit" class="btn-admin-outline w-100" style="justify-content:center;">Renew Plan</button>
          <?php else: ?>
            <button type="submit" class="btn-admin-primary w-100" style="justify-content:center;">Subscribe via M-Pesa</button>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>