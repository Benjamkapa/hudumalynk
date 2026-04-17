<?php
/** @var yii\web\View $this */
/** @var string $token */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="hl-auth-wrap">
  <div class="hl-auth-card">
    <div class="hl-auth-header">
      <img src="/img/auth-logo.png" alt="HudumaLynk" style="height: 56px; width: auto; display: block; margin: 0 auto 1.25rem auto;" onerror="this.style.display='none'">
      <h2>Set New Password</h2>
      <p>Enter your new password below.</p>
    </div>
    <div class="hl-auth-body">

      <form method="post" novalidate>
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">

        <div class="hl-form-group">
          <label class="hl-label">New Password <span style="color:var(--text-muted);font-weight:400;">(min. 8 characters)</span></label>
          <div class="hl-input-icon">
            <i class="bi bi-lock"></i>
            <input type="password" name="password" class="hl-input" id="rp-password" placeholder="Enter new password" required autofocus>
          </div>
        </div>

        <div class="hl-form-group">
          <label class="hl-label">Confirm New Password</label>
          <div class="hl-input-icon">
            <i class="bi bi-lock-fill"></i>
            <input type="password" name="password_confirm" class="hl-input" id="rp-confirm" placeholder="Repeat new password" required>
          </div>
        </div>

        <button type="submit" class="btn-hl-primary w-100" style="justify-content:center;padding:0.8rem;font-size:1rem;">
          <i class="bi bi-key-fill"></i> Update Password
        </button>
      </form>
    </div>
    <div class="hl-auth-footer">
      <a href="<?= Url::to(['/login']) ?>">Back to sign in</a>
    </div>
  </div>
</div>