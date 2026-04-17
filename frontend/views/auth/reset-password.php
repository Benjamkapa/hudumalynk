<?php
/** @var yii\web\View $this */
/** @var string $token */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="hl-auth-wrap" style="padding-top:calc(var(--navbar-h) + 2rem);">
  <div class="hl-auth-card">
    <div class="hl-auth-header">
      <div style="width:60px;height:60px;background:var(--hl-bg);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
        <i class="bi bi-shield-lock" style="font-size:1.8rem;color:var(--hl-blue);"></i>
      </div>
      <h2>Set New Password</h2>
      <p>Please enter your new secure password.</p>
    </div>
    <div class="hl-auth-body">
      <?= Html::beginForm(['/auth/reset-password', 'token' => $token], 'post', ['class' => 'hl-form']) ?>
      
      <div class="hl-form-group">
        <label class="hl-label">New Password</label>
        <div class="hl-input-icon">
          <i class="bi bi-lock"></i>
          <?= Html::passwordInput('password', '', [
            'class' => 'hl-input',
            'placeholder' => 'Minimum 8 characters',
            'required' => true,
            'minlength' => 8
          ]) ?>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Confirm Password</label>
        <div class="hl-input-icon">
          <i class="bi bi-lock-check"></i>
          <?= Html::passwordInput('password_confirm', '', [
            'class' => 'hl-input',
            'placeholder' => 'Repeat your new password',
            'required' => true
          ]) ?>
        </div>
      </div>

      <?= Html::submitButton('<i class="bi bi-check2-circle"></i> Save Password', [
        'class' => 'btn-hl-primary w-100',
        'style' => 'justify-content:center;padding:0.8rem;font-size:1rem;'
      ]) ?>

      <?= Html::endForm() ?>
    </div>
    <div class="hl-auth-footer">
      Remembered your password? <a href="<?= Url::to(['/login']) ?>">Sign In</a>
    </div>
  </div>
</div>
