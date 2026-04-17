<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="hl-card text-center" style="padding:4rem 2rem;max-width:500px;margin:2rem auto;">
  <i class="bi bi-hourglass-split" style="font-size:3.5rem;color:var(--warning);margin-bottom:1rem;display:block;"></i>
  <h3 style="margin-bottom:0.5rem;">Account Under Review</h3>
  <p style="color:var(--text-secondary);margin-bottom:1.5rem;">
    Your provider account is currently pending approval by our team.
    We review all applications within 24 hours to ensure quality on HudumaLynk.
  </p>
  <div class="hl-alert hl-alert-info d-inline-flex" style="text-align:left;font-size:0.85rem;">
    <i class="bi bi-info-circle"></i>
    <span>You'll receive an SMS and email notification as soon as your account is approved.</span>
  </div>
  <div style="margin-top:2rem;">
    <a href="<?= Yii::$app->params['frontendUrl'] ?>" class="btn-admin-outline">Return to Home</a>
  </div>
</div>
