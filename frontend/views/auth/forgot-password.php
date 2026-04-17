<?php
/** @var yii\web\View $this */
/** @var string $email */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="hl-auth-wrap" style="padding-top:calc(var(--navbar-h) + 2rem);">
  <div class="hl-auth-card">
    <div class="hl-auth-header">
      <div style="width:60px;height:60px;background:var(--hl-bg);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
        <i class="bi bi-key" style="font-size:1.8rem;color:var(--hl-blue);"></i>
      </div>
      <h2>Forgot Password?</h2>
      <p>No worries, we'll send you reset instructions.</p>
    </div>
    <div class="hl-auth-body">
      <?= Html::beginForm(['/auth/forgot-password'], 'post', ['class' => 'hl-form']) ?>
      
      <div class="hl-form-group">
        <label class="hl-label">Email Address</label>
        <div class="hl-input-icon">
          <i class="bi bi-envelope"></i>
          <?= Html::textInput('email', $email, [
            'class' => 'hl-input',
            'placeholder' => 'Enter your registered email',
            'required' => true,
            'type' => 'email'
          ]) ?>
        </div>
      </div>

      <?= Html::submitButton('<i class="bi bi-send-fill"></i> Reset Password', [
        'class' => 'btn-hl-primary w-100',
        'style' => 'justify-content:center;padding:0.8rem;font-size:1rem;'
      ]) ?>

      <?= Html::endForm() ?>
    </div>
    <div class="hl-auth-footer">
      <a href="<?= Url::to(['/login']) ?>"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </div>
  </div>
</div>
